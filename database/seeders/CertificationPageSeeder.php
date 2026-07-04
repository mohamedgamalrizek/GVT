<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class CertificationPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::updateOrCreate(
            ['slug' => 'certification'],
            [
                'title' => 'Certification',
                'template' => 'certification',
                'seo_title' => 'GVT Certification Portal',
                'seo_description' => 'Transparent developer vetting, certification status, history, and risk context.',
                'status' => 'published',
            ],
        );

        collect($this->sections())->each(function (array $section, int $index) use ($page): void {
            PageSection::updateOrCreate(
                ['page_id' => $page->id, 'key' => $section['key']],
                [...$section, 'sort_order' => $index + 1],
            );
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sections(): array
    {
        return [
            [
                'key' => 'hero',
                'eyebrow' => 'Certification portal',
                'heading' => 'Transparent developer vetting.',
                'body' => 'If we cannot certify the developer, we do not present the asset. Status changes and watchlist entries remain publicly visible.',
                'content' => [
                    'primary_cta' => 'View certified developers',
                    'secondary_cta' => 'Review methodology',
                ],
            ],
            [
                'key' => 'methodology',
                'eyebrow' => 'Certification methodology',
                'heading' => 'Evidence before exposure.',
                'body' => 'The standard does not move. Developer certification is reviewed through governance, delivery record, financial resilience, legal clarity, buyer protection, and operating partner quality.',
                'content' => [
                    'steps' => [
                        'Governance' => 'Ownership clarity, decision control, and documented operating structure.',
                        'Delivery record' => 'Execution history, handover discipline, and infrastructure readiness.',
                        'Financial resilience' => 'Capital structure, escrow visibility, and stress tolerance.',
                        'Buyer protection' => 'Contract clarity, after-sale process, and service continuity.',
                    ],
                ],
            ],
        ];
    }
}
