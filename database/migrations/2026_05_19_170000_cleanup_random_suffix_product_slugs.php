<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $products = DB::table('products')
            ->orderBy('id')
            ->get(['id', 'slug', 'legacy_slug']);

        $used = [];
        foreach ($products as $product) {
            if (!empty($product->slug)) {
                $used[$product->slug] = (int) $product->id;
            }
        }

        foreach ($products as $product) {
            $current = (string) ($product->slug ?? '');
            if ($current === '') {
                continue;
            }

            if (!preg_match('/^(.*)-[a-z0-9]{6}$/', $current, $matches)) {
                continue;
            }

            $candidate = trim((string) ($matches[1] ?? ''));
            if ($candidate === '' || isset($used[$candidate])) {
                continue;
            }

            unset($used[$current]);
            $used[$candidate] = (int) $product->id;

            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'legacy_slug' => $product->legacy_slug ?: $current,
                    'slug' => $candidate,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Intentionally no-op. Previous slugs are preserved in legacy_slug.
    }
};
