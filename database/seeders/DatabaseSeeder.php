<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@kraftycurvz.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create main categories
        $mainCategories = [
            [
                'name' => 'Resin Products',
                'slug' => 'resin-products',
                'description' => 'Beautiful handmade resin items with glossy finishes and modern aesthetic. Perfect for gifts and home decor.',
                'is_active' => true,
                'parent_id' => null,
                'subcategories' => [
                    ['name' => 'Jewelry', 'slug' => 'resin-jewelry', 'description' => 'Handmade resin jewelry including pendants, earrings, and bracelets'],
                    ['name' => 'Coasters', 'slug' => 'resin-coasters', 'description' => 'Beautiful resin coasters with unique patterns and designs'],
                    ['name' => 'Keychains', 'slug' => 'resin-keychains', 'description' => 'Customizable resin keychains with personalization options'],
                    ['name' => 'Trays', 'slug' => 'resin-trays', 'description' => 'Stunning resin serving trays and decorative trays'],
                    ['name' => 'Decorative Pieces', 'slug' => 'resin-decorative', 'description' => 'Resin wall art, sculptures, and decorative items'],
                ]
            ],
            [
                'name' => 'Candles',
                'slug' => 'candles',
                'description' => 'Handmade candles with beautiful fragrances and designs. Perfect for ambiance and gifting.',
                'is_active' => true,
                'parent_id' => null,
                'subcategories' => [
                    ['name' => 'Scented Candles', 'slug' => 'scented-candles', 'description' => 'Aromatic candles with various fragrances'],
                    ['name' => 'Decorative Candles', 'slug' => 'decorative-candles', 'description' => 'Beautiful decorative candles for home decor'],
                    ['name' => 'Jar Candles', 'slug' => 'jar-candles', 'description' => 'Candles in elegant glass jars'],
                    ['name' => 'Themed Candles', 'slug' => 'themed-candles', 'description' => 'Seasonal and themed candle designs'],
                ]
            ],
            [
                'name' => 'Pipe Cleaner Crafts',
                'slug' => 'pipe-cleaner-crafts',
                'description' => 'Creative handmade items using pipe cleaners. Colorful, playful, and artistic designs.',
                'is_active' => true,
                'parent_id' => null,
                'subcategories' => [
                    ['name' => 'Flowers', 'slug' => 'pipe-cleaner-flowers', 'description' => 'Beautiful pipe cleaner flower arrangements'],
                    ['name' => 'Keychains', 'slug' => 'pipe-cleaner-keychains', 'description' => 'Fun pipe cleaner keychains'],
                    ['name' => 'Bouquets', 'slug' => 'pipe-cleaner-bouquets', 'description' => 'Artistic pipe cleaner bouquets'],
                ]
            ]
        ];

        foreach ($mainCategories as $mainCat) {
            $subcategories = $mainCat['subcategories'];
            unset($mainCat['subcategories']);
            
            $parentCategory = Category::firstOrCreate(['slug' => $mainCat['slug']], $mainCat);
            
            foreach ($subcategories as $subCat) {
                $subCat['parent_id'] = $parentCategory->id;
                $subCat['is_active'] = true;
                Category::firstOrCreate(['slug' => $subCat['slug']], $subCat);
            }
        }

        // Get category IDs
        $resinJewelry = Category::where('slug', 'resin-jewelry')->first();
        $resinCoasters = Category::where('slug', 'resin-coasters')->first();
        $resinKeychains = Category::where('slug', 'resin-keychains')->first();
        $resinTrays = Category::where('slug', 'resin-trays')->first();
        $resinDecorative = Category::where('slug', 'resin-decorative')->first();
        
        $scentedCandles = Category::where('slug', 'scented-candles')->first();
        $decorativeCandles = Category::where('slug', 'decorative-candles')->first();
        $jarCandles = Category::where('slug', 'jar-candles')->first();
        $themedCandles = Category::where('slug', 'themed-candles')->first();
        
        $pipeFlowers = Category::where('slug', 'pipe-cleaner-flowers')->first();
        $pipeKeychains = Category::where('slug', 'pipe-cleaner-keychains')->first();
        $pipeBouquets = Category::where('slug', 'pipe-cleaner-bouquets')->first();

        $products = [
            // Resin Products
            [
                'name' => 'Ocean Wave Coaster Set',
                'description' => 'A beautiful set of 4 ocean-inspired resin coasters with real sand and blue pigments. Each coaster is unique with swirling ocean patterns.',
                'price' => 1200.00,
                'sale_price' => 999.00,
                'category_id' => $resinCoasters->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Geode Resin Tray',
                'description' => 'A stunning geode-inspired serving tray with gold leaf accents and crystal-like resin formations. Perfect for entertaining.',
                'price' => 2500.00,
                'sale_price' => null,
                'category_id' => $resinTrays->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Personalized Name Keychain',
                'description' => 'Custom resin keychain with your name embedded in beautiful colors and glitter. A perfect personalized gift.',
                'price' => 350.00,
                'sale_price' => null,
                'category_id' => $resinKeychains->id,
                'customizable_product' => true,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Galaxy Wall Art',
                'description' => 'A mesmerizing galaxy-themed resin wall art piece with deep purples, blues, and sparkle accents. Makes a stunning centerpiece.',
                'price' => 4500.00,
                'sale_price' => 3999.00,
                'category_id' => $resinDecorative->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Resin Pendant Necklace',
                'description' => 'Elegant handmade resin pendant necklace with dried flowers preserved in crystal-clear resin.',
                'price' => 800.00,
                'sale_price' => null,
                'category_id' => $resinJewelry->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Marble Effect Coaster',
                'description' => 'Elegant marble-effect resin coaster with white and gold veining. Adds a touch of luxury to any table.',
                'price' => 450.00,
                'sale_price' => null,
                'category_id' => $resinCoasters->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Alphabet Letter Keychain',
                'description' => 'Choose your letter and colors for a custom resin alphabet keychain. Great for kids and gifts.',
                'price' => 250.00,
                'sale_price' => null,
                'category_id' => $resinKeychains->id,
                'customizable_product' => true,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Sunset Resin Tray',
                'description' => 'A warm sunset-colored resin tray with oranges, pinks, and gold accents. Perfect for serving or display.',
                'price' => 1800.00,
                'sale_price' => 1599.00,
                'category_id' => $resinTrays->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Custom Name Coaster',
                'description' => 'Personalized resin coaster with your name or initials. Choose your colors and design elements.',
                'price' => 600.00,
                'sale_price' => null,
                'category_id' => $resinCoasters->id,
                'customizable_product' => true,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Floral Resin Bookmark',
                'description' => 'Beautiful resin bookmark with real dried flowers preserved in clear resin. A perfect gift for book lovers.',
                'price' => 300.00,
                'sale_price' => null,
                'category_id' => $resinJewelry->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => false,
            ],
            
            // Candles
            [
                'name' => 'Lavender Scented Candle',
                'description' => 'Calming lavender-scented candle in a beautiful glass jar. Perfect for relaxation and stress relief.',
                'price' => 550.00,
                'sale_price' => null,
                'category_id' => $scentedCandles->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Vanilla Bean Candle',
                'description' => 'Rich vanilla bean scented candle with natural vanilla essence. Warm and comforting fragrance.',
                'price' => 650.00,
                'sale_price' => null,
                'category_id' => $scentedCandles->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Rose Petal Decorative Candle',
                'description' => 'Beautiful decorative candle with real rose petals embedded. Creates a romantic ambiance.',
                'price' => 450.00,
                'sale_price' => null,
                'category_id' => $decorativeCandles->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Honeycomb Jar Candle',
                'description' => 'Elegant honeycomb-textured jar candle with golden honey scent. Long-lasting and beautiful.',
                'price' => 850.00,
                'sale_price' => 699.00,
                'category_id' => $jarCandles->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Christmas Themed Candle Set',
                'description' => 'Festive Christmas-themed candle set with holiday scents and decorations. Perfect for holiday gifting.',
                'price' => 1200.00,
                'sale_price' => null,
                'category_id' => $themedCandles->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Ocean Breeze Scented Candle',
                'description' => 'Fresh ocean breeze scented candle with sea salt and marine notes. Refreshing and clean.',
                'price' => 500.00,
                'sale_price' => null,
                'category_id' => $scentedCandles->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Heart Shaped Decorative Candle',
                'description' => 'Romantic heart-shaped decorative candle. Perfect for anniversaries and Valentine\'s Day.',
                'price' => 400.00,
                'sale_price' => null,
                'category_id' => $decorativeCandles->id,
                'customizable_product' => false,
                'is_active' => true,
                'is_featured' => false,
            ],
            
            // Pipe Cleaner Crafts
            [
                'name' => 'Rainbow Flower Bouquet',
                'description' => 'Colorful rainbow flower bouquet made with pipe cleaners. Cheerful and artistic.',
                'price' => 350.00,
                'sale_price' => null,
                'category_id' => $pipeBouquets->id,
                'customizable_product' => true,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Fluffy Pipe Cleaner Flower',
                'description' => 'Adorable fluffy pipe cleaner flower in a pot. Cute and playful decoration.',
                'price' => 200.00,
                'sale_price' => null,
                'category_id' => $pipeFlowers->id,
                'customizable_product' => true,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Animal Pipe Cleaner Keychain',
                'description' => 'Fun animal-shaped pipe cleaner keychain. Choose your favorite animal design.',
                'price' => 150.00,
                'sale_price' => null,
                'category_id' => $pipeKeychains->id,
                'customizable_product' => true,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Rose Pipe Cleaner Bouquet',
                'description' => 'Elegant rose bouquet made with red and pink pipe cleaners. Long-lasting and beautiful.',
                'price' => 450.00,
                'sale_price' => null,
                'category_id' => $pipeBouquets->id,
                'customizable_product' => true,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Sunflower Pipe Cleaner Art',
                'description' => 'Bright sunflower made with yellow and brown pipe cleaners. Cheerful and sunny.',
                'price' => 250.00,
                'sale_price' => null,
                'category_id' => $pipeFlowers->id,
                'customizable_product' => true,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Initial Pipe Cleaner Keychain',
                'description' => 'Personalized initial keychain made with colorful pipe cleaners. Custom letter available.',
                'price' => 180.00,
                'sale_price' => null,
                'category_id' => $pipeKeychains->id,
                'customizable_product' => true,
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($products as $index => $productData) {
            $productData['slug'] = Str::slug($productData['name']) . '-' . Str::lower(Str::random(6));
            
            // Check if product already exists by name
            $product = Product::where('name', $productData['name'])->first();
            
            if (!$product) {
                $product = Product::create($productData);
            }

            // Add sizes for Trays, Decorative Pieces, and Jar Candles only if they don't exist
            if (in_array($product->category_id, [$resinTrays->id, $resinDecorative->id, $jarCandles->id]) && !$product->sizes()->exists()) {
                $sizes = [
                    ['name' => 'Small', 'price' => $product->price * 0.8, 'sort_order' => 1],
                    ['name' => 'Medium', 'price' => $product->price, 'sort_order' => 2],
                    ['name' => 'Large', 'price' => $product->price * 1.2, 'sort_order' => 3],
                ];

                foreach ($sizes as $sizeData) {
                    ProductSize::create([
                        'product_id' => $product->id,
                        'name' => $sizeData['name'],
                        'price' => $sizeData['price'],
                        'sort_order' => $sizeData['sort_order'],
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
