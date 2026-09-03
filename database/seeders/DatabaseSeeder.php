<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use \Illuminate\Database\Console\Seeds\WithoutModelEvents;

    public function run(): void
    {
        // =====================
        // Users
        // =====================
        DB::table('users')->insert([
            ['name' => 'Superadmin', 'email' => 'super@enyleather.com', 'password' => Hash::make('password'), 'role' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin',      'email' => 'admin@enyleather.com', 'password' => Hash::make('password'), 'role' => 'admin',      'created_at' => now(), 'updated_at' => now()],
        ]);

        // =====================
        // Categories
        // =====================
        $categories = [
            ['name' => 'Outerwear', 'slug' => 'outerwear', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bottoms', 'slug' => 'bottoms', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Footwear', 'slug' => 'footwear', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accessories', 'slug' => 'accessories', 'created_at' => now(), 'updated_at' => now()],
        ];
        
        DB::table('categories')->insert($categories);
        
        // Get category IDs
        $outerwearId = DB::table('categories')->where('slug', 'outerwear')->value('id');
        $bottomsId = DB::table('categories')->where('slug', 'bottoms')->value('id');
        $footwearId = DB::table('categories')->where('slug', 'footwear')->value('id');
        $accessoriesId = DB::table('categories')->where('slug', 'accessories')->value('id');

        // =====================
        // Products
        // =====================
        $products = [
            [
                'name'        => 'Classic Biker Jacket',
                'description' => 'Jaket kulit hitam klasik dengan desain biker, dibuat dari kulit asli berkualitas tinggi yang awet.',
                'price'       => 4500000,
                'image_url'   => '/images/products/biker_jacket.jpg',
                'category_id' => $outerwearId,
                'stock'       => 10,
                'created_at'  => now(), 'updated_at' => now(),
            ],
            [
                'name'        => 'Premium Leather Pants',
                'description' => 'Celana kulit premium dengan potongan slim fit. Nyaman digunakan untuk acara formal maupun kasual.',
                'price'       => 3200000,
                'image_url'   => '/images/products/leather_pants.jpg',
                'category_id' => $bottomsId,
                'stock'       => 15,
                'created_at'  => now(), 'updated_at' => now(),
            ],
            [
                'name'        => 'Vintage Leather Boots',
                'description' => 'Sepatu boots kulit tangguh dengan sol karet anti-slip, cocok untuk perjalanan jauh maupun gaya urban.',
                'price'       => 2800000,
                'image_url'   => '/images/products/leather_boots.jpg',
                'category_id' => $footwearId,
                'stock'       => 20,
                'created_at'  => now(), 'updated_at' => now(),
            ],
            [
                'name'        => 'Riding Leather Gloves',
                'description' => 'Sarung tangan kulit dengan perlindungan ekstra, wajib dimiliki untuk keamanan berkendara bergaya.',
                'price'       => 850000,
                'image_url'   => '/images/products/leather_gloves.jpg',
                'category_id' => $accessoriesId,
                'stock'       => 30,
                'created_at'  => now(), 'updated_at' => now(),
            ],
            [
                'name'        => 'Classic Leather Vest',
                'description' => 'Rompi kulit bergaya retro yang memancarkan aura tangguh dan maskulin, cocok untuk dipadukan dengan kaos atau kemeja.',
                'price'       => 2100000,
                'image_url'   => '/images/products/leather_vest.jpg',
                'category_id' => $outerwearId,
                'stock'       => 12,
                'created_at'  => now(), 'updated_at' => now(),
            ],
            [
                'name'        => 'Premium Leather Duffel',
                'description' => 'Tas duffel kulit sapi asli berkapasitas besar untuk menemani perjalanan traveling Anda.',
                'price'       => 3900000,
                'image_url'   => '/images/products/leather_bag.jpg',
                'category_id' => $accessoriesId,
                'stock'       => 8,
                'created_at'  => now(), 'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert($products);
    }
}
