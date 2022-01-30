<?php

use App\ItemTax;
use Illuminate\Database\Seeder;
use App\Product;
use App\Tax;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'company_id' => 1,
                'location_id' => 1,
                'name' => 'Eyebrow Trimmer',
                'description' => 'Get Best Eyebrow Trimmer in Best Price',
                'price' => '100',
                'image' => '["Hair-Dryer.jpg"]',
                'default_image' => 'Hair-Dryer.jpg',
                'status' => 'active',
            ],
            [
                'company_id' => 1,
                'location_id' => 1,
                'name' => 'Herbal Facial Kit',
                'description' => 'Get Best Herbal Facial Kit in Best Price',
                'price' => '100',
                'image' => '["Herbal Facial Kit.jpg"]',
                'default_image' => 'Herbal Facial Kit.jpg',
                'status' => 'active',
            ],
            [
                'company_id' => 1,
                'location_id' => 1,
                'name' => 'Hair Dye Brush',
                'description' => 'Get Best Hair Dye Brush in Best Price',
                'price' => '100',
                'image' => '["Hair Dye Brush.jpg"]',
                'default_image' => 'Hair Dye Brush.jpg',
                'status' => 'active',
            ],
            [
                'company_id' => 1,
                'location_id' => 2,
                'name' => 'Wooden Hair Brush',
                'description' => 'Get Best Wooden Hair Brush in Best Price',
                'price' => '100',
                'image' => '["Wooden Hair Brush.jpg"]',
                'default_image' => 'Wooden Hair Brush.jpg',
                'status' => 'active',
            ],
            [
                'company_id' => 1,
                'location_id' => 2,
                'name' => 'Home Spa Kit',
                'description' => 'Get Best Home Spa Kit in Best Price',
                'price' => '100',
                'image' => '["Home Spa Kit.jpg"]',
                'default_image' => 'Home Spa Kit.jpg',
                'status' => 'active',
            ],
            [
                'company_id' => 1,
                'location_id' => 1,
                'name' => 'Makeup Brush',
                'description' => 'Get Best Makeup Brush in Best Price',
                'price' => '100',
                'image' => '["Makeup Brush.jpg"]',
                'default_image' => 'Makeup Brush.jpg',
                'status' => 'active',
            ]
        ];


        foreach ($products as $key => $product) {
            Product::create($product);
        }

        $path = base_path('public/user-uploads/' . 'product' . '/');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path);
        }

        $path1 = base_path('public/user-uploads/' . 'product' . '/' . '1' . '/');

        if (!File::isDirectory($path1)) {
            File::makeDirectory($path1);
        }

        $path2 = base_path('public/user-uploads/' . 'product' . '/' . '2' . '/');

        if (!File::isDirectory($path2)) {
            File::makeDirectory($path2);
        }

        $path3 = base_path('public/user-uploads/' . 'product' . '/' . '3' . '/');

        if (!File::isDirectory($path3)) {
            File::makeDirectory($path3);
        }

        $path4 = base_path('public/user-uploads/' . 'product' . '/' . '4' . '/');

        if (!File::isDirectory($path4)) {
            File::makeDirectory($path4);
        }

        $path5 = base_path('public/user-uploads/' . 'product' . '/' . '5' . '/');

        if (!File::isDirectory($path5)) {
            File::makeDirectory($path5);
        }
        
        $path6 = base_path('public/user-uploads/' . 'product' . '/' . '6' . '/');

        if (!File::isDirectory($path6)) {
            File::makeDirectory($path6);
        }

        File::copy(public_path('front/images/Hair-Dryer.jpg'), public_path('user-uploads/product/1/Hair-Dryer.jpg'));
        File::copy(public_path('front/images/Herbal Facial Kit.jpg'), public_path('user-uploads/product/2/Herbal Facial Kit.jpg'));
        File::copy(public_path('front/images/Hair Dye Brush.jpg'), public_path('user-uploads/product/3/Hair Dye Brush.jpg'));
        File::copy(public_path('front/images/Wooden Hair Brush.jpg'), public_path('user-uploads/product/4/Wooden Hair Brush.jpg'));
        File::copy(public_path('front/images/Home Spa Kit.jpg'), public_path('user-uploads/product/5/Home Spa Kit.jpg'));
        File::copy(public_path('front/images/Makeup Brush.jpg'), public_path('user-uploads/product/6/Makeup Brush.jpg'));

        $tax = Tax::active()->first();
        $products = Product::withoutGlobalScopes(['company'])->get();

        if ($products && $tax) {
            foreach ($products as $key => $product) {
                $taxServices = new ItemTax();
                $taxServices->tax_id = $tax->id;
                $taxServices->service_id = null;
                $taxServices->deal_id = null;
                $taxServices->product_id = $product->id;
                $taxServices->company_id = null;
                $taxServices->save();
            }
        }
    }

}
