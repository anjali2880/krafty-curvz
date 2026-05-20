<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('legacy_slug')->nullable()->unique()->after('slug');
        });

        $products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->orderBy('products.id')
            ->get([
                'products.id',
                'products.name',
                'products.slug',
                'products.legacy_slug',
                'categories.slug as category_slug',
            ]);

        $used = DB::table('products')->pluck('slug')->filter()->values()->all();
        $usedMap = array_fill_keys($used, true);

        foreach ($products as $product) {
            $base = Str::slug((string) $product->name);
            if ($base === '') {
                $base = 'product';
            }

            $newSlug = $base;
            $categorySuffix = Str::slug((string) ($product->category_slug ?? ''));

            if (isset($usedMap[$newSlug]) && $newSlug !== $product->slug) {
                if ($categorySuffix !== '' && !isset($usedMap[$base . '-' . $categorySuffix])) {
                    $newSlug = $base . '-' . $categorySuffix;
                } else {
                    $counter = 2;
                    $stem = $categorySuffix !== '' ? $base . '-' . $categorySuffix : $base;
                    $newSlug = $stem . '-' . $counter;
                    while (isset($usedMap[$newSlug])) {
                        $counter++;
                        $newSlug = $stem . '-' . $counter;
                    }
                }
            }

            unset($usedMap[$product->slug]);
            $usedMap[$newSlug] = true;

            if ($newSlug !== $product->slug) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'legacy_slug' => $product->legacy_slug ?: $product->slug,
                        'slug' => $newSlug,
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    public function down(): void
    {
        $products = DB::table('products')->whereNotNull('legacy_slug')->get(['id', 'slug', 'legacy_slug']);

        foreach ($products as $product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'slug' => $product->legacy_slug,
                    'legacy_slug' => null,
                    'updated_at' => now(),
                ]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['legacy_slug']);
            $table->dropColumn('legacy_slug');
        });
    }
};
