<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Services table
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('tag')->nullable(); // e.g. "A gentle start", "Signature · Launch offer", "Premium"
            $table->string('tag_style')->default('normal'); // 'normal', 'tan', 'dark'
            $table->text('description')->nullable();
            $table->json('features')->nullable(); // list of bullet points
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('launch_label')->nullable(); // "launch price"
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Specialists table
        Schema::create('specialists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('qualification'); // e.g. "B.Sc. in Biochemistry"
            $table->string('location'); // "Paldi & Central Ahmedabad (380007)"
            $table->string('pincode', 10)->nullable();
            $table->string('city')->default('ahmedabad'); // ahmedabad, gandhinagar, surat, rajkot
            $table->json('languages')->nullable(); // ['Gujarati', 'Hindi', 'English']
            $table->string('badge')->default('Verified Specialist');
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Time Slots table (allows defining slots per specialist or general, by day of week or specific date)
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialist_id')->nullable()->constrained('specialists')->nullOnDelete();
            $table->enum('period', ['Morning', 'Afternoon', 'Evening']);
            $table->string('start_time'); // "10:00 AM"
            $table->string('end_time'); // "11:30 AM"
            $table->string('day_of_week')->nullable(); // Monday, Tuesday, etc. or null for all
            $table->date('specific_date')->nullable(); // For slot specific to a single date
            $table->integer('max_bookings')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Bookings table
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('pincode', 10);
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('service_name');
            $table->foreignId('specialist_id')->nullable()->constrained('specialists')->nullOnDelete();
            $table->string('specialist_name')->nullable();
            $table->date('booking_date');
            $table->foreignId('time_slot_id')->nullable()->constrained('time_slots')->nullOnDelete();
            $table->string('time_slot_text')->nullable(); // e.g. "Morning (10:00 AM - 11:30 AM)"
            $table->text('skin_concern')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'failed', 'refunded'])->default('unpaid');
            $table->string('payment_method')->default('after_visit'); // 'after_visit', 'razorpay', 'upi'
            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            $table->text('admin_notes')->nullable();
            $table->boolean('consent')->default(true);
            $table->timestamps();
        });

        // Plans table
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('tag')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('price_subtext')->nullable(); // "₹1,100 per visit"
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Devices table
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('pill')->default('Premium');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Products table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('step_name')->nullable(); // "CLEANSE", "TONE", "TREAT"
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('category')->default('daily'); // 'daily', 'wellness'
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Concerns table
        Schema::create('concerns', function (Blueprint $table) {
            $table->id();
            $table->string('quote'); // "“My skin looks dull and tired”"
            $table->text('solution'); // "Try the Nu Skin LumiSpa Glow Facial..."
            $table->string('link_text')->default('See the facial →');
            $table->string('link_target')->default('#services');
            $table->boolean('is_dark')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Reviews / Results table
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('service_tag')->nullable();
            $table->string('concern')->nullable();
            $table->string('plan_info')->nullable();
            $table->string('products_used')->nullable();
            $table->text('quote');
            $table->string('image_after')->nullable();
            $table->string('image_before')->nullable();
            $table->boolean('is_featured_result')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // FAQs table
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Email Templates table (managed dynamically in backend)
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // booking_received, booking_confirmed, booking_cancelled, admin_alert
            $table->string('name');
            $table->string('subject');
            $table->text('content');
            $table->text('variables_hint')->nullable(); // e.g. "{name}, {booking_number}, {service}, {date}, {time}, {specialist}"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Settings table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('concerns');
        Schema::dropIfExists('products');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('specialists');
        Schema::dropIfExists('services');
    }
};
