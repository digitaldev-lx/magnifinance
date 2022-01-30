<?php

use App\FrontThemeSetting;
use App\Helper\Permissions;
use App\Page;
use App\Role;
use App\SmsSetting;
use App\SmtpSetting;
use App\ThemeSetting;
use Illuminate\Database\Seeder;

class SuperAdminSettingSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // default superadmin theme setting
        ThemeSetting::insert([
            'role' => 'superadmin',
            'primary_color' => '#414552',
            'secondary_color' => '#788AE2',
            'sidebar_bg_color' => '#FFFFFF',
            'sidebar_text_color' => '#5C5C62'
        ]);

        // default admin theme setting
        ThemeSetting::insert([
            'role' => 'administrator',
            'primary_color' => '#414552',
            'secondary_color' => '#788AE2',
            'sidebar_bg_color' => '#FFFFFF',
            'sidebar_text_color' => '#5C5C62'
        ]);

        // default customer theme setting
        ThemeSetting::insert([
            'role' => 'customer',
            'primary_color' => '#414552',
            'secondary_color' => '#788AE2',
            'sidebar_bg_color' => '#FFFFFF',
            'sidebar_text_color' => '#5C5C62'
        ]);

        // default front theme setting
        FrontThemeSetting::insert([
            'primary_color' => '#00c1cf',
            'secondary_color' => '#373737',
            'custom_css' => '/* Coupon Box */
.coupon_code_box a {
    background-color: #ffcc00;
}
/* Deals Flag */
.featuredDealDetail .tag {
    background-color: #ffcc00;
}
/* Cart itme quantity number */
.cart-badge {
    background-color: #f72222;
}',
            'seo_description' => 'Meta descriptions can have a surprisingly large impact on your search marketing campaigns; find out how...',
            'seo_keywords' => 'Appointo Multi Vendor,Saas,Manicure',
            'title' => 'Appointo Multi Vendor',

        ]);

        // default pages
        $pages = [
            'aboutUs' => [
                'title' => 'About Us',
                'content' => '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                <br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p>
                <br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p>
                <br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p><br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p>
                ',
                'slug' => 'about-us'
            ],
            'contactUs' => [
                'title' => 'Contact Us',
                'content' => '<p>How can we help you? We will try to get back to you as soon as possible.</p>',
                'slug' => 'contact-us'
            ],
            'howItWorks' => [
                'title' => 'How It Works',
                'content' => '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                <br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p>
                <br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p>
                <br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p><br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p>
                ',
                'slug' => 'how-it-works'
            ],
            'privacyPolicy' => [
                'title' => 'Privacy Policy',
                'content' => '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                <br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p>
                <br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p>
                <br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p><br />
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</p>
                ',
                'slug' => 'privacy-policy'
            ],
        ];

        foreach ($pages as $page) {
            $page_detail = new Page();

            $page_detail->title = $page['title'];
            $page_detail->content = $page['content'];
            $page_detail->slug = $page['slug'];

            $page_detail->save();
        }

        // default email settings
        $smtp = new SmtpSetting();

        $smtp->mail_driver = 'mail';
        $smtp->mail_host = 'smtp.gmail.com';
        $smtp->mail_port = '587';
        $smtp->mail_username = 'myemail@gmail.com';
        $smtp->mail_password = '123456';
        $smtp->mail_from_name = 'Appointo-multi-vendor';
        $smtp->mail_from_email = 'myemail@gmail.com';
        $smtp->mail_encryption = 'none';

        $smtp->save();

        // default sms settings
        (new SmsSetting())->save();

        // create superadmin role
        $roles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Super Admin',
                'description' => 'Super Admin',
            ],
            [
                'name' => 'customer',
                'display_name' => 'Customer',
                'description' => 'Customer',
            ]
        ];

        foreach ($roles as $role) {
            $newRole = Role::create($role);
            Permissions::assignPermissions($newRole);
        }
    }

}
