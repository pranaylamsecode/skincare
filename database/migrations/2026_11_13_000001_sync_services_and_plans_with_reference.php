<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\Service;
use App\Models\Concern;
use App\Models\Review;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update or create the Glow 3-Visit Plan (₹2,799 / ₹933 per visit)
        $plan = Plan::where('title', 'like', '%3-Visit%')
            ->orWhere('title', 'like', '%Glow%Plan%')
            ->orWhere('price', 3299)
            ->first();

        if ($plan) {
            $plan->update([
                'title' => 'Glow 3-Visit Plan',
                'tag' => 'Launch plan',
                'price' => 2799,
                'price_subtext' => '₹933 per visit',
                'description' => 'Three Glow Facials with the same specialist, spaced to suit your skin. Valid 90 days.',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]);
        } else {
            Plan::create([
                'title' => 'Glow 3-Visit Plan',
                'tag' => 'Launch plan',
                'price' => 2799,
                'price_subtext' => '₹933 per visit',
                'description' => 'Three Glow Facials with the same specialist, spaced to suit your skin. Valid 90 days.',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]);
        }

        // Deactivate "coming soon" memberships
        Plan::where('title', 'like', '%Memberships%')->update(['is_active' => false]);

        // 2. Sync services to match client reference HTML exactly
        // Cleanse
        $cleanse = Service::where('slug', 'fmz-glow-cleanse')
            ->orWhere('title', 'like', '%Cleanse%')
            ->first();
        if ($cleanse) {
            $cleanse->update([
                'title' => 'FMZ Glow Cleanse',
                'tag' => 'A gentle start',
                'tag_style' => 'normal',
                'description' => 'Deep cleansing and hydration with the Nu Skin LumiSpa iO.',
                'features' => [
                    'Skin suitability check',
                    'LumiSpa iO cleanse',
                    'Moisturising finish',
                    'Simple aftercare tips',
                ],
                'price' => 799,
                'original_price' => null,
                'is_featured' => false,
                'sort_order' => 1,
            ]);
        }

        // Facial
        $facial = Service::where('slug', 'fmz-glow-facial')
            ->orWhere('slug', 'fmz-galvanic-glow-facial')
            ->orWhere('title', 'like', '%Facial%')
            ->first();
        if ($facial) {
            $facial->update([
                'title' => 'FMZ Glow Facial',
                'slug' => 'fmz-glow-facial',
                'tag' => 'Launch offer',
                'tag_style' => 'tan',
                'description' => 'Our signature facial with the Nu Skin Galvanic Spa and selected Nu Skin products.',
                'features' => [
                    'Skin consultation',
                    'Galvanic Spa facial treatment',
                    'Leaves skin soft and refreshed',
                    'Home-care tips',
                ],
                'price' => 999,
                'original_price' => 1199,
                'is_featured' => true,
                'sort_order' => 2,
            ]);
        }

        // Ritual
        $ritual = Service::where('slug', 'fmz-radiance-ritual')
            ->orWhere('slug', 'fmz-prysm-io-radiance-ritual')
            ->orWhere('title', 'like', '%Ritual%')
            ->first();
        if ($ritual) {
            $ritual->update([
                'title' => 'FMZ Radiance Ritual',
                'slug' => 'fmz-radiance-ritual',
                'tag' => 'Most complete',
                'tag_style' => 'dark',
                'description' => 'The Glow Facial plus a 15-second Prysm iO antioxidant scan, explained by your specialist.',
                'features' => [
                    'Extended consultation',
                    'Galvanic Spa facial',
                    'Prysm iO antioxidant score',
                    'Personal skincare and food tips',
                ],
                'price' => 1699,
                'original_price' => null,
                'is_featured' => false,
                'sort_order' => 3,
            ]);
        }

        // 3. Sync Concerns to exactly 4 cards matching reference HTML
        Concern::where('quote', 'like', '%event%')->orWhere('quote', 'like', '%routine%')->delete();
        
        $refConcerns = [
            ['quote' => '“My skin looks dull and tired”', 'solution' => 'The Glow Facial brings back a fresh, bright look.', 'link_text' => 'Book the Glow Facial', 'link_target' => '#book', 'is_dark' => false, 'sort_order' => 1],
            ['quote' => '“I want a gentle first step”', 'solution' => 'Start with the Glow Cleanse: cleansing, hydration and advice.', 'link_text' => 'Book the Glow Cleanse', 'link_target' => '#book', 'is_dark' => false, 'sort_order' => 2],
            ['quote' => '“I deserve a proper pamper”', 'solution' => 'The Radiance Ritual is our most complete visit.', 'link_text' => 'Book the Radiance Ritual', 'link_target' => '#book', 'is_dark' => false, 'sort_order' => 3],
            ['quote' => '“I’m not sure what my skin needs”', 'solution' => 'Chat with us first. It’s free, with no obligation.', 'link_text' => 'Chat on WhatsApp', 'link_target' => 'whatsapp', 'is_dark' => true, 'sort_order' => 4],
        ];

        foreach ($refConcerns as $rc) {
            Concern::updateOrCreate(['quote' => $rc['quote']], $rc);
        }

        // 4. Update Priya & Neha result images for Before/After Slider
        Review::where('client_name', 'like', '%Priya%')->update([
            'image_before' => 'images/priya-before.jpg',
            'image_after' => 'images/priya-after.jpg',
        ]);
        Review::where('client_name', 'like', '%Neha%')->update([
            'image_before' => 'images/neha-before.jpg',
            'image_after' => 'images/neha-after.jpg',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed
    }
};

