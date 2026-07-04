<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view dashboard', 'manage users', 'manage roles', 'manage pages', 'manage theses',
            'manage positions', 'manage developers', 'manage certifications', 'manage blog',
            'manage settings', 'view inquiries', 'export subscribers', 'manage crm',
            'manage assigned clients',
        ];

        collect($permissions)->each(fn (string $permission) => Permission::findOrCreate($permission));

        $roles = [
            'Super Admin' => $permissions,
            'Admin' => array_diff($permissions, ['manage roles']),
            'Editor' => ['view dashboard', 'manage pages', 'manage blog', 'manage theses'],
            'Analyst' => ['view dashboard', 'manage theses', 'manage positions', 'manage developers', 'manage certifications'],
            'Sales' => ['view dashboard', 'manage assigned clients'],
            'Viewer' => ['view dashboard'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            Role::findOrCreate($roleName)->syncPermissions($rolePermissions);
        }

        $users = collect([
            ['name' => 'Nadine Farouk', 'email' => 'admin@gvt.test', 'role' => 'Super Admin'],
            ['name' => 'Omar Selim', 'email' => 'analyst@gvt.test', 'role' => 'Analyst'],
            ['name' => 'Maya Khalil', 'email' => 'editor@gvt.test', 'role' => 'Editor'],
            ['name' => 'Youssef Adel', 'email' => 'sales@gvt.test', 'role' => 'Sales'],
        ])->map(function (array $user): User {
            $model = User::updateOrCreate(
                ['email' => $user['email']],
                ['name' => $user['name'], 'password' => Hash::make('password'), 'email_verified_at' => now()]
            );
            $model->assignRole($user['role']);

            return $model;
        });

        $developers = collect([
            ['name' => 'North Coast Urban Partners', 'status' => 'Certified', 'score' => 91, 'hq' => 'Cairo, Egypt'],
            ['name' => 'Delta Living Developments', 'status' => 'Under Review', 'score' => 74, 'hq' => 'New Cairo, Egypt'],
            ['name' => 'Red Sea Asset Company', 'status' => 'Certified', 'score' => 88, 'hq' => 'Hurghada, Egypt'],
            ['name' => 'Cairo Ring Communities', 'status' => 'Watchlist', 'score' => 58, 'hq' => '6th of October, Egypt'],
        ])->map(function (array $developer): Developer {
            $statusSlug = Str::of($developer['status'])->lower()->replace(' ', '_')->toString();

            $model = Developer::create([
                'name' => $developer['name'],
                'slug' => Str::slug($developer['name']),
                'headquarters' => $developer['hq'],
                'founded_year' => fake()->numberBetween(2008, 2019),
                'certification_status' => $statusSlug,
                'rating_score' => $developer['score'],
                'summary' => 'A developer evaluated through delivery history, capital discipline, governance visibility, and post-handover asset performance.',
                'evaluation_summary' => 'Certification reflects documented execution, transparent ownership structure, and evidence of sustained buyer service after delivery.',
                'strengths' => ['Delivery discipline', 'Transparent escrow structure', 'Documented operating partners'],
                'risk_flags' => $developer['status'] === 'Watchlist' ? ['Delayed infrastructure package', 'Governance clarification required'] : ['Market absorption should be monitored'],
                'certified_at' => $developer['status'] === 'Certified' ? now()->subMonths(4) : null,
                'published_at' => now()->subMonths(6),
            ]);

            $certification = Certification::create([
                'developer_id' => $model->id,
                'status' => $statusSlug,
                'score' => $developer['score'],
                'issued_at' => now()->subMonths(4),
                'expires_at' => now()->addYear(),
                'methodology_summary' => 'Assessment covers governance, financial resilience, delivery record, legal clarity, buyer protection, and operating partner quality.',
                'criteria' => ['governance' => $developer['score'], 'delivery' => $developer['score'] - 3, 'financial_resilience' => $developer['score'] - 6],
            ]);

            CertificationHistory::create([
                'developer_id' => $model->id,
                'certification_id' => $certification->id,
                'from_status' => null,
                'to_status' => $statusSlug,
                'event_type' => $developer['status'] === 'Watchlist' ? 'watchlist_entry' : 'certification_review',
                'rationale' => 'Public status assigned after evidence review and investment committee discussion.',
                'effective_at' => now()->subMonths(4),
            ]);

            return $model;
        });

        $thesisData = [
            ['title' => 'North Coast Yield Compression Before Institutional Hospitality', 'market' => 'Egypt', 'city' => 'North Coast', 'category' => 'Hospitality', 'window' => '2026-2028'],
            ['title' => 'New Cairo Education Corridor Defensive Rental Thesis', 'market' => 'Egypt', 'city' => 'New Cairo', 'category' => 'Residential', 'window' => '2026-2027'],
            ['title' => 'Red Sea Branded Residences and Hard Currency Demand', 'market' => 'Egypt', 'city' => 'Red Sea', 'category' => 'Branded Residences', 'window' => '2026-2029'],
            ['title' => 'West Cairo Infrastructure Catch-Up and Family Formation', 'market' => 'Egypt', 'city' => 'West Cairo', 'category' => 'Mixed Use', 'window' => '2026-2028'],
        ];

        $theses = collect($thesisData)->map(fn (array $thesis, int $index): InvestmentThesis => InvestmentThesis::create([
            'developer_id' => $developers[$index % $developers->count()]->id,
            'author_id' => $users->first()->id,
            'title' => $thesis['title'],
            'slug' => Str::slug($thesis['title']),
            'market' => $thesis['market'],
            'city' => $thesis['city'],
            'category' => $thesis['category'],
            'status' => 'published',
            'positioning_window' => $thesis['window'],
            'featured_image_path' => ['theses/north-coast-thesis.png', 'theses/new-cairo-thesis.png', 'theses/gcc-capital-thesis.png', null][$index] ?? null,
            'executive_summary' => 'The position is supported by structural demand, constrained credible supply, and developer execution evidence.',
            'market_context' => 'Currency repricing, household allocation behavior, and infrastructure sequencing are changing the relative value of specific real estate submarkets.',
            'asset_rationale' => 'The asset class offers a clear use case, defensible exit audience, and documented operating assumptions.',
            'risk_notes' => 'Absorption pace, delivery timing, and liquidity windows require active monitoring.',
            'seo_title' => $thesis['title'],
            'seo_description' => 'A GVT investment thesis built on evidence, risk context, and market-cycle positioning.',
            'published_at' => now()->subDays($index * 12),
        ]));

        $positions = [
            ['project_name' => 'Marassi Income Suites Allocation', 'city' => 'North Coast', 'type' => 'Serviced Residence', 'risk' => 'Measured', 'yield' => '8-11%', 'appreciation' => '18-26%'],
            ['project_name' => 'Fifth Settlement Education Belt Homes', 'city' => 'New Cairo', 'type' => 'Residential', 'risk' => 'Low', 'yield' => '6-8%', 'appreciation' => '12-18%'],
            ['project_name' => 'Red Sea Managed Residence Basket', 'city' => 'Red Sea', 'type' => 'Branded Residence', 'risk' => 'Moderate', 'yield' => '7-10%', 'appreciation' => '20-30%'],
            ['project_name' => 'West Cairo Mixed-Use Early Position', 'city' => 'West Cairo', 'type' => 'Mixed Use', 'risk' => 'Elevated', 'yield' => '5-7%', 'appreciation' => '22-34%'],
        ];

        collect($positions)->each(function (array $position, int $index) use ($developers, $theses): void {
            Position::create([
                'developer_id' => $developers[$index % $developers->count()]->id,
                'investment_thesis_id' => $theses[$index % $theses->count()]->id,
                'project_name' => $position['project_name'],
                'slug' => Str::slug($position['project_name']),
                'location' => $position['city'].', Egypt',
                'city' => $position['city'],
                'asset_type' => $position['type'],
                'risk_level' => $position['risk'],
                'certification_status' => 'Certified',
                'expected_yield_range' => $position['yield'],
                'expected_appreciation_range' => $position['appreciation'],
                'thesis_summary' => 'A position selected for structural value rather than launch momentum.',
                'investment_rationale' => 'The allocation is based on demand durability, developer certification, liquidity depth, and an identifiable exit buyer.',
                'location_intelligence' => 'Infrastructure timing, community services, and comparable absorption support the positioning window.',
                'financial_indicators' => ['entry_price_index' => 100 + ($index * 7), 'liquidity_depth' => 'High', 'currency_sensitivity' => 'Medium'],
                'gallery' => ['/images/gvt-position-'.($index + 1).'.jpg'],
                'documents' => [['name' => 'Investment memo', 'status' => 'Available after inquiry']],
                'published_at' => now()->subDays($index * 9),
            ]);
        });

        $categories = collect(['Market Cycles', 'Developer Vetting', 'Currency', 'Portfolio Allocation'])
            ->map(fn (string $name): BlogCategory => BlogCategory::create(['name' => $name, 'slug' => Str::slug($name), 'description' => 'Research notes for '.$name.'.']));
        $tags = collect(['Research', 'Evidence', 'Risk', 'Certification', 'Egypt'])->map(fn (string $name): BlogTag => BlogTag::create(['name' => $name, 'slug' => Str::slug($name)]));

        for ($i = 0; $i < 6; $i++) {
            $post = BlogPost::create([
                'blog_category_id' => $categories[$i % $categories->count()]->id,
                'author_id' => $users[$i % $users->count()]->id,
                'title' => ['Position Ahead of the Consensus', 'Research Speaks. Returns Follow.', 'Investors Who Think in Cycles, Not Launches.', 'The Standard Does Not Move. The Market Does.', 'Built on Evidence. Built to Last.', 'Developer Certification as Downside Control'][$i],
                'slug' => Str::slug(['Position Ahead of the Consensus', 'Research Speaks Returns Follow', 'Investors Who Think in Cycles Not Launches', 'The Standard Does Not Move The Market Does', 'Built on Evidence Built to Last', 'Developer Certification as Downside Control'][$i]),
                'excerpt' => 'Short evidence-led commentary for investors allocating capital across Egyptian real estate cycles.',
                'body' => 'Every investment needs a thesis. This note examines the evidence, the market cycle, and the risks that should shape allocation before an asset is presented.',
                'status' => 'published',
                'seo_title' => 'GVT Insight',
                'seo_description' => 'Evidence-led real estate investment intelligence.',
                'published_at' => now()->subDays($i * 5),
            ]);
            $post->tags()->sync($tags->random(3)->pluck('id'));
        }

        collect(['hnwi@example.com', 'diaspora@example.com', 'familyoffice@example.com'])->each(fn (string $email) => NewsletterSubscriber::create([
            'email' => $email,
            'name' => fake()->name(),
            'investor_type' => fake()->randomElement(['Egyptian HNWI', 'Diaspora Investor', 'Developer']),
            'status' => 'active',
            'subscribed_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]));

        ContactMessage::create([
            'name' => 'Leila Morgan',
            'email' => 'leila@example.com',
            'company' => 'Family Office',
            'inquiry_type' => 'Investor inquiry',
            'subject' => 'Portfolio allocation discussion',
            'message' => 'We are reviewing Egyptian real estate exposure and would like to understand thesis-led positions.',
        ]);

        $home = Page::create(['title' => 'Home', 'slug' => 'home', 'seo_title' => 'Global Value Thesis', 'seo_description' => 'Every investment needs a thesis.']);
        collect([
            ['key' => 'hero', 'eyebrow' => 'Global Value Thesis', 'heading' => 'Every investment needs a thesis.', 'body' => 'We are a research and advisory house specializing in real estate investments and offshore opportunities.', 'content' => ['primary_cta' => 'Explore Theses', 'secondary_cta' => 'View Positions']],
            ['key' => 'value_strip', 'eyebrow' => 'Signals', 'heading' => 'Research driven. Global perspective. Capital intelligence. Integrity first.', 'body' => 'Data, insights, evidence. Local expertise, global reach. Access to offshore markets. Independent. Unbiased. Trusted.', 'content' => []],
            ['key' => 'approach', 'eyebrow' => 'Our investment approach', 'heading' => 'A structured view on value', 'body' => 'Our proprietary framework ensures every investment is backed by rigorous research, real-world data, and deep market knowledge.', 'content' => ['steps' => ['Research' => 'We identify structural shifts and emerging opportunities before the market.', 'Challenge' => 'We question assumptions and validate every opportunity.', 'Reprice' => 'We uncover mispriced assets and hidden value.', 'Position' => 'We build conviction-backed positions for long-term capital growth.']]],
            ['key' => 'market_intelligence', 'eyebrow' => 'Market intelligence', 'heading' => 'Real-time signals. Smarter decisions.', 'body' => 'A compact view of currency, absorption, repricing, sentiment, and offshore rates.', 'content' => ['signals' => [['label' => 'Egypt', 'title' => 'Currency Dynamics', 'value' => 'EGP', 'meta' => '30.85 USD', 'change' => '+2.5%'], ['label' => 'North Coast', 'title' => 'Capital Flow Index', 'value' => '72.4', 'meta' => 'High', 'change' => '+8.7%'], ['label' => 'Ras El Hekma', 'title' => 'Repricing Score', 'value' => '68.9', 'meta' => 'Rising', 'change' => '+4.1%'], ['label' => 'GCC Investors', 'title' => 'Sentiment Tracker', 'value' => 'Positive 64%', 'meta' => 'Allocation bias', 'change' => '+12%'], ['label' => 'Offshore Markets', 'title' => 'Interest Rate Outlook', 'value' => '4.25%', 'meta' => 'Stable', 'change' => '0.00%']]]],
            ['key' => 'theses', 'eyebrow' => 'Investment theses', 'heading' => 'Evidence. Conviction. Opportunity.', 'body' => 'Research speaks. Returns follow.', 'content' => []],
            ['key' => 'positions', 'eyebrow' => 'Curated investment opportunities', 'heading' => 'Premium properties. Global standards.', 'body' => 'Positions selected for structural value, certified developers, and risk-aware capital allocation.', 'content' => []],
            ['key' => 'founder', 'eyebrow' => 'Founder', 'heading' => 'Mohamed Salama', 'body' => 'We do not bring you properties. We bring you positions.', 'content' => ['bio' => 'With over a decade of experience in real estate and offshore investments, Mohamed Salama leads GVT with a commitment to integrity, intelligence, and long-term value creation.', 'stats' => [['value' => '10+', 'label' => 'Years of experience'], ['value' => '500+', 'label' => 'Clients served'], ['value' => '25+', 'label' => 'Countries covered'], ['value' => '$2B+', 'label' => 'Transactions advised']]]],
            ['key' => 'cta', 'eyebrow' => 'Contact', 'heading' => 'Let us build your next position', 'body' => 'Connect with our team to discuss tailored investment opportunities.', 'content' => []],
        ])->each(fn (array $section, int $index) => PageSection::create([...$section, 'page_id' => $home->id, 'sort_order' => $index + 1]));

        foreach (['about', 'methodology', 'certification'] as $pageSlug) {
            $page = Page::create(['title' => Str::headline($pageSlug), 'slug' => $pageSlug, 'seo_title' => 'GVT '.Str::headline($pageSlug), 'seo_description' => 'Global Value Thesis '.$pageSlug.' page.']);
            PageSection::create(['page_id' => $page->id, 'key' => 'hero', 'eyebrow' => 'Global Value Thesis', 'heading' => 'Every investment needs a thesis.', 'body' => 'We do not bring you properties. We bring you positions.', 'sort_order' => 1]);
        }

        WebsiteSetting::updateOrCreate(['key' => 'brand'], ['value' => [
            'name' => 'Global Value Thesis',
            'slogan' => 'Every investment needs a thesis.',
            'logo' => null,
            'social_links' => ['linkedin' => 'https://linkedin.com/company/global-value-thesis'],
            'seo' => ['title' => 'Global Value Thesis', 'description' => 'Institutional real estate investment advisory and developer certification.'],
            'contact' => ['email' => 'research@gvt.test', 'phone' => '+20 100 000 0000'],
        ]]);

        WebsiteSetting::updateOrCreate(['key' => 'site'], ['value' => WebsiteSetting::defaults()]);

        $this->call([
            HomePageSeeder::class,
            AboutPageSeeder::class,
            CrmSeeder::class,
        ]);
    }
}
