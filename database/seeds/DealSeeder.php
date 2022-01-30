<?php

use App\Coupon;
use App\Deal;
use App\FrontFaq;
use App\ItemTax;
use App\Spotlight;
use App\Tax;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DealSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $start_date_time = Carbon::now()->subDays(30)->format('Y-m-d H:i:s');
        $end_date_time = Carbon::now()->addDays(30)->format('Y-m-d H:i:s');

        $deals = [
            [
                'company_id' => '1',
                'location_id' => '1',
                'slug' => 'deep-tissue-massage',
                'title' => 'Choice Of deep-tissue-massage (50 Mins)',
                'deal_type' => '',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'open_time' => '09:00:00',
                'close_time' => '21:59:00',
                'description' => '<div class="tab-content" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit; font-family: " open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"=""><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-bottom: 30px !important; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline;"></div></div><div class="tab-content" open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; margin-bottom: 30px !important;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">How to use offer</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; font: inherit; padding: 0px; border: 0px; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Carry your deal on phone or access it under the "booking" section of the site</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Make prior reservation before you visit the store</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Store verifies the deal&nbsp;or you can redeem it yourself using the site</li></ul></div></div><div open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">Things to remember</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">All offers are inclusive of taxes &amp; service charges</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Prior appointment mandatory (Upon purchase, you will receive a deal with the booking id)</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Cannot be clubbed with any other existing offers/promotions</li></ul></div>',
                'uses_limit' => '0',
                'used_time' => '0',
                'original_amount' => '50',
                'deal_amount' => '16',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'image' => 'spa.jpg',
                'discount_type' => 'percentage',
                'percentage' => '20',
                'deal_applied_on' => 'location',
                'max_order_per_customer' => '10',
            ],
            [
                'company_id' => '1',
                'location_id' => '1',
                'slug' => 'pedicure',
                'title' => 'Deal on Pedicure services (30 Mins)',
                'deal_type' => '',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'open_time' => '09:00:00',
                'close_time' => '21:59:00',
                'description' => '<div class="tab-content" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit; font-family: " open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"=""><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-bottom: 30px !important; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline;"></div></div><div class="tab-content" open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; margin-bottom: 30px !important;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">How to use offer</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; font: inherit; padding: 0px; border: 0px; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Carry your deal on phone or access it under the "booking" section of the site</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Make prior reservation before you visit the store</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Store verifies the deal&nbsp;or you can redeem it yourself using the site</li></ul></div></div><div open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">Things to remember</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">All offers are inclusive of taxes &amp; service charges</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Prior appointment mandatory (Upon purchase, you will receive a deal with the booking id)</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Cannot be clubbed with any other existing offers/promotions</li></ul></div>',
                'uses_limit' => '0',
                'used_time' => '0',
                'original_amount' => '50',
                'deal_amount' => '25',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'image' => 'pedicure.jpg',
                'discount_type' => 'percentage',
                'percentage' => '50',
                'deal_applied_on' => 'location',
                'max_order_per_customer' => '10',
            ],
            [
                'company_id' => '1',
                'location_id' => '1',
                'slug' => 'hair-cut',
                'title' => 'Choice of best HairCut (30 Mins)',
                'deal_type' => '',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'open_time' => '09:00:00',
                'close_time' => '21:59:00',
                'description' => '<div class="tab-content" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit; font-family: " open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"=""><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-bottom: 30px !important; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline;"></div></div><div class="tab-content" open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; margin-bottom: 30px !important;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">How to use offer</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; font: inherit; padding: 0px; border: 0px; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Carry your deal on phone or access it under the "booking" section of the site</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Make prior reservation before you visit the store</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Store verifies the deal&nbsp;or you can redeem it yourself using the site</li></ul></div></div><div open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">Things to remember</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">All offers are inclusive of taxes &amp; service charges</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Prior appointment mandatory (Upon purchase, you will receive a deal with the booking id)</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Cannot be clubbed with any other existing offers/promotions</li></ul></div>',
                'uses_limit' => '0',
                'used_time' => '0',
                'original_amount' => '60',
                'deal_amount' => '48',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'image' => 'hair-cut.jpeg',
                'discount_type' => 'percentage',
                'percentage' => '20',
                'deal_applied_on' => 'location',
                'max_order_per_customer' => '10',
            ],
            [
                'company_id' => '1',
                'location_id' => '1',
                'slug' => 'head-to-toe',
                'title' => 'Head to Toe full body massage (180 Min)',
                'deal_type' => '',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'open_time' => '09:00:00',
                'close_time' => '21:59:00',
                'description' => '<div class="tab-content" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit; font-family: " open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"=""><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-bottom: 30px !important; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline;"></div></div><div class="tab-content" open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; margin-bottom: 30px !important;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">How to use offer</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; font: inherit; padding: 0px; border: 0px; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Carry your deal on phone or access it under the "booking" section of the site</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Make prior reservation before you visit the store</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Store verifies the deal&nbsp;or you can redeem it yourself using the site</li></ul></div></div><div open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">Things to remember</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">All offers are inclusive of taxes &amp; service charges</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Prior appointment mandatory (Upon purchase, you will receive a deal with the booking id)</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Cannot be clubbed with any other existing offers/promotions</li></ul></div>',
                'uses_limit' => '0',
                'used_time' => '0',
                'original_amount' => '200',
                'deal_amount' => '160',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'image' => 'hair-spa.jpg',
                'discount_type' => 'percentage',
                'percentage' => '20',
                'deal_applied_on' => 'location',
                'max_order_per_customer' => '10',
            ],
            [
                'company_id' => '1',
                'location_id' => '2',
                'slug' => 'waxing',
                'title' => 'Get 70% off on Waxing (40 Mins)',
                'deal_type' => '',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'open_time' => '09:00:00',
                'close_time' => '21:59:00',
                'description' => '<div class="tab-content" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit; font-family: " open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"=""><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-bottom: 30px !important; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline;"></div></div><div class="tab-content" open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; margin-bottom: 30px !important;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">How to use offer</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; font: inherit; padding: 0px; border: 0px; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Carry your deal on phone or access it under the "booking" section of the site</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Make prior reservation before you visit the store</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Store verifies the deal&nbsp;or you can redeem it yourself using the site</li></ul></div></div><div open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">Things to remember</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">All offers are inclusive of taxes &amp; service charges</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Prior appointment mandatory (Upon purchase, you will receive a deal with the booking id)</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Cannot be clubbed with any other existing offers/promotions</li></ul></div>',
                'uses_limit' => '0',
                'used_time' => '0',
                'original_amount' => '300',
                'deal_amount' => '90',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'image' => 'waxing.jpg',
                'discount_type' => 'percentage',
                'percentage' => '70',
                'deal_applied_on' => 'location',
                'max_order_per_customer' => '10',
            ],
            [
                'company_id' => '1',
                'location_id' => '2',
                'slug' => 'manicure',
                'title' => 'Best Choice Manicure (20 Mins)',
                'deal_type' => '',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'open_time' => '09:00:00',
                'close_time' => '21:59:00',
                'description' => '<div class="tab-content" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit; font-family: " open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"=""><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-bottom: 30px !important; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline;"></div></div><div class="tab-content" open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; margin-bottom: 30px !important;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">How to use offer</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; font: inherit; padding: 0px; border: 0px; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Carry your deal on phone or access it under the "booking" section of the site</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Make prior reservation before you visit the store</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Store verifies the deal&nbsp;or you can redeem it yourself using the site</li></ul></div></div><div open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">Things to remember</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">All offers are inclusive of taxes &amp; service charges</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Prior appointment mandatory (Upon purchase, you will receive a deal with the booking id)</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Cannot be clubbed with any other existing offers/promotions</li></ul></div>',
                'uses_limit' => '0',
                'used_time' => '0',
                'original_amount' => '20',
                'deal_amount' => '16',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'image' => 'Manicure.jpg',
                'discount_type' => 'percentage',
                'percentage' => '20',
                'deal_applied_on' => 'location',
                'max_order_per_customer' => '10',
            ],
            [
                'company_id' => '1',
                'location_id' => '1',
                'slug' => 'hair-coloring',
                'title' => 'Get Best Hair Coloring (40 Mins)',
                'deal_type' => '',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'open_time' => '09:00:00',
                'close_time' => '21:59:00',
                'description' => '<div class="tab-content" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit; font-family: " open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"=""><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-bottom: 30px !important; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline;"></div></div><div class="tab-content" open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; margin-bottom: 30px !important;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">How to use offer</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; font: inherit; padding: 0px; border: 0px; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Carry your deal on phone or access it under the "booking" section of the site</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Make prior reservation before you visit the store</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Store verifies the deal&nbsp;or you can redeem it yourself using the site</li></ul></div></div><div open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">Things to remember</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">All offers are inclusive of taxes &amp; service charges</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Prior appointment mandatory (Upon purchase, you will receive a deal with the booking id)</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Cannot be clubbed with any other existing offers/promotions</li></ul></div>',
                'uses_limit' => '0',
                'used_time' => '0',
                'original_amount' => '20',
                'deal_amount' => '16',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'image' => 'hair-coloring.jpg',
                'discount_type' => 'percentage',
                'percentage' => '20',
                'deal_applied_on' => 'location',
                'max_order_per_customer' => '10',
            ],
            [
                'company_id' => '1',
                'location_id' => '1',
                'slug' => 'manicure',
                'title' => 'Best Choice Manicure (20 Mins)',
                'deal_type' => '',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'open_time' => '09:00:00',
                'close_time' => '21:59:00',
                'description' => '<div class="tab-content" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit; font-family: " open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"=""><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-bottom: 30px !important; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline;"></div></div><div class="tab-content" open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><div class="margin-bottom-xl" style="box-sizing: inherit; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; margin-bottom: 30px !important;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">How to use offer</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; font: inherit; padding: 0px; border: 0px; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Carry your deal on phone or access it under the "booking" section of the site</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Make prior reservation before you visit the store</li><li class="font-lg" style="box-sizing: inherit; font-weight: inherit; font-stretch: inherit; font-style: inherit; color: rgb(45, 45, 45); padding: 0px; margin: 12px 0px 0px; border: 0px; font-variant: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Store verifies the deal&nbsp;or you can redeem it yourself using the site</li></ul></div></div><div open="" sans",="" sans-serif;="" vertical-align:="" baseline;="" color:="" rgb(102,="" 102,="" 102);"="" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-variant-east-asian: inherit; font-stretch: inherit; font-size: 16px; line-height: inherit;"><h3 class="font-xl line-height-xs font-weight-semibold txt-secondary" style="box-sizing: inherit; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-stretch: inherit; vertical-align: baseline; -webkit-font-smoothing: subpixel-antialiased; font-weight: 600 !important; line-height: 18px !important; color: rgb(102, 102, 102) !important; font-size: 15px !important;">Things to remember</h3><ul class="section" style="box-sizing: inherit; margin: 12px 1em 0px; padding: 0px; border: 0px; font: inherit; vertical-align: baseline; list-style-position: initial; list-style-image: initial;"><li class="font-lg" style="box-sizing: inherit; margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">All offers are inclusive of taxes &amp; service charges</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Prior appointment mandatory (Upon purchase, you will receive a deal with the booking id)</li><li class="font-lg" style="box-sizing: inherit; margin: 12px 0px 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; line-height: 18px; font-family: inherit; vertical-align: baseline; font-size: 14px !important;">Cannot be clubbed with any other existing offers/promotions</li></ul></div>',
                'uses_limit' => '0',
                'used_time' => '0',
                'original_amount' => '120',
                'deal_amount' => '100',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'image' => 'Manicure.jpg',
                'discount_type' => 'percentage',
                'percentage' => '17',
                'deal_applied_on' => 'location',
                'max_order_per_customer' => '10',
            ],
        ];

        foreach ($deals as $key => $deal) {
            Deal::create($deal);
        }
        
        $dealItem = [
            'business_service_id' => 1,
            'quantity' => '1',
            'unit_price' => '20',
            'discount_amount' => '4',
            'total_amount' => '16',
        ];

        $deals = Deal::all();

        foreach ($deals as $deal) {
            $deal->services()->create($dealItem);
        }

        $path = base_path('public/user-uploads/' . 'deal' . '/');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path);
        }

        File::copy(public_path('front/images/hair-cut.jpeg'), public_path('user-uploads/deal/hair-cut.jpeg'));
        File::copy(public_path('front/images/hair-spa.jpg'), public_path('user-uploads/deal/hair-spa.jpg'));
        File::copy(public_path('front/images/deals/hair-coloring.jpg'), public_path('user-uploads/deal/hair-coloring.jpg'));
        File::copy(public_path('front/images/Manicure.jpg'), public_path('user-uploads/deal/Manicure.jpg'));
        File::copy(public_path('front/images/deals/pedicure.jpg'), public_path('user-uploads/deal/pedicure.jpg'));
        File::copy(public_path('front/images/waxing.jpg'), public_path('user-uploads/deal/waxing.jpg'));
        File::copy(public_path('front/images/spa.jpg'), public_path('user-uploads/deal/spa.jpg'));


        Coupon::insert([
            [
                'title' => 'SAVE $20',
                'code' => 'SAVE20',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'uses_limit' => '0',
                'used_time' => '',
                'amount' => '20',
                'minimum_purchase_amount' => '20',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'description' => 'Limited Time Coupon !! HURRY UP.'
            ],
            [
                'title' => 'BACK2BEAUTY',
                'code' => 'BACK15',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'uses_limit' => '0',
                'used_time' => '',
                'amount' => '15',
                'minimum_purchase_amount' => '10',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'description' => 'GRAB IT NOW !! HURRY UP.'
            ],
            [
                'title' => 'Eazy Saloon ',
                'code' => 'GRAB50',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'uses_limit' => '0',
                'used_time' => '',
                'amount' => '10',
                'minimum_purchase_amount' => '10',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'description' => 'GRAB IT NOW !! HURRY UP.'
            ],
            [
                'title' => 'New Year Dhamaka',
                'code' => 'DHAMAKA5',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'uses_limit' => '0',
                'used_time' => '',
                'amount' => '5',
                'minimum_purchase_amount' => '10',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'description' => 'GRAB IT NOW !! HURRY UP.'
            ],
            [
                'title' => 'Happy Diwali',
                'code' => 'HAPPY30',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'uses_limit' => '0',
                'used_time' => '',
                'amount' => '30',
                'minimum_purchase_amount' => '10',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'description' => 'GRAB IT NOW !! HURRY UP.'
            ],
            [
                'title' => 'Summer Relax',
                'code' => 'SUMMER25',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'uses_limit' => '0',
                'used_time' => '',
                'amount' => '25',
                'minimum_purchase_amount' => '10',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'description' => 'GRAB IT NOW !! HURRY UP.'
            ],
            [
                'title' => 'SAVE $10',
                'code' => 'SAVE10',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'uses_limit' => '0',
                'used_time' => '',
                'amount' => '10',
                'minimum_purchase_amount' => '20',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'description' => 'Limited Time Coupon !! HURRY UP.'
            ],
            [
                'title' => 'BIG DEAL',
                'code' => 'BIG50',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'uses_limit' => '0',
                'used_time' => '',
                'amount' => '50',
                'minimum_purchase_amount' => '20',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'description' => 'Limited Time Coupon !! HURRY UP.'
            ],
            [
                'title' => 'DEAL DAY',
                'code' => 'DEAL70',
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'uses_limit' => '0',
                'used_time' => '',
                'amount' => '70',
                'minimum_purchase_amount' => '20',
                'days' => '["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]',
                'status' => 'active',
                'description' => 'Limited Time Coupon !! HURRY UP.'
            ],
        ]);

        FrontFaq::insert([
            [
                'language_id' => 1,
                'question' => 'Can i see demo ?',
                'answer' => 'Yes, definitely. We would be happy to demonstrate you Appointo-multivendor through a web conference at your convenience. Please submit a query on our contact us page or drop a mail to our mail id appointo-multivendor@froiden.com.',
            ],
            [
                'language_id' => 1,
                'question' => 'How can i update app ?',
                'answer' => 'Yes, definitely. We would be happy to demonstrate you Appointo-multivendor through a web conference at your convenience. Please submit a query on our contact us page or drop a mail to our mail id appointo-multivendor@froiden.com.'
            ]
        ]);

        Spotlight::insert([
            [
                'company_id' => 1,
                'deal_id' => 1,
                'from_date' => $start_date_time,
                'to_date' => $end_date_time,
                'sequence' => 1,
            ],
            [
                'company_id' => 1,
                'deal_id' => 2,
                'from_date' => $start_date_time,
                'to_date' => $end_date_time,
                'sequence' => 2,
            ],
            [
                'company_id' => 1,
                'deal_id' => 3,
                'from_date' => $start_date_time,
                'to_date' => $end_date_time,
                'sequence' => 3,
            ],
            [
                'company_id' => 1,
                'deal_id' => 4,
                'from_date' => $start_date_time,
                'to_date' => $end_date_time,
                'sequence' => 4,
            ],
            [
                'company_id' => 1,
                'deal_id' => 5,
                'from_date' => $start_date_time,
                'to_date' => $end_date_time,
                'sequence' => 5,
            ],
            [
                'company_id' => 1,
                'deal_id' => 6,
                'from_date' => $start_date_time,
                'to_date' => $end_date_time,
                'sequence' => 6,
            ]
        ]);

        $tax = Tax::active()->first();
        $deals = Deal::withoutGlobalScopes(['company'])->get();

        if ($deals && $tax) {
            foreach ($deals as $key => $deal) {
                $taxServices = new ItemTax();
                $taxServices->tax_id = $tax->id;
                $taxServices->service_id = null;
                $taxServices->deal_id = $deal->id;
                $taxServices->product_id = null;
                $taxServices->company_id = null;
                $taxServices->save();
            }
        }

    }

}
