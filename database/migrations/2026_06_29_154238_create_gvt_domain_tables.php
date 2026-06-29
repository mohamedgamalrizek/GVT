<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('developers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->string('website_url')->nullable();
            $table->string('headquarters')->nullable();
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->string('certification_status')->default('under_review')->index();
            $table->unsignedTinyInteger('rating_score')->default(0)->index();
            $table->text('summary');
            $table->text('evaluation_summary')->nullable();
            $table->json('strengths')->nullable();
            $table->json('risk_flags')->nullable();
            $table->timestamp('certified_at')->nullable()->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->cascadeOnDelete();
            $table->string('status')->index();
            $table->unsignedTinyInteger('score')->default(0);
            $table->date('issued_at')->nullable()->index();
            $table->date('expires_at')->nullable()->index();
            $table->text('methodology_summary');
            $table->json('criteria')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('certification_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('certification_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status')->index();
            $table->string('event_type')->default('status_update')->index();
            $table->text('rationale');
            $table->timestamp('effective_at')->index();
            $table->timestamps();
        });

        Schema::create('investment_theses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('developer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('market')->index();
            $table->string('city')->index();
            $table->string('category')->index();
            $table->string('status')->default('published')->index();
            $table->string('positioning_window')->nullable();
            $table->text('executive_summary');
            $table->longText('market_context');
            $table->longText('asset_rationale');
            $table->longText('risk_notes');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('developer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('investment_thesis_id')->nullable()->constrained()->nullOnDelete();
            $table->string('project_name');
            $table->string('slug')->unique();
            $table->string('location')->index();
            $table->string('city')->index();
            $table->string('asset_type')->index();
            $table->string('risk_level')->index();
            $table->string('certification_status')->index();
            $table->string('expected_yield_range')->nullable();
            $table->string('expected_appreciation_range')->nullable();
            $table->text('thesis_summary');
            $table->longText('investment_rationale');
            $table->longText('location_intelligence')->nullable();
            $table->json('financial_indicators')->nullable();
            $table->json('gallery')->nullable();
            $table->json('documents')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('blog_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('featured_image_path')->nullable();
            $table->text('excerpt');
            $table->longText('body');
            $table->string('status')->default('published')->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_post_tag', function (Blueprint $table) {
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blog_tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['blog_post_id', 'blog_tag_id']);
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('investor_type')->nullable()->index();
            $table->string('status')->default('active')->index();
            $table->timestamp('subscribed_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->index();
            $table->string('company')->nullable();
            $table->string('inquiry_type')->index();
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('new')->index();
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('template')->default('editorial');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('status')->default('published')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('key')->index();
            $table->string('eyebrow')->nullable();
            $table->string('heading');
            $table->text('body')->nullable();
            $table->json('content')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('investment_theses');
        Schema::dropIfExists('certification_histories');
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('developers');
    }
};
