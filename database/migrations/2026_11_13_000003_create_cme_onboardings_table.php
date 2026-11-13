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
        if (!Schema::hasTable('cme_onboardings')) {
            Schema::create('cme_onboardings', function (Blueprint $table) {
                $table->id();
                $table->string('registration_id')->unique();
                $table->json('programmes'); // ["Beauty & Wellness", "Nursing & Healthcare", "Carer & Aged Care"]
                $table->string('nursing_qualification')->nullable();
                $table->string('pincode', 10);
                $table->string('city', 100);
                $table->string('state', 100)->default('Gujarat');
                $table->string('first_name', 100);
                $table->string('last_name', 100);
                $table->string('age_group', 50); // Under 18, 18–24, 25–34, 35–44, 45+
                $table->string('gender', 50); // Female, Male, Other, Prefer not to say
                $table->string('highest_qualification', 100); // 10th pass, 12th pass, Graduate, etc.
                $table->string('vehicle', 50); // Two-wheeler, Four-wheeler, Both, None
                $table->string('driving_licence', 20); // Yes, No
                $table->string('mobile', 20);
                $table->boolean('is_whatsapp_same')->default(true);
                $table->string('whatsapp_number', 20)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('referral_source', 100)->nullable(); // On my own, A person referred me, Through an organisation
                $table->string('referral_name', 150)->nullable();
                $table->string('referral_contact', 100)->nullable();
                $table->string('referral_org', 150)->nullable();
                $table->boolean('confirmed_accuracy')->default(true);
                $table->boolean('confirmed_conditions')->default(true);
                $table->boolean('confirmed_contact')->default(true);
                $table->enum('status', ['pending', 'reviewing', 'interview_scheduled', 'approved', 'rejected'])->default('pending');
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cme_onboardings');
    }
};

