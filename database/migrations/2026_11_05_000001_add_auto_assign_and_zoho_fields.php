<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('specialists', function (Blueprint $table) {
            $table->text('serviceable_pincodes')->nullable()->after('pincode');
            $table->string('pincode', 255)->nullable()->change();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('zoho_payment_id')->nullable()->after('razorpay_signature');
            $table->string('zoho_checkout_url')->nullable()->after('zoho_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('specialists', function (Blueprint $table) {
            $table->dropColumn('serviceable_pincodes');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['zoho_payment_id', 'zoho_checkout_url']);
        });
    }
};

