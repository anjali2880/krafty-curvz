<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_shape_options')->default(false)->after('customizable_product');
            $table->json('shape_options')->nullable()->after('base_image');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('shape_option')->nullable()->after('size_name');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('shape_option');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['has_shape_options', 'shape_options']);
        });
    }
};
