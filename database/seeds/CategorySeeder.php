<?php

use App\BusinessService;
use App\Category;
use App\Deal;
use App\ItemTax;
use App\Tax;
use App\UniversalSearch;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CategorySeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Hair', 'slug' => 'hair', 'image' => 'salon.jpg'],
            ['name' => 'Nails', 'slug' => 'nails', 'image' => 'nails.jpeg'],
            ['name' => 'Body', 'slug' => 'body', 'image' => 'spa.jpg'],
            ['name' => 'Skin', 'slug' => 'skin', 'image' => 'skin.jpeg'],
            ['name' => 'Face', 'slug' => 'face', 'image' => 'skin.jpg'],
            ['name' => 'Medical', 'slug' => 'Medical', 'image' => 'medical.jpeg'],
            ['name' => 'Soften Skin', 'slug' => 'soften-skin', 'image' => 'Softer Skin.jpg'],
            ['name' => 'Pain Relief', 'slug' => 'pain-relief', 'image' => 'Pain Relief.jpg'],
        ];

        foreach ($categories as $key => $category) {
            Category::create($category);
        }

        $business_services = [
            [
                'company_id' => 1,
                'name' => 'Hair Cut',
                'slug' => 'hair-cut',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '30',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["hair-cut.jpeg", "hair-spa.jpg"]',
                'default_image' => 'hair-cut.jpeg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 1,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Hair Spa',
                'slug' => 'hair-spa',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '50',
                'time' => '30',
                'time_type' => 'minutes',
                'discount' => '5.00',
                'net_price' => '45.00',
                'image' => '["hair-spa.jpg", "hair-cut.jpeg"]',
                'default_image' => 'hair-spa.jpg',
                'discount_type' => 'fixed',
                'status' => 'active',
                'category_id' => 1,
                'location_id' => 2,
            ],
            [
                'company_id' => 1,
                'name' => 'Hair Coloring',
                'slug' => 'hair-coloring',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '50',
                'time' => '30',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '45.00',
                'image' => '["hair-coloring.jpg", "hair-spa.jpg"]',
                'default_image' => 'hair-coloring.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 1,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Waxing',
                'slug' => 'waxing',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '40',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '40.00',
                'image' => '["waxing.jpg", "pedicure.jpg"]',
                'default_image' => 'waxing.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 1,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Manicure',
                'slug' => 'manicure',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["Manicure.jpg", "pedicure.jpg"]',
                'default_image' => 'Manicure.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 2,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Pedicure',
                'slug' => 'pedicure',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '0.00',
                'net_price' => '20.00',
                'image' => '["pedicure.jpg", "Manicure.jpg"]',
                'default_image' => 'pedicure.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 2,
                'location_id' => 2,
            ],
            [
                'company_id' => 1,
                'name' => 'Dip powder nails',
                'slug' => 'dip-powder-nails',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '15',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '0.00',
                'net_price' => '15.00',
                'image' => '["Dip powder nails.jpg", "pedicure.jpg"]',
                'default_image' => 'Dip powder nails.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 2,
                'location_id' => 2,
            ],
            [
                'company_id' => 1,
                'name' => 'Breathing Bounty',
                'slug' => 'breathing-bounty',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '15',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '0.00',
                'net_price' => '15.00',
                'image' => '["Breathing Bounty.jpg", "Deep Breath Spa.jpg"]',
                'default_image' => 'Breathing Bounty.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 3,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Hot Stone Massage',
                'slug' => 'hot-stone-massage',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '50',
                'time' => '50',
                'time_type' => 'minutes',
                'discount' => '0.00',
                'net_price' => '50.00',
                'image' => '["Hot stone.jpg", "spa.jpg"]',
                'default_image' => 'Hot stone.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 3,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Deep Breath Spa',
                'slug' => 'deep-breath-spa',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '15',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '0.00',
                'net_price' => '15.00',
                'image' => '["Deep Breath Spa.jpg", "Breathing Bounty.jpg"]',
                'default_image' => 'Deep Breath Spa.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 3,
                'location_id' => 2,
            ],
            [
                'company_id' => 1,
                'name' => 'Deep Tissue Massage',
                'slug' => 'deep-tissue-massage',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '30',
                'time' => '50',
                'time_type' => 'minutes',
                'discount' => '0.00',
                'net_price' => '30.00',
                'image' => '["spa.jpg", "Hot stone.jpg"]',
                'default_image' => 'spa.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 3,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Exfoliation',
                'slug' => 'exfoliation',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["exfoliation.jpg", "aromatherapy.jpg"]',
                'default_image' => 'exfoliation.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 4,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Aromatherapy',
                'slug' => 'aromatherapy',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["aromatherapy.jpg", "exfoliation.jpg"]',
                'default_image' => 'aromatherapy.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 4,
                'location_id' => 2,
            ],
            [
                'company_id' => 1,
                'name' => 'Wraps and Packs',
                'slug' => 'wraps-and-packs',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["wraps and packs.jpg", "facial masks.jpg"]',
                'default_image' => 'wraps and packs.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 4,
                'location_id' => 2,
            ],
            [
                'company_id' => 1,
                'name' => 'Facial Massages',
                'slug' => 'facial-massages',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["facial massages.jpg", "facial masks.jpg"]',
                'default_image' => 'facial massages.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 5,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Peels',
                'slug' => 'peels',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["peels.jpg", "wraps and packs.jpg"]',
                'default_image' => 'peels.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 5,
                'location_id' => 2,
            ],
            [
                'company_id' => 1,
                'name' => 'Facial Masks',
                'slug' => 'facial-masks',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["facial masks.jpg", "facial massages.jpg"]',
                'default_image' => 'facial masks.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 5,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Permanent Makeup',
                'slug' => 'permanent-makeup',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["Permanent makeup.jpg", "wraps and packs.jpg"]',
                'default_image' => 'Permanent makeup.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 6,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Reflexology',
                'slug' => 'reflexology',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["Reflexology.jpeg", "microdermabrasion.jpg"]',
                'default_image' => 'Reflexology.jpeg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 6,
                'location_id' => 2,
            ],




            [
                'company_id' => 1,
                'name' => 'Perfect Skin Facial',
                'slug' => 'Perfect Skin Facial',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["Reflexology.jpeg", "microdermabrasion.jpg"]',
                'default_image' => 'microdermabrasion.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 7,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Anti Aging Facial',
                'slug' => 'anti-aging-facial',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '50',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '45.00',
                'image' => '["chemical peels.jpeg", "Perfect Skin Facial.jpg"]',
                'default_image' => 'Perfect Skin Facial.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 7,
                'location_id' => 2,
            ],
            [
                'company_id' => 1,
                'name' => 'Ayurveda Pain Relief',
                'slug' => 'ayurveda-pain-relief',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["Perfect Skin Facial.jpg", "chemical peels.jpeg"]',
                'default_image' => 'chemical peels.jpeg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 8,
                'location_id' => 1,
            ],
            [
                'company_id' => 1,
                'name' => 'Ski Boot Revenge',
                'slug' => 'ski-boot-revenge',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '20',
                'time' => '20',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '18.00',
                'image' => '["Anti-Aging Facial.jpg", "chemical peels.jpeg"]',
                'default_image' => 'Anti-Aging Facial.jpg',
                'discount_type' => 'percent',
                'status' => 'active',
                'category_id' => 8,
                'location_id' => 1,
            ]
        ];

        foreach ($business_services as $key => $business_service) {
            BusinessService::create($business_service);
        }

        // Insert some front popular search
        $universal_searches = [
            [
                'location_id' => 1,
                'searchable_id' => 'keywords',
                'searchable_type' => 'service',
                'title' => 'hair coloring',
                'route_name' => 'front.searchServices',
                'count' => 7,
                'type' => 'frontend'
            ],
            [
                'location_id' => 1,
                'searchable_id' => 'keywords',
                'searchable_type' => 'service',
                'title' => 'hair spa',
                'route_name' => 'front.searchServices',
                'count' => 6,
                'type' => 'frontend'
            ],
            [
                'location_id' => 1,
                'searchable_id' => 'keywords',
                'searchable_type' => 'service',
                'title' => 'manicure',
                'route_name' => 'front.searchServices',
                'count' => 5,
                'type' => 'frontend'
            ],
            [
                'location_id' => 1,
                'searchable_id' => 'keywords',
                'searchable_type' => 'service',
                'title' => 'facial',
                'route_name' => 'front.searchServices',
                'count' => 4,
                'type' => 'frontend'
            ],
            [
                'location_id' => 1,
                'searchable_id' => 'keywords',
                'searchable_type' => 'service',
                'title' => 'massage',
                'route_name' => 'front.searchServices',
                'count' => 3,
                'type' => 'frontend'
            ],
        ];

        foreach ($universal_searches as $key => $universal_search) {
            UniversalSearch::create($universal_search);
        }

        $path = base_path('public/user-uploads/' . 'category' . '/');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path);
        }

        File::copy(public_path('front/images/hair-cut.jpeg'), public_path('user-uploads/category/hair-cut.jpeg'));
        File::copy(public_path('front/images/spa.jpg'), public_path('user-uploads/category/spa.jpg'));
        File::copy(public_path('front/images/salon.jpg'), public_path('user-uploads/category/salon.jpg'));
        File::copy(public_path('front/images/skin.jpeg'), public_path('user-uploads/category/skin.jpeg'));
        File::copy(public_path('front/images/skin.jpg'), public_path('user-uploads/category/skin.jpg'));
        File::copy(public_path('front/images/nails.jpg'), public_path('user-uploads/category/nails.jpeg'));
        File::copy(public_path('front/images/medical.jpeg'), public_path('user-uploads/category/medical.jpeg'));
        File::copy(public_path('front/images/Softer Skin.jpg'), public_path('user-uploads/category/Softer Skin.jpg'));
        File::copy(public_path('front/images/Pain Relief.jpg'), public_path('user-uploads/category/Pain Relief.jpg'));

        $path = base_path('public/user-uploads/' . 'service' . '/');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path);
        }

        $path1 = base_path('public/user-uploads/' . 'service' . '/' . '1' . '/');

        if (!File::isDirectory($path1)) {
            File::makeDirectory($path1);
        }

        File::copy(public_path('front/images/hair-cut.jpeg'), public_path('user-uploads/service/1/hair-cut.jpeg'));
        File::copy(public_path('front/images/hair-spa.jpg'), public_path('user-uploads/service/1/hair-spa.jpg'));

        $path2 = base_path('public/user-uploads/' . 'service' . '/' . '2' . '/');

        if (!File::isDirectory($path2)) {
            File::makeDirectory($path2);
        }

        File::copy(public_path('front/images/hair-spa.jpg'), public_path('user-uploads/service/2/hair-spa.jpg'));
        File::copy(public_path('front/images/hair-cut.jpeg'), public_path('user-uploads/service/2/hair-cut.jpeg'));

        $path3 = base_path('public/user-uploads/' . 'service' . '/' . '3' . '/');

        if (!File::isDirectory($path3)) {
            File::makeDirectory($path3);
        }

        File::copy(public_path('front/images/deals/hair-coloring.jpg'), public_path('user-uploads/service/3/hair-coloring.jpg'));
        File::copy(public_path('front/images/hair-spa.jpg'), public_path('user-uploads/service/3/hair-spa.jpg'));

        $path4 = base_path('public/user-uploads/' . 'service' . '/' . '4' . '/');

        if (!File::isDirectory($path4)) {
            File::makeDirectory($path4);
        }

        File::copy(public_path('front/images/waxing.jpg'), public_path('user-uploads/service/4/waxing.jpg'));
        File::copy(public_path('front/images/pedicure.jpg'), public_path('user-uploads/service/4/pedicure.jpg'));

        $path5 = base_path('public/user-uploads/' . 'service' . '/' . '5' . '/');

        if (!File::isDirectory($path5)) {
            File::makeDirectory($path5);
        }

        File::copy(public_path('front/images/Manicure.jpg'), public_path('user-uploads/service/5/Manicure.jpg'));
        File::copy(public_path('front/images/pedicure.jpg'), public_path('user-uploads/service/5/pedicure.jpg'));

        $path6 = base_path('public/user-uploads/' . 'service' . '/' . '6' . '/');

        if (!File::isDirectory($path6)) {
            File::makeDirectory($path6);
        }

        File::copy(public_path('front/images/Manicure.jpg'), public_path('user-uploads/service/6/Manicure.jpg'));
        File::copy(public_path('front/images/pedicure.jpg'), public_path('user-uploads/service/6/pedicure.jpg'));

        $path7 = base_path('public/user-uploads/' . 'service' . '/' . '7' . '/');

        if (!File::isDirectory($path7)) {
            File::makeDirectory($path7);
        }

        File::copy(public_path('front/images/Dip powder nails.jpg'), public_path('user-uploads/service/7/Dip powder nails.jpg'));
        File::copy(public_path('front/images/pedicure.jpg'), public_path('user-uploads/service/7/pedicure.jpg'));

        $path8 = base_path('public/user-uploads/' . 'service' . '/' . '8' . '/');

        if (!File::isDirectory($path8)) {
            File::makeDirectory($path8);
        }

        File::copy(public_path('front/images/Breathing Bounty.jpg'), public_path('user-uploads/service/8/Breathing Bounty.jpg'));
        File::copy(public_path('front/images/Deep Breath Spa.jpg'), public_path('user-uploads/service/8/Deep Breath Spa.jpg'));

        $path9 = base_path('public/user-uploads/' . 'service' . '/' . '9' . '/');

        if (!File::isDirectory($path9)) {
            File::makeDirectory($path9);
        }

        File::copy(public_path('front/images/Hot stone.jpg'), public_path('user-uploads/service/9/Hot stone.jpg'));
        File::copy(public_path('front/images/spa.jpg'), public_path('user-uploads/service/9/spa.jpg'));

        $path10 = base_path('public/user-uploads/' . 'service' . '/' . '10' . '/');

        if (!File::isDirectory($path10)) {
            File::makeDirectory($path10);
        }

        File::copy(public_path('front/images/Deep Breath Spa.jpg'), public_path('user-uploads/service/10/Deep Breath Spa.jpg'));
        File::copy(public_path('front/images/Breathing Bounty.jpg'), public_path('user-uploads/service/10/Breathing Bounty.jpg'));

        $path11 = base_path('public/user-uploads/' . 'service' . '/' . '11' . '/');

        if (!File::isDirectory($path11)) {
            File::makeDirectory($path11);
        }

        File::copy(public_path('front/images/spa.jpg'), public_path('user-uploads/service/11/spa.jpg'));
        File::copy(public_path('front/images/Hot stone.jpg'), public_path('user-uploads/service/11/Hot stone.jpg'));

        $path12 = base_path('public/user-uploads/' . 'service' . '/' . '12' . '/');

        if (!File::isDirectory($path12)) {
            File::makeDirectory($path12);
        }

        File::copy(public_path('front/images/exfoliation.jpg'), public_path('user-uploads/service/12/exfoliation.jpg'));
        File::copy(public_path('front/images/aromatherapy.jpg'), public_path('user-uploads/service/12/aromatherapy.jpg'));

        $path13 = base_path('public/user-uploads/' . 'service' . '/' . '13' . '/');

        if (!File::isDirectory($path13)) {
            File::makeDirectory($path13);
        }

        File::copy(public_path('front/images/aromatherapy.jpg'), public_path('user-uploads/service/13/aromatherapy.jpg'));
        File::copy(public_path('front/images/exfoliation.jpg'), public_path('user-uploads/service/13/exfoliation.jpg'));

        $path14 = base_path('public/user-uploads/' . 'service' . '/' . '14' . '/');

        if (!File::isDirectory($path14)) {
            File::makeDirectory($path14);
        }

        File::copy(public_path('front/images/wraps and packs.jpg'), public_path('user-uploads/service/14/wraps and packs.jpg'));
        File::copy(public_path('front/images/facial masks.jpg'), public_path('user-uploads/service/14/facial masks.jpg'));

        $path15 = base_path('public/user-uploads/' . 'service' . '/' . '15' . '/');

        if (!File::isDirectory($path15)) {
            File::makeDirectory($path15);
        }

        File::copy(public_path('front/images/facial massages.jpg'), public_path('user-uploads/service/15/facial massages.jpg'));
        File::copy(public_path('front/images/facial masks.jpg'), public_path('user-uploads/service/15/facial masks.jpg'));

        $path16 = base_path('public/user-uploads/' . 'service' . '/' . '16' . '/');

        if (!File::isDirectory($path16)) {
            File::makeDirectory($path16);
        }

        File::copy(public_path('front/images/peels.jpg'), public_path('user-uploads/service/16/peels.jpg'));
        File::copy(public_path('front/images/wraps and packs.jpg'), public_path('user-uploads/service/16/wraps and packs.jpg'));

        $path17 = base_path('public/user-uploads/' . 'service' . '/' . '17' . '/');

        if (!File::isDirectory($path17)) {
            File::makeDirectory($path17);
        }

        File::copy(public_path('front/images/facial masks.jpg'), public_path('user-uploads/service/17/facial masks.jpg'));
        File::copy(public_path('front/images/facial massages.jpg'), public_path('user-uploads/service/17/facial massages.jpg'));

        $path18 = base_path('public/user-uploads/' . 'service' . '/' . '18' . '/');

        if (!File::isDirectory($path18)) {
            File::makeDirectory($path18);
        }

        File::copy(public_path('front/images/Permanent makeup.jpg'), public_path('user-uploads/service/18/Permanent makeup.jpg'));
        File::copy(public_path('front/images/wraps and packs.jpg'), public_path('user-uploads/service/18/wraps and packs.jpg'));

        $path19 = base_path('public/user-uploads/' . 'service' . '/' . '19' . '/');

        if (!File::isDirectory($path19)) {
            File::makeDirectory($path19);
        }

        File::copy(public_path('front/images/microdermabrasion.jpg'), public_path('user-uploads/service/19/microdermabrasion.jpg'));
        File::copy(public_path('front/images/Reflexology.jpeg'), public_path('user-uploads/service/19/Reflexology.jpeg'));


        $path20 = base_path('public/user-uploads/' . 'service' . '/' . '20' . '/');

        if (!File::isDirectory($path20)) {
            File::makeDirectory($path20);
        }

        File::copy(public_path('front/images/Reflexology.jpeg'), public_path('user-uploads/service/20/Reflexology.jpeg'));
        File::copy(public_path('front/images/microdermabrasion.jpg'), public_path('user-uploads/service/20/microdermabrasion.jpg'));

        $path21 = base_path('public/user-uploads/' . 'service' . '/' . '21' . '/');

        if (!File::isDirectory($path21)) {
            File::makeDirectory($path21);
        }

        File::copy(public_path('front/images/chemical peels.jpeg'), public_path('user-uploads/service/21/chemical peels.jpeg'));
        File::copy(public_path('front/images/Perfect Skin Facial.jpg'), public_path('user-uploads/service/21/Perfect Skin Facial.jpg'));

        $path22 = base_path('public/user-uploads/' . 'service' . '/' . '22' . '/');

        if (!File::isDirectory($path22)) {
            File::makeDirectory($path22);
        }

        File::copy(public_path('front/images/Perfect Skin Facial.jpg'), public_path('user-uploads/service/22/Perfect Skin Facial.jpg'));
        File::copy(public_path('front/images/chemical peels.jpeg'), public_path('user-uploads/service/22/chemical peels.jpeg'));

        $path23 = base_path('public/user-uploads/' . 'service' . '/' . '23' . '/');

        if (!File::isDirectory($path23)) {
            File::makeDirectory($path23);
        }

        File::copy(public_path('front/images/Anti-Aging Facial.jpg'), public_path('user-uploads/service/23/Anti-Aging Facial.jpg'));
        File::copy(public_path('front/images/chemical peels.jpeg'), public_path('user-uploads/service/23/chemical peels.jpeg'));

        $path24 = base_path('public/user-uploads/' . 'service' . '/' . '24' . '/');

        if (!File::isDirectory($path24)) {
            File::makeDirectory($path24);
        }

        File::copy(public_path('front/images/HydraFacial.jpg'), public_path('user-uploads/service/24/HydraFacial.jpg'));
        File::copy(public_path('front/images/Anti-Aging Facial.jpg'), public_path('user-uploads/service/24/Anti-Aging Facial.jpg'));

        $path25 = base_path('public/user-uploads/' . 'service' . '/' . '25' . '/');

        if (!File::isDirectory($path25)) {
            File::makeDirectory($path25);
        }

        File::copy(public_path('front/images/ayurveda-pain-relief.jpeg'), public_path('user-uploads/service/25/ayurveda-pain-relief.jpeg'));
        File::copy(public_path('front/images/Ski Boot Revenge.jpg'), public_path('user-uploads/service/25/Ski Boot Revenge.jpg'));

        $path26 = base_path('public/user-uploads/' . 'service' . '/' . '26' . '/');

        if (!File::isDirectory($path26)) {
            File::makeDirectory($path26);
        }

        File::copy(public_path('front/images/Ski Boot Revenge.jpg'), public_path('user-uploads/service/26/Ski Boot Revenge.jpg'));
        File::copy(public_path('front/images/ayurveda-pain-relief.jpeg'), public_path('user-uploads/service/26/ayurveda-pain-relief.jpeg'));

        $path27 = base_path('public/user-uploads/' . 'service' . '/' . '27' . '/');

        if (!File::isDirectory($path27)) {
            File::makeDirectory($path27);
        }

        File::copy(public_path('front/images/Maya Abdominal Therapy.jpg'), public_path('user-uploads/service/27/Maya Abdominal Therapy.jpg'));
        File::copy(public_path('front/images/spa.jpg'), public_path('user-uploads/service/27/spa.jpg'));

        $tax = Tax::active()->first();
        $services = BusinessService::withoutGlobalScopes(['company'])->get();

        if ($services && $tax) {
            foreach ($services as $key => $value) {
                $taxServices = new ItemTax();
                $taxServices->tax_id = $tax->id;
                $taxServices->service_id = $value->id;
                $taxServices->deal_id = null;
                $taxServices->product_id = null;
                $taxServices->company_id = null;
                $taxServices->save();
            }
        }
    }

}
