<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class AboutPageSeeder extends Seeder
{
    public function run(): void
    {
        $about = Page::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'template' => 'about',
                'seo_title' => 'About Global Value Thesis',
                'seo_description' => 'Investment-first real estate advisory built on research, evidence, and developer vetting.',
                'status' => 'published',
            ],
        );

        collect($this->sections())->each(function (array $section, int $index) use ($about): void {
            PageSection::updateOrCreate(
                ['page_id' => $about->id, 'key' => $section['key']],
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
                'eyebrow' => 'About Us',
                'heading' => 'Investment-first real estate advisory.',
                'body' => 'GVT exists for investors who require context before exposure. We think in cycles, capital flows, governance quality, and structural value.',
                'content' => [],
            ],
            [
                'key' => 'philosophy',
                'eyebrow' => 'Brand philosophy',
                'heading' => 'Context before exposure.',
                'body' => 'The standard does not move. The market does. Our role is to separate launch momentum from durable investment rationale.',
                'content' => [
                    'principles' => [
                        'We do not treat assets as launches.',
                        'We do not separate upside from risk.',
                        'We do not present developers without evidence.',
                    ],
                ],
            ],
            [
                'key' => 'thinking',
                'eyebrow' => 'How we think',
                'heading' => 'A research house before an opportunity desk.',
                'body' => 'Every position is evaluated through market timing, developer discipline, currency context, and portfolio role.',
                'content' => [
                    'items' => [
                        ['title' => 'Market cycle first', 'body' => 'We ask where capital is moving, where consensus is late, and which submarkets have structural support.'],
                        ['title' => 'Developer evidence', 'body' => 'If we cannot certify the developer, we do not present the asset.'],
                        ['title' => 'Portfolio fit', 'body' => 'A strong asset still needs the right sizing, entry window, liquidity expectation, and risk context.'],
                    ],
                ],
            ],
            [
                'key' => 'thesis_led',
                'eyebrow' => 'Why thesis-led matters',
                'heading' => 'Every allocation should explain itself.',
                'body' => 'A thesis creates discipline before capital moves: what must be true, what can go wrong, and where the position belongs in a broader portfolio.',
                'content' => [],
            ],
        ];
    }
}
