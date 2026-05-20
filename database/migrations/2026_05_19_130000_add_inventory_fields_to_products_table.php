<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('manage_stock')->default(false)->after('max_order_quantity');
            $table->unsignedInteger('stock_quantity')->nullable()->after('manage_stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['manage_stock', 'stock_quantity']);
        });
    }
};
