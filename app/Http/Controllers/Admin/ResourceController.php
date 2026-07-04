<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveResourceRequest;
use App\Http\Requests\Admin\UploadThesisImageRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Certification;
use App\Models\CertificationHistory;
use App\Models\ContactMessage;
use App\Models\Developer;
use App\Models\InvestmentThesis;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Position;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ResourceController extends Controller
{
    /**
     * @var array<string, array{model: class-string<Model>, permission: string, title: string, relations?: array<int, string>}>
     */
    private array $resources = [
        'pages' => ['model' => Page::class, 'permission' => 'manage pages', 'title' => 'Pages', 'relations' => ['sections']],
        'page-sections' => ['model' => PageSection::class, 'permission' => 'manage pages', 'title' => 'Page Sections', 'relations' => ['page:id,title,slug']],
        'theses' => ['model' => InvestmentThesis::class, 'permission' => 'manage theses', 'title' => 'Investment Theses', 'relations' => ['developer:id,name']],
        'positions' => ['model' => Position::class, 'permission' => 'manage positions', 'title' => 'Positions', 'relations' => ['developer:id,name', 'thesis:id,title']],
        'developers' => ['model' => Developer::class, 'permission' => 'manage developers', 'title' => 'Developers'],
        'certifications' => ['model' => Certification::class, 'permission' => 'manage certifications', 'title' => 'Certifications', 'relations' => ['developer:id,name']],
        'certification-history' => ['model' => CertificationHistory::class, 'permission' => 'manage certifications', 'title' => 'Certification History', 'relations' => ['developer:id,name']],
        'blog' => ['model' => BlogPost::class, 'permission' => 'manage blog', 'title' => 'Blog Posts', 'relations' => ['category:id,name', 'tags:id,name']],
        'categories' => ['model' => BlogCategory::class, 'permission' => 'manage blog', 'title' => 'Categories'],
        'tags' => ['model' => BlogTag::class, 'permission' => 'manage blog', 'title' => 'Tags'],
        'newsletter' => ['model' => NewsletterSubscriber::class, 'permission' => 'export subscribers', 'title' => 'Newsletter Subscribers'],
        'contact-messages' => ['model' => ContactMessage::class, 'permission' => 'view inquiries', 'title' => 'Contact Messages'],
        'users' => ['model' => User::class, 'permission' => 'manage users', 'title' => 'Users', 'relations' => ['roles:id,name']],
        'roles' => ['model' => Role::class, 'permission' => 'manage roles', 'title' => 'Roles', 'relations' => ['permissions:id,name']],
        'settings' => ['model' => WebsiteSetting::class, 'permission' => 'manage settings', 'title' => 'Website Settings'],
    ];

    public function index(Request $request, string $resource): Response
    {
        $config = $this->config($resource);
        abort_unless($request->user()?->can($config['permission']), 403);

        $query = $config['model']::query();

        if ($relations = $config['relations'] ?? []) {
            $query->with($relations);
        }

        if ($search = $request->string('search')->toString()) {
            $table = (new $config['model'])->getTable();
            $columns = collect(['name', 'title', 'project_name', 'email', 'key', 'subject'])
                ->filter(fn (string $column): bool => Schema::hasColumn($table, $column));

            if ($columns->isNotEmpty()) {
                $query->where(function ($builder) use ($columns, $search): void {
                    foreach ($columns as $column) {
                        $builder->orWhere($column, 'like', "%{$search}%");
                    }
                });
            }
        }

        return Inertia::render('admin/ResourceIndex', [
            'resource' => $resource,
            'title' => $config['title'],
            'items' => $query->latest('id')->paginate(15)->withQueryString(),
            'meta' => $this->meta(),
            'fields' => $this->fields($resource),
            'tableFields' => $this->tableFields($resource),
        ]);
    }

    public function store(SaveResourceRequest $request, string $resource): RedirectResponse
    {
        $config = $this->config($resource);
        abort_unless($request->user()?->can($config['permission']), 403);
        $rawPayload = $this->payload($request);

        if ($resource === 'users' && blank($rawPayload['password'] ?? null)) {
            $rawPayload['password'] = 'password';
        }

        $payload = $this->preparePayload($resource, $rawPayload);

        $model = $config['model']::query()->create($payload);
        $this->afterSave($resource, $model, $rawPayload);
        $this->storeFeaturedImage($request, $resource, $model);

        return back()->with('success', "{$config['title']} record created.");
    }

    public function update(SaveResourceRequest $request, string $resource, int $id): RedirectResponse
    {
        $config = $this->config($resource);
        abort_unless($request->user()?->can($config['permission']), 403);
        $model = $config['model']::query()->findOrFail($id);
        $model->update($this->preparePayload($resource, $this->payload($request)));
        $this->afterSave($resource, $model, $this->payload($request));
        $this->storeFeaturedImage($request, $resource, $model);

        return back()->with('success', "{$config['title']} record updated.");
    }

    public function destroy(Request $request, string $resource, int $id): RedirectResponse
    {
        $config = $this->config($resource);
        abort_unless($request->user()?->can($config['permission']), 403);
        $config['model']::query()->findOrFail($id)->delete();

        return back()->with('success', "{$config['title']} record deleted.");
    }

    public function uploadThesisImage(UploadThesisImageRequest $request, InvestmentThesis $thesis): RedirectResponse
    {
        if ($thesis->featured_image_path) {
            Storage::disk('public')->delete($thesis->featured_image_path);
        }

        $path = $request->file('featured_image')->store('theses', 'public');

        $thesis->update(['featured_image_path' => $path]);

        return back()->with('success', 'Thesis image updated.');
    }

    /**
     * @return array{model: class-string<Model>, permission: string, title: string, relations?: array<int, string>}
     */
    private function config(string $resource): array
    {
        abort_unless(array_key_exists($resource, $this->resources), 404);

        return $this->resources[$resource];
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(SaveResourceRequest $request): array
    {
        $payload = $request->input('payload');

        if (is_string($payload)) {
            $decoded = json_decode($payload, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($payload) ? $payload : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function preparePayload(string $resource, array $payload): array
    {
        $payload = collect($payload)
            ->map(fn (mixed $value): mixed => $value === '' ? null : $value)
            ->all();

        $payload = Arr::except($payload, ['id', 'uuid', 'created_at', 'updated_at', 'deleted_at', 'roles', 'permissions', 'role_id', 'tag_ids', 'permission_ids', 'featured_image']);

        if ($resource === 'users') {
            if (blank($payload['password'] ?? null)) {
                unset($payload['password']);
            } else {
                $payload['password'] = Hash::make($payload['password']);
            }
        }

        if ($resource === 'roles') {
            $payload['guard_name'] ??= 'web';
        }

        if (isset($payload['name']) && blank($payload['slug'] ?? null) && in_array($resource, ['developers', 'categories', 'tags'], true)) {
            $payload['slug'] = Str::slug($payload['name']);
        }

        if (isset($payload['title']) && blank($payload['slug'] ?? null)) {
            $payload['slug'] = Str::slug($payload['title']);
        }

        if (isset($payload['project_name']) && blank($payload['slug'] ?? null)) {
            $payload['slug'] = Str::slug($payload['project_name']);
        }

        return $payload;
    }

    private function afterSave(string $resource, Model $model, array $payload): void
    {
        if ($resource === 'users' && $model instanceof User && filled($payload['role_id'] ?? null)) {
            $role = Role::query()->find($payload['role_id']);

            if ($role instanceof Role) {
                $model->syncRoles([$role->name]);
            }
        }

        if ($resource === 'blog' && $model instanceof BlogPost) {
            $model->tags()->sync($payload['tag_ids'] ?? []);
        }

        if ($resource === 'roles' && $model instanceof Role) {
            $model->syncPermissions($payload['permission_ids'] ?? []);
        }
    }

    private function storeFeaturedImage(SaveResourceRequest $request, string $resource, Model $model): void
    {
        if (! $request->hasFile('featured_image')) {
            return;
        }

        if (! $model instanceof BlogPost || $resource !== 'blog') {
            return;
        }

        if ($model->featured_image_path) {
            Storage::disk('public')->delete($model->featured_image_path);
        }

        $model->update([
            'featured_image_path' => $request->file('featured_image')->store('blog', 'public'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function meta(): array
    {
        return [
            'developers' => Developer::query()->orderBy('name')->get(['id', 'name']),
            'theses' => InvestmentThesis::query()->orderBy('title')->get(['id', 'title']),
            'categories' => BlogCategory::query()->orderBy('name')->get(['id', 'name']),
            'tags' => BlogTag::query()->orderBy('name')->get(['id', 'name']),
            'users' => User::query()->orderBy('name')->get(['id', 'name', 'email']),
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
            'permissions' => Permission::query()->orderBy('name')->get(['id', 'name']),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fields(string $resource): array
    {
        $developerStatus = [
            ['value' => 'certified', 'label' => 'Certified'],
            ['value' => 'under_review', 'label' => 'Under Review'],
            ['value' => 'watchlist', 'label' => 'Watchlist'],
            ['value' => 'revoked', 'label' => 'Revoked'],
        ];

        $publishStatus = [
            ['value' => 'published', 'label' => 'Published'],
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'watching', 'label' => 'Watching'],
        ];

        return match ($resource) {
            'developers' => [
                $this->field('name', 'Developer name', 'text', required: true),
                $this->field('slug', 'Slug', 'text', help: 'Leave empty to generate automatically.'),
                $this->field('website_url', 'Website URL', 'url'),
                $this->field('headquarters', 'Headquarters', 'text'),
                $this->field('founded_year', 'Founded year', 'number'),
                $this->field('certification_status', 'Certification status', 'select', options: $developerStatus),
                $this->field('rating_score', 'Rating score', 'number'),
                $this->field('summary', 'Summary', 'textarea', required: true),
                $this->field('evaluation_summary', 'Evaluation summary', 'textarea'),
                $this->field('strengths', 'Strengths', 'list', help: 'One strength per line.'),
                $this->field('risk_flags', 'Risk flags', 'list', help: 'One risk flag per line.'),
                $this->field('certified_at', 'Certified at', 'datetime'),
                $this->field('published_at', 'Published at', 'datetime'),
            ],
            'theses' => [
                $this->field('developer_id', 'Related developer', 'relation', source: 'developers'),
                $this->field('author_id', 'Author', 'relation', source: 'users'),
                $this->field('title', 'Title', 'text', required: true),
                $this->field('slug', 'Slug', 'text', help: 'Leave empty to generate automatically.'),
                $this->field('market', 'Market', 'text', required: true),
                $this->field('city', 'City', 'text', required: true),
                $this->field('category', 'Category', 'text', required: true),
                $this->field('status', 'Status', 'select', options: $publishStatus),
                $this->field('positioning_window', 'Positioning window', 'text'),
                $this->field('executive_summary', 'Executive summary', 'textarea', required: true),
                $this->field('market_context', 'Market context', 'textarea', required: true),
                $this->field('asset_rationale', 'Asset rationale', 'textarea', required: true),
                $this->field('risk_notes', 'Risk notes', 'textarea', required: true),
                $this->field('seo_title', 'SEO title', 'text'),
                $this->field('seo_description', 'SEO description', 'textarea'),
                $this->field('published_at', 'Published at', 'datetime'),
            ],
            'positions' => [
                $this->field('developer_id', 'Developer', 'relation', source: 'developers', required: true),
                $this->field('investment_thesis_id', 'Related thesis', 'relation', source: 'theses'),
                $this->field('project_name', 'Project name', 'text', required: true),
                $this->field('slug', 'Slug', 'text', help: 'Leave empty to generate automatically.'),
                $this->field('location', 'Location', 'text', required: true),
                $this->field('city', 'City', 'text', required: true),
                $this->field('asset_type', 'Asset type', 'text', required: true),
                $this->field('risk_level', 'Risk level', 'select', options: [['value' => 'Low', 'label' => 'Low'], ['value' => 'Moderate', 'label' => 'Moderate'], ['value' => 'Measured', 'label' => 'Measured'], ['value' => 'Elevated', 'label' => 'Elevated']]),
                $this->field('certification_status', 'Certification status', 'select', options: [['value' => 'Certified', 'label' => 'Certified'], ['value' => 'Under Review', 'label' => 'Under Review'], ['value' => 'Watchlist', 'label' => 'Watchlist'], ['value' => 'Revoked', 'label' => 'Revoked']]),
                $this->field('expected_yield_range', 'Expected yield range', 'text'),
                $this->field('expected_appreciation_range', 'Expected appreciation range', 'text'),
                $this->field('thesis_summary', 'Thesis summary', 'textarea', required: true),
                $this->field('investment_rationale', 'Investment rationale', 'textarea', required: true),
                $this->field('location_intelligence', 'Location intelligence', 'textarea'),
                $this->field('financial_indicators', 'Financial indicators', 'keyValue', help: 'One item per line, for example: Liquidity depth: High'),
                $this->field('gallery', 'Gallery image paths', 'list', help: 'One image path per line.'),
                $this->field('documents', 'Documents', 'list', help: 'One document name per line.'),
                $this->field('published_at', 'Published at', 'datetime'),
            ],
            'certifications' => [
                $this->field('developer_id', 'Developer', 'relation', source: 'developers', required: true),
                $this->field('status', 'Status', 'select', options: $developerStatus),
                $this->field('score', 'Score', 'number'),
                $this->field('issued_at', 'Issued at', 'date'),
                $this->field('expires_at', 'Expires at', 'date'),
                $this->field('methodology_summary', 'Methodology summary', 'textarea', required: true),
                $this->field('criteria', 'Criteria', 'keyValue', help: 'One criterion per line, for example: Governance: 91'),
            ],
            'certification-history' => [
                $this->field('developer_id', 'Developer', 'relation', source: 'developers', required: true),
                $this->field('certification_id', 'Certification ID', 'number'),
                $this->field('from_status', 'From status', 'select', options: [['value' => '', 'label' => 'None'], ...$developerStatus]),
                $this->field('to_status', 'To status', 'select', options: $developerStatus),
                $this->field('event_type', 'Event type', 'text'),
                $this->field('rationale', 'Rationale', 'textarea', required: true),
                $this->field('effective_at', 'Effective at', 'datetime', required: true),
            ],
            'blog' => [
                $this->field('featured_image', 'Featured image', 'image', help: 'Shown on the public insights listing and article page.'),
                $this->field('blog_category_id', 'Category', 'relation', source: 'categories', required: true),
                $this->field('author_id', 'Author', 'relation', source: 'users'),
                $this->field('tag_ids', 'Tags', 'multiRelation', source: 'tags'),
                $this->field('title', 'Title', 'text', required: true),
                $this->field('slug', 'Slug', 'text', help: 'Leave empty to generate automatically.'),
                $this->field('excerpt', 'Excerpt', 'textarea', required: true),
                $this->field('body', 'Body', 'textarea', required: true),
                $this->field('status', 'Status', 'select', options: $publishStatus),
                $this->field('seo_title', 'SEO title', 'text'),
                $this->field('seo_description', 'SEO description', 'textarea'),
                $this->field('published_at', 'Published at', 'datetime'),
            ],
            'categories' => [
                $this->field('name', 'Name', 'text', required: true),
                $this->field('slug', 'Slug', 'text', help: 'Leave empty to generate automatically.'),
                $this->field('description', 'Description', 'textarea'),
            ],
            'tags' => [
                $this->field('name', 'Name', 'text', required: true),
                $this->field('slug', 'Slug', 'text', help: 'Leave empty to generate automatically.'),
            ],
            'newsletter' => [
                $this->field('email', 'Email', 'email', required: true),
                $this->field('name', 'Name', 'text'),
                $this->field('investor_type', 'Investor type', 'text'),
                $this->field('status', 'Status', 'select', options: [['value' => 'active', 'label' => 'Active'], ['value' => 'unsubscribed', 'label' => 'Unsubscribed']]),
                $this->field('subscribed_at', 'Subscribed at', 'datetime'),
            ],
            'contact-messages' => [
                $this->field('name', 'Name', 'text', required: true),
                $this->field('email', 'Email', 'email', required: true),
                $this->field('company', 'Company', 'text'),
                $this->field('inquiry_type', 'Inquiry type', 'select', options: [['value' => 'Investor inquiry', 'label' => 'Investor inquiry'], ['value' => 'Developer certification inquiry', 'label' => 'Developer certification inquiry'], ['value' => 'Media inquiry', 'label' => 'Media inquiry'], ['value' => 'General', 'label' => 'General']]),
                $this->field('subject', 'Subject', 'text', required: true),
                $this->field('message', 'Message', 'textarea', required: true),
                $this->field('status', 'Status', 'select', options: [['value' => 'new', 'label' => 'New'], ['value' => 'read', 'label' => 'Read'], ['value' => 'closed', 'label' => 'Closed']]),
                $this->field('read_at', 'Read at', 'datetime'),
            ],
            'pages' => [
                $this->field('title', 'Title', 'text', required: true),
                $this->field('slug', 'Slug', 'text', required: true),
                $this->field('template', 'Template', 'text'),
                $this->field('seo_title', 'SEO title', 'text'),
                $this->field('seo_description', 'SEO description', 'textarea'),
                $this->field('status', 'Status', 'select', options: $publishStatus),
            ],
            'users' => [
                $this->field('name', 'Name', 'text', required: true),
                $this->field('email', 'Email', 'email', required: true),
                $this->field('password', 'Password', 'password', help: 'Leave empty when editing to keep the current password.'),
                $this->field('role_id', 'Role', 'relation', source: 'roles'),
            ],
            'roles' => [
                $this->field('name', 'Role name', 'text', required: true),
                $this->field('permission_ids', 'Permissions', 'multiRelation', source: 'permissions'),
            ],
            default => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function field(string $name, string $label, string $type, ?string $source = null, array $options = [], bool $required = false, ?string $help = null): array
    {
        return compact('name', 'label', 'type', 'source', 'options', 'required', 'help');
    }

    /**
     * @return array<int, string>
     */
    private function tableFields(string $resource): array
    {
        return match ($resource) {
            'developers' => ['name', 'certification_status', 'rating_score', 'headquarters', 'published_at'],
            'theses' => ['title', 'market', 'city', 'category', 'status'],
            'positions' => ['project_name', 'city', 'asset_type', 'risk_level', 'certification_status'],
            'certifications' => ['developer', 'status', 'score', 'issued_at', 'expires_at'],
            'certification-history' => ['developer', 'to_status', 'event_type', 'effective_at'],
            'blog' => ['title', 'category', 'status', 'published_at'],
            'categories', 'tags' => ['name', 'slug'],
            'newsletter' => ['email', 'name', 'investor_type', 'status'],
            'contact-messages' => ['name', 'email', 'inquiry_type', 'status', 'subject'],
            'pages' => ['title', 'slug', 'template', 'status'],
            'users' => ['name', 'email', 'roles'],
            'roles' => ['name', 'permissions'],
            default => ['id'],
        };
    }
}
