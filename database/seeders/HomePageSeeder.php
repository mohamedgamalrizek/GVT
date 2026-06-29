<?php

namespace Database\Seeders;

use App\Models\InvestmentThesis;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        $home = Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'template' => 'home',
                'seo_title' => 'Global Value Thesis',
                'seo_description' => 'Every investment needs a thesis.',
                'status' => 'published',
            ],
        );

        collect($this->sections())->each(function (array $section, int $index) use ($home): void {
            PageSection::updateOrCreate(
                ['page_id' => $home->id, 'key' => $section['key']],
                [...$section, 'sort_order' => $index + 1],
            );
        });

        InvestmentThesis::query()
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(3)
            ->get()
            ->values()
            ->each(function (InvestmentThesis $thesis, int $index): void {
                $images = [
                    'theses/north-coast-thesis.png',
                    'theses/new-cairo-thesis.png',
                    'theses/gcc-capital-thesis.png',
                ];

                $thesis->update(['featured_image_path' => $images[$index]]);
            });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sections(): array
    {
        return [
            ['key' => 'hero', 'eyebrow' => 'Global Value Thesis', 'heading' => 'Every investment needs a thesis.', 'body' => 'We are a research and advisory house specializing in real estate investments and offshore opportunities.', 'content' => ['primary_cta' => 'Explore Theses', 'secondary_cta' => 'View Positions']],
            ['key' => 'value_strip', 'eyebrow' => 'Signals', 'heading' => 'Research driven. Global perspective. Capital intelligence. Integrity first.', 'body' => 'Data, insights, evidence. Local expertise, global reach. Access to offshore markets. Independent. Unbiased. Trusted.', 'content' => []],
            ['key' => 'approach', 'eyebrow' => 'Our investment approach', 'heading' => 'A structured view on value', 'body' => 'Our proprietary framework ensures every investment is backed by rigorous research, real-world data, and deep market knowledge.', 'content' => ['steps' => ['Research' => 'We identify structural shifts and emerging opportunities before the market.', 'Challenge' => 'We question assumptions and validate every opportunity.', 'Reprice' => 'We uncover mispriced assets and hidden value.', 'Position' => 'We build conviction-backed positions for long-term capital growth.']]],
            ['key' => 'market_intelligence', 'eyebrow' => 'Market intelligence', 'heading' => 'Real-time signals. Smarter decisions.', 'body' => 'A compact view of currency, absorption, repricing, sentiment, and offshore rates.', 'content' => ['signals' => [['label' => 'Egypt', 'title' => 'Currency Dynamics', 'value' => 'EGP', 'meta' => '30.85 USD', 'change' => '+2.5%'], ['label' => 'North Coast', 'title' => 'Capital Flow Index', 'value' => '72.4', 'meta' => 'High', 'change' => '+8.7%'], ['label' => 'Ras El Hekma', 'title' => 'Repricing Score', 'value' => '68.9', 'meta' => 'Rising', 'change' => '+4.1%'], ['label' => 'GCC Investors', 'title' => 'Sentiment Tracker', 'value' => 'Positive 64%', 'meta' => 'Allocation bias', 'change' => '+12%'], ['label' => 'Offshore Markets', 'title' => 'Interest Rate Outlook', 'value' => '4.25%', 'meta' => 'Stable', 'change' => '0.00%']]]],
            ['key' => 'theses', 'eyebrow' => 'Investment theses', 'heading' => 'Evidence. Conviction. Opportunity.', 'body' => 'Research speaks. Returns follow.', 'content' => []],
            ['key' => 'positions', 'eyebrow' => 'Curated investment opportunities', 'heading' => 'Premium properties. Global standards.', 'body' => 'Positions selected for structural value, certified developers, and risk-aware capital allocation.', 'content' => []],
            ['key' => 'founder', 'eyebrow' => 'Founder', 'heading' => 'Mohamed Salama', 'body' => 'We do not bring you properties. We bring you positions.', 'content' => ['bio' => 'With over a decade of experience in real estate and offshore investments, Mohamed Salama leads GVT with a commitment to integrity, intelligence, and long-term value creation.', 'stats' => [['value' => '10+', 'label' => 'Years of experience'], ['value' => '500+', 'label' => 'Clients served'], ['value' => '25+', 'label' => 'Countries covered'], ['value' => '$2B+', 'label' => 'Transactions advised']]]],
            ['key' => 'cta', 'eyebrow' => 'Contact', 'heading' => 'Let us build your next position', 'body' => 'Connect with our team to discuss tailored investment opportunities.', 'content' => []],
        ];
    }
}
