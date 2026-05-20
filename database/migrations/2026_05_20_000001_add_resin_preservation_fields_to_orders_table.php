<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('customer_will_send_item')->default(false)->after('notes');
            $table->text('item_description')->nullable()->after('customer_will_send_item');
            $table->text('custom_note')->nullable()->after('item_description');
            $table->string('courier_agency_name')->nullable()->after('custom_note');
            $table->string('tracking_number')->nullable()->after('courier_agency_name');
            $table->string('parcel_slip_path')->nullable()->after('tracking_number');
            $table->text('parcel_additional_notes')->nullable()->after('parcel_slip_path');
            $table->timestamp('parcel_details_submitted_at')->nullable()->after('parcel_additional_notes');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'customer_will_send_item',
                'item_description',
                'custom_note',
                'courier_agency_name',
                'tracking_number',
                'parcel_slip_path',
                'parcel_additional_notes',
                'parcel_details_submitted_at',
            ]);
        });
    }
};
