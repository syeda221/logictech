<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\ProductWebImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Original Sample Product Setup
        $category = Category::firstOrCreate(['name' => 'Electronics']);
        $subCategory = Subcategory::firstOrCreate([
            'category_id' => $category->id,
            'name' => 'Air-Condition(AC)',
        ]);

        $unit = Unit::firstOrCreate(['name' => 'Piece']);
        $brandSamsung = Brand::firstOrCreate(['name' => 'Samsung']);
        $brandTrendHub = Brand::firstOrCreate(['name' => 'TrendHub']);

        $lastId = Product::max('id') ?? 0;
        $nextId = $lastId + 1;
        $itemCode = 'ITEM-'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $product = Product::create([
            'creater_id' => 1,
            'category_id' => $category->id,
            'sub_category_id' => $subCategory->id,
            'brand_id' => $brandSamsung->id,
            'is_part' => 0,
            'is_assembled' => 0,
            'is_active' => 1,
            'is_web_visible' => 0,
            'item_code' => $itemCode,
            'unit_id' => $unit->id,
            'item_name' => 'Formal Shirt',
            'color' => json_encode(['Black']),
            'sale_price_per_box' => 5000,
            'sale_price_per_piece' => 5000 / 12,
            'purchase_price_per_piece' => 375,
            'purchase_price_per_box' => 375 * 12,
            'size_mode' => 'by_cartons',
            'pieces_per_box' => 12,
            'total_m2' => 0,
            'height' => 0,
            'width' => 0,
            'pieces_per_m2' => 0,
            'price_per_m2' => 0,
            'purchase_price_per_m2' => 0,
            'barcode_path' => rand(100000000000, 999999999999),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $warehouse = Warehouse::first() ?: Warehouse::create([
            'name' => 'Main Warehouse',
            'address' => 'Default Address',
            'phone' => '1234567890'
        ]);

        if ($warehouse) {
            WarehouseStock::create([
                'warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
                'quantity' => 125,
                'total_pieces' => 125 * 12,
                'remarks' => 'Seeded stock',
            ]);
        }

        // 2. Fashion Products Setup (E-Commerce Web Showcase)
        $fashionCategories = [];
        foreach (['Men', 'Women', 'Boys', 'Girls'] as $catName) {
            $fashionCategories[$catName] = Category::firstOrCreate(
                ['name' => $catName],
                ['show_on_website' => 1]
            );
            $fashionCategories[$catName]->show_on_website = 1;
            $fashionCategories[$catName]->save();
        }

        $fashionProducts = [
            // Men
            [
                'name' => "Men's Classic White Oxford Shirt",
                'category' => 'Men',
                'main_image' => 'product_1_main.jpg',
                'gallery' => ['product_1_gal1.jpg', 'product_1_gal2.jpg'],
                'promo_tag' => 'New Arrival',
                'sale_price' => 2499,
                'web_price' => 1899,
                'purch_price' => 1100,
                'color_variant' => 'White',
                'description' => "Crafted from 100% premium cotton, this classic Oxford shirt features a button-down collar, structured shoulders, and a tailored fit perfect for professional and casual settings alike."
            ],
            [
                'name' => "Men's Tailored Navy Blazer",
                'category' => 'Men',
                'main_image' => 'product_3_main.jpg',
                'gallery' => ['product_3_gal1.jpg', 'product_3_gal2.jpg'],
                'promo_tag' => 'Trending',
                'sale_price' => 5999,
                'web_price' => 4999,
                'purch_price' => 3000,
                'color_variant' => 'Navy Blue',
                'description' => "Elevate your style with this slim-fit navy blazer. Featuring a classic two-button closure, notched lapels, and a lightweight wool-blend construction for year-round sophistication."
            ],
            [
                'name' => "Men's Slim Fit Chino Trousers",
                'category' => 'Men',
                'main_image' => 'product_7_main.jpg',
                'gallery' => ['product_7_gal1.jpg', 'product_7_gal2.jpg'],
                'promo_tag' => 'Best Seller',
                'sale_price' => 2999,
                'web_price' => 2199,
                'purch_price' => 1300,
                'color_variant' => 'Khaki',
                'description' => "Our signature slim chinos offer the ultimate blend of comfort and style. Made with premium stretch cotton twill, detailed with side pockets and a clean flat-front design."
            ],
            [
                'name' => "Men's Casual Denim Jacket",
                'category' => 'Men',
                'main_image' => 'outfitter_prod_1.jpg',
                'gallery' => [],
                'promo_tag' => 'Flash Sale',
                'sale_price' => 4499,
                'web_price' => 3499,
                'purch_price' => 2000,
                'color_variant' => 'Denim Blue',
                'description' => "A timeless staple, this classic denim jacket is made from heavy-weight cotton denim. Features brass button closures, chest pockets, and adjustable waist tabs."
            ],
            [
                'name' => "Men's Athletic Pullover Hoodie",
                'category' => 'Men',
                'main_image' => 'outfitter_prod_5.jpg',
                'gallery' => [],
                'promo_tag' => 'Trending',
                'sale_price' => 3499,
                'web_price' => 2799,
                'purch_price' => 1500,
                'color_variant' => 'Grey Melange',
                'description' => "Stay comfortable with this fleece-lined pullover hoodie. Designed with a double-layered hood, spacious kangaroo pocket, and rib-knit cuffs."
            ],
            // Women
            [
                'name' => "Women's Luxury Silk Maxi Dress",
                'category' => 'Women',
                'main_image' => 'product_2_main.jpg',
                'gallery' => ['product_2_gal1.jpg', 'product_2_gal2.jpg'],
                'promo_tag' => 'Trending',
                'sale_price' => 6999,
                'web_price' => 5499,
                'purch_price' => 3500,
                'color_variant' => 'Crimson Red',
                'description' => "Make a statement in this exquisite silk maxi dress. Designed with a flowing silhouette, elegant wrap V-neckline, and an adjustable tie waist."
            ],
            [
                'name' => "Women's Elegant Leather Handbag",
                'category' => 'Women',
                'main_image' => 'product_4_main.jpg',
                'gallery' => ['product_4_gal1.jpg', 'product_4_gal2.jpg'],
                'promo_tag' => 'Best Seller',
                'sale_price' => 4999,
                'web_price' => 3999,
                'purch_price' => 2200,
                'color_variant' => 'Tan Leather',
                'description' => "Structured from premium vegan leather, this chic handbag features gold-tone hardware, a spacious multi-compartment interior, and a detachable shoulder strap."
            ],
            [
                'name' => "Women's Classic Double-Breasted Trench Coat",
                'category' => 'Women',
                'main_image' => 'product_8_main.jpg',
                'gallery' => ['product_8_gal1.jpg', 'product_8_gal2.jpg'],
                'promo_tag' => 'New Arrival',
                'sale_price' => 7999,
                'web_price' => 6499,
                'purch_price' => 4000,
                'color_variant' => 'Beige',
                'description' => "A sophisticated trench coat styled with double-breasted button closures, waist belt, buttoned shoulder epaulets, and structured storm flaps."
            ],
            [
                'name' => "Women's Cozy Knit Cardigan",
                'category' => 'Women',
                'main_image' => 'outfitter_prod_2.jpg',
                'gallery' => [],
                'promo_tag' => 'New Arrival',
                'sale_price' => 2999,
                'web_price' => 2299,
                'purch_price' => 1300,
                'color_variant' => 'Oatmeal',
                'description' => "Stay warm and cozy in this chunky open-front knit cardigan. Ribbed collar and hem details make it the perfect layering piece."
            ],
            [
                'name' => "Women's Stylish Biker Leather Jacket",
                'category' => 'Women',
                'main_image' => 'outfitter_prod_6.jpg',
                'gallery' => [],
                'promo_tag' => 'Best Seller',
                'sale_price' => 7499,
                'web_price' => 5999,
                'purch_price' => 3500,
                'color_variant' => 'Black Leather',
                'description' => "Bring an edge to your look with this asymmetrical biker jacket. Crafted from top-grade soft leather, detailed with zip pockets and silver hardware."
            ],
            // Boys
            [
                'name' => "Boys' Cotton Polo Shirt",
                'category' => 'Boys',
                'main_image' => 'product_5_main.jpg',
                'gallery' => ['product_5_gal1.jpg', 'product_5_gal2.jpg'],
                'promo_tag' => 'Flash Sale',
                'sale_price' => 1499,
                'web_price' => 999,
                'purch_price' => 600,
                'color_variant' => 'Royal Blue',
                'description' => "Classic short-sleeve polo shirt made from breathable cotton pique fabric. Features a two-button placket and contrasting tip collar."
            ],
            [
                'name' => "Boys' Activewear Crewneck Set",
                'category' => 'Boys',
                'main_image' => 'outfitter_prod_3.jpg',
                'gallery' => [],
                'promo_tag' => 'New Arrival',
                'sale_price' => 2499,
                'web_price' => 1999,
                'purch_price' => 1100,
                'color_variant' => 'Navy / Grey',
                'description' => "Perfect for active play, this two-piece set includes a sporty crewneck sweatshirt and matching elastic-waist joggers with side panels."
            ],
            [
                'name' => "Boys' Adventure Graphic Tee",
                'category' => 'Boys',
                'main_image' => 'outfitter_prod_7.jpg',
                'gallery' => [],
                'promo_tag' => 'Trending',
                'sale_price' => 1199,
                'web_price' => 799,
                'purch_price' => 450,
                'color_variant' => 'Sun Yellow',
                'description' => "Soft cotton jersey tee with a vibrant screen-printed 'Adventure awaits' graphic on the front. Comfort crewneck design."
            ],
            // Girls
            [
                'name' => "Girls' Elegant Floral Summer Dress",
                'category' => 'Girls',
                'main_image' => 'product_6_main.jpg',
                'gallery' => ['product_6_gal1.jpg', 'product_6_gal2.jpg'],
                'promo_tag' => 'Best Seller',
                'sale_price' => 2499,
                'web_price' => 1799,
                'purch_price' => 1000,
                'color_variant' => 'Blush Pink',
                'description' => "A beautifully patterned floral dress featuring a smocked bodice, ruffled sleeves, and a tiered flared skirt for a lovely spinning silhouette."
            ],
            [
                'name' => "Girls' Cozy Cable-Knit Cardigan",
                'category' => 'Girls',
                'main_image' => 'outfitter_prod_4.jpg',
                'gallery' => [],
                'promo_tag' => 'Trending',
                'sale_price' => 2499,
                'web_price' => 1899,
                'purch_price' => 1100,
                'color_variant' => 'Off-White',
                'description' => "Wrap her in warmth with this vintage-inspired cable-knit cardigan. Features a classic round neck and floral-etched front buttons."
            ],
            [
                'name' => "Girls' Classic Denim Skirt",
                'category' => 'Girls',
                'main_image' => 'outfitter_prod_8.jpg',
                'gallery' => [],
                'promo_tag' => 'Flash Sale',
                'sale_price' => 1799,
                'web_price' => 1299,
                'purch_price' => 800,
                'color_variant' => 'Wash Indigo',
                'description' => "Durable stretch denim skirt with an adjustable inner waistband, five-pocket design, and double-stitched hems for daily active wear."
            ]
        ];

        foreach ($fashionProducts as $data) {
            $nextId = (Product::max('id') ?? 0) + 1;
            $code = 'ITEM-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $cat = $fashionCategories[$data['category']];

            // Setup variant details (Sizes S, M, L, XL or 6Y, 8Y, 10Y, 12Y)
            $sizes = $data['category'] === 'Boys' || $data['category'] === 'Girls'
                ? ['6Y', '8Y', '10Y', '12Y']
                : ($data['name'] === "Women's Elegant Leather Handbag" ? ['OS'] : ['S', 'M', 'L', 'XL']);

            $variants = [];
            $stockPerVariant = 25;
            foreach ($sizes as $idx => $size) {
                $variants[] = [
                    'name' => $data['name'] . ' - ' . $size,
                    'size' => $size,
                    'color' => $data['color_variant'],
                    'stock' => $stockPerVariant,
                    'sale_price' => $data['sale_price'],
                    'wholesale_price' => $data['sale_price'] - 200,
                    'weight_per_piece' => 0.3,
                    'purch_price' => $data['purch_price'],
                    'alert' => 5,
                    'barcode' => rand(100000000000, 999999999999),
                    'conv_factor' => 1,
                    'is_base_variant' => $idx === 0 ? 1 : 0,
                    'unit' => 'Pcs',
                ];
            }

            $totalPieces = count($sizes) * $stockPerVariant;

            // Create fashion product
            $p = Product::create([
                'creater_id' => 1,
                'category_id' => $cat->id,
                'sub_category_id' => null,
                'brand_id' => $brandTrendHub->id,
                'is_part' => 0,
                'is_assembled' => 0,
                'is_active' => 1,
                'is_web_visible' => 1,
                'show_on_homepage' => 1,
                'promo_tag' => $data['promo_tag'],
                'item_code' => $code,
                'unit_id' => $unit->id,
                'item_name' => $data['name'],
                'size_mode' => 'by_pieces',
                'height' => 0,
                'width' => 0,
                'pieces_per_box' => 1,
                'pieces_per_m2' => 0,
                'total_m2' => 0,
                'price_per_m2' => 0,
                'sale_price_per_box' => $data['sale_price'],
                'sale_price_per_piece' => $data['sale_price'],
                'purchase_price_per_piece' => $data['purch_price'],
                'purchase_price_per_box' => $data['purch_price'],
                'web_sale_price' => $data['web_price'],
                'auto_hide_out_of_stock' => 0,
                'meta_title' => $data['name'] . " | TrendHub Premium",
                'meta_description' => $data['description'],
                'image' => $data['main_image'],
                'web_main_image' => $data['main_image'],
                'barcode_path' => rand(100000000000, 999999999999),
                'color' => json_encode($variants),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Save web gallery
            foreach ($data['gallery'] as $sort => $galImg) {
                ProductWebImage::create([
                    'product_id' => $p->id,
                    'image_path' => $galImg,
                    'sort_order' => $sort
                ]);
            }

            // Save stock
            if ($warehouse) {
                WarehouseStock::create([
                    'warehouse_id' => $warehouse->id,
                    'product_id' => $p->id,
                    'quantity' => $totalPieces,
                    'total_pieces' => $totalPieces,
                    'remarks' => 'Seeded stock for fashion e-commerce showcase'
                ]);
            }
        }
    }
}
