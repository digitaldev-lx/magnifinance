<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Social Auth
Route::get('/redirect/{provider}', ['uses' => 'Auth\LoginController@redirect', 'as' => 'social.login']);
Route::get('/callback/{provider}', ['uses' => 'Auth\LoginController@callback', 'as' => 'social.login-callback']);

Auth::routes();

Route::post('/save-razorpay-invoices', ['as' => 'save_razorpay-webhook', 'uses' => 'RazorpayWebhookController@saveInvoices']);
Route::post('/save-invoices', ['as' => 'save_webhook', 'uses' => 'StripeWebhookController@saveInvoices']);

// SuperAdmin & Admin Routes
Route::group(['middleware' => 'auth'], function () {

    // SuperAdmin routes
    Route::group(
        ['namespace' => 'SuperAdmin', 'prefix' => 'super-admin', 'as' => 'superadmin.'],
        function () {
            Route::post('theme-settings/update-settings', 'ThemeSettingController@update')->name('theme-settings.updateSettings');
            Route::get('viewCompanyDetail/{id}', 'CompanyController@showCompanyDetails')->name('viewCompanyDetail');
            Route::post('loginAsVendor/{id}', 'CompanyController@loginAsVendor')->name('loginAsVendor');
            Route::get('editNote', 'SettingController@editNote')->name('editNote');
            Route::get('editTerms', 'SettingController@editTerms')->name('editTerms');
            Route::post('updateNote/{id}', 'SettingController@updateNote')->name('updateNote');
            Route::post('updateTerms/{id}', 'SettingController@updateTerms')->name('updateTerms');
            Route::get('change-section-status', 'FrontSettingController@changeSectionStatus')->name('change_section_status');
            Route::post('todo-items/update-todo-item', 'TodoItemController@updateTodoItem')->name('todo-items.updateTodoItem');
            Route::post('tickets/store-images', 'TicketController@storeImages')->name('tickets.storeImages');
            Route::post('tickets/fetch-template', 'TicketController@fetchTemplate')->name('tickets.fetch_template');
            Route::post('tickets/reply', 'TicketController@replyStore')->name('tickets.reply');
            Route::get('tickets/reply/{id}', 'TicketController@latsReplyStore')->name('tickets.latsReply');
            Route::delete('tickets/reply/{id}', 'TicketController@deleteReply')->name('tickets.reply.destroy');
            Route::get('ratings.changeStatus/{id}', ['uses' => 'RatingController@changeStatus'])->name('ratings.changeStatus');

            Route::post('articles/save/{slug}', 'ArticleController@save')->name('articles.save');
            Route::post('articles/{article}', 'ArticleController@update')->name('articles.update');
            Route::post('articles/approve/{article}', 'ArticleController@approve')->name('articles.approve');

            Route::post('advertises/update/{id}', [
                'as' => 'advertises.update',
                'uses' => 'AdvertiseController@update'
            ]);

            Route::resource('advertises', 'AdvertiseController', ['except' => ['update']]);

            Route::resources([
                'pages' => 'PageController',
                'currency-settings' => 'CurrencySettingController',
                'language-settings' => 'LanguageSettingController',
                'email-settings' => 'SmtpSettingController',
                'theme-settings' => 'ThemeSettingController',
                'tax-settings' => 'TaxSettingController',
                'tickets' => 'TicketController',
                'ticket-agents' => 'TicketAgentController',
                'ticket-priorities' => 'TicketPriorityController',
                'ticket-types' => 'TicketTypeController',
                'ticket-templates' => 'TicketTemplateController',
                'front-theme-settings' => 'FrontThemeSettingController',
                'credential' => 'PaymentCredentialSettingController',
                'sms-settings' => 'SmsSettingController',
                'google-captcha-settings' => 'GoogleCaptchaSettingController',
                'profile' => 'ProfileController',
                'settings' => 'SettingController',
                'front-settings' => 'FrontSettingController',
                'front-slider' => 'FrontSliderController',
                'popular-stores' => 'PopularStoresController',
                'companies' => 'CompanyController',
                'locations' => 'LocationController',
                'categories' => 'CategoryController',
                'coupons' => 'CouponController',
                'packages' => 'PackageController',
                'spotlight-deal' => 'SpotlightController',
                'front-faq' => 'FrontFaqSettingController',
                'search' => 'SearchController',
                'front-widget' => 'FrontWidgetController',
                'todo-items' => 'TodoItemController',
                'ratings' => 'RatingController',
                'social-auth-settings' => 'SocialAuthSettingController',
                'articles'     => 'ArticleController',
                'advertises'     => 'AdvertiseController'
            ]);

            Route::get('payment-settings/offline-payments', ['uses' => 'PaymentSettingController@offlinePayments'])->name('payment-settings.offlinePayments');
            Route::resource('/payment-settings', 'PaymentSettingController');
            Route::get('invoices/data', ['uses' => 'InvoiceController@data'])->name('invoices.data');
            Route::resource('/invoices', 'InvoiceController');
            Route::get('bookingInvoice', ['uses' => 'InvoiceController@bookingInvoices'])->name('bookingInvoice');
            Route::get('paypal-invoice-download/{id}', array('as' => 'paypal.invoice-download', 'uses' => 'InvoiceController@paypalInvoiceDownload',));
            Route::get('invoices/invoice-download/{invoice}', 'InvoiceController@download')->name('stripe.invoice-download');
            Route::get('invoices/razorpay-download/{invoice}', 'InvoiceController@razorpayInvoiceDownload')->name('razorpay.invoice-download');
            Route::get('invoices/offline-download/{invoice}', 'InvoiceController@offlineInvoiceDownload')->name('offline.invoice-download');
            Route::resource('/invoices', 'InvoiceController', ['only' => ['index']]);

            Route::get('offline-plan/data', ['uses' => 'OfflinePlanChangeController@data'])->name('offline-plan.data');
            Route::post('offline-plan/verify', ['uses' => 'OfflinePlanChangeController@verify'])->name('offline-plan.verify');
            Route::post('offline-plan/reject', ['uses' => 'OfflinePlanChangeController@reject'])->name('offline-plan.reject');
            Route::resource('/offline-plan', 'OfflinePlanChangeController', ['only' => ['index', 'update']]);

            Route::get('getdeal/{id}', 'SpotlightController@getdeal')->name('getdeal');
            Route::post('updateSequence/{id}', 'SpotlightController@updateSequence')->name('updateSequence');

            Route::put('free-trial-setting/{id}', 'SettingController@freeTrialSetting')->name('freeTrialSetting');

            Route::post('change-language/{code}', 'SettingController@changeLanguage')->name('changeLanguage');
            Route::put('change-language-status/{id}', 'LanguageSettingController@changeStatus')->name('language-settings.changeStatus');
            Route::get('smtp-settings/sent-test-email/modal', ['uses' => 'SmtpSettingController@sendTestEmailModal'])->name('email-settings.sendTestEmailModal');
            Route::get('smtp-settings/sent-test-email', ['uses' => 'SmtpSettingController@sendTestEmail'])->name('email-settings.sendTestEmail');

            Route::get('dashboard', 'ShowDashboard')->name('dashboard');

            Route::post('location/changeStatus', 'LocationController@changeStatus')->name('location.changeStatus');

            Route::post('changePackage', 'CompanyController@changePackage')->name('changePackage');
            Route::post('add-seo-details', 'FrontThemeSettingController@addSeoDetails')->name('add-seo-details');

            Route::post('save-contact-settings', 'SettingController@editContactDetails')->name('save-contact-settings');
            Route::post('save-map-configuration', 'SettingController@editMapKey')->name('save-map-configuration');
            Route::post('save-google-calendar-config', 'SettingController@saveGoogleCalendarConfig')->name('saveGoogleCalendarConfig');
            Route::get('sitemap-download', 'SettingController@sitemapDownload')->name('sitemapDownload');
            Route::post('sitemap-generate', 'SettingController@sitemapGenerate')->name('sitemapGenerate');

            Route::post('reports/earningChart', ['uses' => 'ReportController@earningReportChart'])->name('reports.earningReportChart');
            Route::post('reports/salesChart', ['uses' => 'ReportController@salesReportChart'])->name('reports.salesReportChart');
            Route::post('reports/newCustomers', ['uses' => 'ReportController@newCustomers'])->name('reports.newCustomersChart');
            Route::post('reports/newVendors', ['uses' => 'ReportController@newVendors'])->name('reports.newVendorsChart');
            Route::post('reports/commissionRevenue', ['uses' => 'ReportController@commissionRevenue'])->name('reports.commissionRevenueChart');
            Route::post('reports/advertisesRevenue', ['uses' => 'ReportController@advertisesRevenue'])->name('reports.advertisesRevenueChart');

            Route::get('reports', ['uses' => 'ReportController@index'])->name('reports.index');
            Route::get('reports/commissionRevenueTable', ['uses' => 'ReportController@commissionRevenueTable'])->name('reports.commissionRevenueTable');
            Route::get('reports/advertisesRevenueTable', ['uses' => 'ReportController@advertisesRevenueTable'])->name('reports.advertisesRevenueTable');
            Route::get('reports/customerTable', ['uses' => 'ReportController@customerTable'])->name('reports.customerTable');

            Route::post('currency-formate-settings', 'CurrencySettingController@formateUpdate')->name('currency.formateSettingsUpdate');

            Route::post('role-permission/add-role', 'RolePermissionSettingController@addRole')->name('role-permission.addRole');
            Route::post('role-permission/add-members/{role_id}', 'RolePermissionSettingController@addMembers')->name('role-permission.addMembers');
            Route::get('role-permission/get-members/{role_id}', 'RolePermissionSettingController@getMembers')->name('role-permission.getMembers');
            Route::get('role-permission/get-members-to-add/{id}', 'RolePermissionSettingController@getMembersToAdd')->name('role-permission.getMembersToAdd');
            Route::delete('role-permission/remove-member', 'RolePermissionSettingController@removeMember')->name('role-permission.removeMember');
            Route::get('role-permission/data', 'RolePermissionSettingController@data')->name('role-permission.data');
            Route::post('role-permission/toggleAllPermissions', 'RolePermissionSettingController@toggleAllPermissions')->name('role-permission.toggleAllPermissions');
            Route::resource('role-permission', 'RolePermissionSettingController');

        }
    );

    // Admin routes
    Route::group(
        ['namespace' => 'Admin', 'prefix' => 'account', 'as' => 'admin.'],
        function () {
            Route::put('update-module-setting', 'SettingController@updateModuleSetting')->name('updateModuleSetting');

            Route::get('module-setting', 'SettingController@moduleSetting')->name('moduleSetting');

            Route::post('business-services/store-images', 'BusinessServiceController@storeImages')->name('business-services.storeImages');
            Route::post('business-services/update-images', 'BusinessServiceController@updateImages')->name('business-services.updateImages');

            Route::get('billing', 'BillingController@index')->name('billing.index');
            Route::post('billing/unsubscribe', 'BillingController@cancelSubscription')->name('billing.unsubscribe');
            Route::post('billing/razorpay-payment', 'BillingController@razorpayPayment')->name('billing.razorpay-payment');
            Route::post('billing/razorpay-subscription', 'BillingController@razorpaySubscription')->name('billing.razorpay-subscription');
            Route::get('billing/data', 'BillingController@data')->name('billing.data');
            Route::get('billing/change-plan', 'BillingController@changePlan')->name('billing.change-plan');
            Route::get('billing/select-package/{id?}', 'BillingController@selectPackage')->name('billing.select-package');
            Route::post('subscribe', 'BillingController@subscribe')->name('subscribe');

            Route::get('billing/invoice-download/{invoice}', 'BillingController@download')->name('stripe.invoice-download');
            Route::get('billing/razorpay-invoice-download/{id}', 'BillingController@razorpayInvoiceDownload')->name('billing.razorpay-invoice-download');
            Route::get('billing/offline-payment', 'BillingController@offlinePayment')->name('billing.offline-payment');
            Route::post('billing/free-plan', 'BillingController@freePlan')->name('billing.free-plan');
            Route::post('billing/offline-payment-submit', 'BillingController@offlinePaymentSubmit')->name('billing.offline-payment-submit');
            Route::get('billing/offline-invoice-download/{id}', 'BillingController@offlineInvoiceDownload')->name('billing.offline-invoice-download');

            Route::post('todo-items/update-todo-item', 'TodoItemController@updateTodoItem')->name('todo-items.updateTodoItem');

            Route::post('save-booking-times-field', 'SettingController@saveBookingTimesField')->name('save-booking-times-field');
            Route::put('update-vendor-page/{id}', 'VendorPageController@update')->name('update-vendor-page');
            Route::post('vendor-page/update-images', 'VendorPageController@updateImages')->name('vendor-page.updateImages');
            Route::get('credential/account-link-form', 'PaymentCredentialSettingController@accountLinkForm')->name('credential.accountLinkForm');

            Route::post('tickets/store-images', 'TicketController@storeImages')->name('tickets.storeImages');
            Route::post('tickets/fetch-template', 'TicketController@fetchTemplate')->name('tickets.fetch_template');
            Route::post('tickets/reply', 'TicketController@replyStore')->name('tickets.reply');
            Route::get('tickets/reply/{id}', 'TicketController@latsReplyStore')->name('tickets.latsReply');

            Route::post('articles/save/{slug}', 'ArticleController@save')->name('articles.save');
            Route::post('articles/{article}', 'ArticleController@update')->name('articles.update');

            Route::get('advertises/create/{id?}', [
                'as' => 'advertises.create',
                'uses' => 'AdvertiseController@create'
            ]);

            Route::post('advertises/update/{id}', [
                'as' => 'advertises.update',
                'uses' => 'AdvertiseController@update'
            ]);

            Route::resource('advertises', 'AdvertiseController', ['except' => ['create', 'update']]);

            Route::resources(
                [
                    'tickets' => 'TicketController',
                    'business-services' => 'BusinessServiceController',
                    'settings' => 'SettingController',
                    'booking-times' => 'BookingTimeController',
                    'theme-settings' => 'ThemeSettingController',
                    'customers' => 'CustomerController',
                    'credential' => 'PaymentCredentialSettingController',
                    'todo-items' => 'TodoItemController',
                    'deals' => 'DealController',
                    'billing' => 'BillingController',
                    'employee-schedule' => 'EmployeeScheduleSettingController',
                    'office-leaves'     => 'OfficeLeaveController',
                    'articles'     => 'ArticleController',
                ]
            );

            Route::get('customer/{id}', 'CustomerController@getCustomer')->name('customer.find');

            Route::post('updateWorking/{id}', 'EmployeeScheduleSettingController@updateWorking')->name('updateWorking');
            Route::post('selectLocation', 'DealController@selectLocation')->name('deals.selectLocation');
            Route::post('selectServices', 'DealController@selectServices')->name('deals.selectServices');
            Route::get('resetSelection', 'DealController@resetSelection')->name('deals.resetSelection');
            Route::post('makeDealWithMultipleLocation', 'DealController@makeDealWithMultipleLocation')->name('deals.makeDealWithMultipleLocation');
            Route::post('makeDeal', 'DealController@makeDeal')->name('deals.makeDeal');
            Route::post('makeDealMultipleLocation', 'DealController@makeDealMultipleLocation')->name('deals.makeDealMultipleLocation');

            Route::post('change-language/{code}', 'SettingController@changeLanguage')->name('changeLanguage');

            Route::get('refreshLink/{id}', 'PaymentCredentialSettingController@refreshLink')->name('refreshLink');
            Route::get('returnStripeSuccess', 'PaymentCredentialSettingController@checkVerificationStatus')->name('returnStripeSuccess');

            Route::post('role-permission/add-role', 'RolePermissionSettingController@addRole')->name('role-permission.addRole');
            Route::post('role-permission/add-members/{role_id}', 'RolePermissionSettingController@addMembers')->name('role-permission.addMembers');
            Route::get('role-permission/get-members/{role_id}', 'RolePermissionSettingController@getMembers')->name('role-permission.getMembers');
            Route::get('role-permission/get-members-to-add/{id}', 'RolePermissionSettingController@getMembersToAdd')->name('role-permission.getMembersToAdd');
            Route::delete('role-permission/remove-member', 'RolePermissionSettingController@removeMember')->name('role-permission.removeMember');
            Route::get('role-permission/data', 'RolePermissionSettingController@data')->name('role-permission.data');
            Route::post('role-permission/toggleAllPermissions', 'RolePermissionSettingController@toggleAllPermissions')->name('role-permission.toggleAllPermissions');
            Route::resource('role-permission', 'RolePermissionSettingController');

            Route::get('reports/earningTable', ['uses' => 'ReportController@earningTable'])->name('reports.earningTable');
            Route::post('reports/earningChart', ['uses' => 'ReportController@earningReportChart'])->name('reports.earningReportChart');
            Route::get('reports/customerTable', ['uses' => 'ReportController@customerTable'])->name('reports.customerTable');
            Route::get('reports', ['uses' => 'ReportController@index'])->name('reports.index');

            Route::get('reports/salesTable', ['uses' => 'ReportController@salesTable'])->name('reports.salesTable');
            Route::get('reports/tabularTable', ['uses' => 'ReportController@tabularTable'])->name('reports.tabularTable');
            Route::post('reports/salesChart', ['uses' => 'ReportController@salesReportChart'])->name('reports.salesReportChart');

            /* Graphical reporting section  */
            Route::get('reports/userTypeChart', ['uses' => 'ReportController@userTypeChart'])->name('reports.userTypeChart');
            Route::get('reports/serviceTypeChart', ['uses' => 'ReportController@serviceTypeChart'])->name('reports.serviceTypeChart');
            Route::get('reports/bookingSourceChart', ['uses' => 'ReportController@bookingSourceChart'])->name('reports.bookingSourceChart');
            Route::post('reports/bookingPerDayChart', ['uses' => 'ReportController@bookingPerDayChart'])->name('reports.bookingPerDayChart');
            Route::post('reports/paymentPerDayChart', ['uses' => 'ReportController@paymentPerDayChart'])->name('reports.paymentPerDayChart');
            Route::post('reports/bookingPerMonthChart', ['uses' => 'ReportController@bookingPerMonthChart'])->name('reports.bookingPerMonthChart');
            Route::post('reports/paymentPerMonthChart', ['uses' => 'ReportController@paymentPerMonthChart'])->name('reports.paymentPerMonthChart');
            Route::post('reports/bookingPerYearChart', ['uses' => 'ReportController@bookingPerYearChart'])->name('reports.bookingPerYearChart');
            Route::post('reports/bookingPerYearChart', ['uses' => 'ReportController@bookingPerYearChart'])->name('reports.bookingPerYearChart');
            Route::post('reports/paymentPerYearChart', ['uses' => 'ReportController@paymentPerYearChart'])->name('reports.paymentPerYearChart');

            Route::get('reports/customer', ['uses' => 'ReportController@customer'])->name('reports.customer');

            Route::get('pos/select-customer', ['uses' => 'POSController@selectCustomer'])->name('pos.select-customer');
            Route::get('pos/search-customer', ['uses' => 'POSController@searchCustomer'])->name('pos.search-customer');
            Route::get('pos/filter-services', ['uses' => 'POSController@filterServices'])->name('pos.filter-services');
            Route::get('pos/filter-products', ['uses' => 'POSController@filterProducts'])->name('pos.filter-products');
            Route::get('pos/addCart', ['uses' => 'POSController@addCart'])->name('pos.addCart');
            Route::post('pos/apply-coupon', ['uses' => 'POSController@applyCoupon'])->name('pos.apply-coupon');
            Route::post('pos/update-coupon', ['uses' => 'POSController@updateCoupon'])->name('pos.update-coupon');
            Route::post('/check-user-availability', ['uses' => 'POSController@checkAvailability'])->name('pos.check-user-availability');
            Route::get('pos/show-checkout-modal/{amount}', ['uses' => 'POSController@showCheckoutModal'])->name('pos.show-checkout-modal');
            Route::resource('pos', 'POSController');

            Route::post('employee/changeRole', 'EmployeeController@changeRole')->name('employee.changeRole');
            Route::resource('employee', 'EmployeeController');
            Route::resource('employee-group', 'EmployeeGroupController');

            // Manage Products
            Route::post('products/store-images', 'ProductController@storeImages')->name('products.storeImages');
            Route::post('products/update-images', 'ProductController@updateImages')->name('products.updateImages');
            Route::resource('products', 'ProductController');

            // Manage employee leaves
            Route::get('employeeLeaves', ['uses' => 'LeaveSettingController@view'])->name('employeeLeaves');
            Route::get('changeStatus/{id}', ['uses' => 'LeaveSettingController@updateStatus'])->name('changeStatus');
            Route::resource('employee-leaves', 'LeaveSettingController');

//            Route::resource('update-application', 'UpdateApplicationController');
            Route::resource('search', 'SearchController');

            Route::get('dashboard', 'ShowDashboard')->name('dashboard');

            Route::get('paypal-recurring', array('as' => 'paypal-recurring', 'uses' => 'PaypalController@payWithPaypalRecurrring',));
            Route::get('paypal-invoice-download/{id}', array('as' => 'paypal.invoice-download', 'uses' => 'PaypalController@paypalInvoiceDownload',));
            Route::get('paypal-invoice', array('as' => 'paypal-invoice', 'uses' => 'PaypalController@createInvoice'));

            // route for view/blade file
            Route::get('paywithpaypal', array('as' => 'paywithpaypal', 'uses' => 'PaypalController@payWithPaypal'));
            // route for post request
            Route::get('paypal/{packageId}/{type}', array('as' => 'paypal', 'uses' => 'PaypalController@paymentWithpaypal'));
            Route::get('paypal/cancel-agreement', array('as' => 'paypal.cancel-agreement', 'uses' => 'PaypalController@cancelAgreement'));
            // route for check status responce
            Route::get('paypalx', array('as' => 'status', 'uses' => 'PaypalController@getPaymentStatus'));

            Route::post('bookings/update-coupon', ['uses' => 'BookingController@updateCoupon'])->name('bookings.update-coupon');
            Route::post('multiStatusUpdate', ['uses' => 'BookingController@multiStatusUpdate'])->name('bookings.multiStatusUpdate');
            Route::post('sendReminder', ['uses' => 'BookingController@sendReminder'])->name('bookings.sendReminder');
            Route::get('calendar', ['uses' => 'BookingController@calendar'])->name('calendar');
            Route::post('bookings/storeFeedback', ['uses' => 'BookingController@storeRating'])->name('bookings.storeFeedback');
            Route::post('bookings/{status?}', ['uses' => 'BookingController@index'])->name('bookings.index');
            Route::post('bookings/requestCancel/{id}', ['uses' => 'BookingController@requestCancel'])->name('bookings.requestCancel');
            Route::post('google-notification', ['uses' => 'BookingNotificationController@store'])->name('google.notification.store');
            Route::delete('google-notification/{id}', ['uses' => 'BookingNotificationController@destroy'])->name('google.notification.destroy');
            Route::get('bookings/download/{id}', ['uses' => 'BookingController@download'])->name('bookings.download');
            Route::get('bookings/get-invoce-pdf/{id}', ['uses' => 'BookingController@invocePdf'])->name('bookings.invocePdf');
            Route::get('bookings/print/{id}', ['uses' => 'BookingController@print'])->name('bookings.print');
            Route::put('bookings/update-booking-date/{id}', ['uses' => 'BookingController@updateBookingDate'])->name('bookings.update_booking_date');
            Route::get('bookings/feedBack/{id}', ['uses' => 'BookingController@feedBack'])->name('bookings.feedBack');
            Route::get('bookings/ask-payment-modal/{amount}', ['uses' => 'BookingController@askPaymentModal'])->name('bookings.ask-payment-modal');
            Route::post('/booking-prepayment', ['uses' => 'BookingController@posPrePayment'])->name('booking.prepayment');

            Route::resources([
                'bookings' => 'BookingController',
                'profile' => 'ProfileController'
            ]);

            Route::post('/booking-slots', ['uses' => 'BookingController@bookingSlots'])->name('pos.bookingSlots');
        }
    );

    Route::post('/send-otp-code', 'VerifyMobileController@sendVerificationCode')->name('sendOtpCode');
    Route::post('/send-otp-code/account', 'VerifyMobileController@sendVerificationCode')->name('sendOtpCode.account');
    Route::post('/verify-otp-phone', 'VerifyMobileController@verifyOtpCode')->name('verifyOtpCode');
    Route::post('/verify-otp-phone/account', 'VerifyMobileController@verifyOtpCode')->name('verifyOtpCode.account');
    Route::get('/remove-session', 'VerifyMobileController@removeSession')->name('removeSession');
    Route::get('/google-auth', 'GoogleAuthController@index')->name('googleAuth');
    Route::delete('/google-auth/{id}', 'GoogleAuthController@destroy')->name('googleAuth.destroy');
});

// Front Routes
Route::group(['namespace' => 'Front', 'as' => 'front.'], function () {
    Route::get('teste', 'FrontController@teste');


    Route::get('/pricing', ['uses' => 'FrontController@pricing'])->name('pricing');

    Route::get('confirm/{email}', 'FrontController@confirmEmail')->name('confirm');

    Route::get('/', ['uses' => 'FrontController@index'])->name('index');
    Route::get('/register', ['uses' => 'FrontController@register'])->name('register');
    Route::post('/store-company', ['uses' => 'FrontController@storeCompany'])->name('storeCompany');

    Route::get('/deals', ['uses' => 'FrontController@allDeals'])->name('deals');

    Route::get('/blog', ['uses' => 'FrontController@allArticles'])->name('blog');

    Route::get('{category_id?}/services', ['uses' => 'FrontController@allServices'])->name('services');
    Route::get('{company_id?}/popular-store-services', ['uses' => 'FrontController@allServices'])->name('popular-store-services');

    Route::get('/globalSearch', ['uses' => 'FrontController@globalSearch'])->name('globalSearch');
    Route::get('/search', ['uses' => 'FrontController@searchServices'])->name('search');

    Route::get('/allCoupons', ['uses' => 'FrontController@allCoupons'])->name('allCoupons');

    Route::group(['middleware' => 'cookieRedirect'], function () {
        Route::get('/booking', ['uses' => 'FrontController@bookingPage'])->name('bookingPage');
        Route::get('/checkout', ['uses' => 'FrontController@checkoutPage'])->name('checkoutPage');
    });
    Route::get('/cart', ['uses' => 'FrontController@cartPage'])->name('cartPage');
    Route::get('/check-deal-quantity/{id}', ['uses' => 'FrontController@checkDealQuantity'])->name('check_deal_quantity');

    Route::get('/apply-coupon', ['uses' => 'FrontController@applyCoupon'])->name('apply-coupon');
    Route::get('/update-coupon', ['uses' => 'FrontController@updateCoupon'])->name('update-coupon');
    Route::get('/remove-coupon', ['uses' => 'FrontController@removeCoupon'])->name('remove-coupon');
    Route::post('/add-or-update-product', ['uses' => 'FrontController@addOrUpdateProduct'])->name('addOrUpdateProduct');
    Route::post('/add-booking-details', ['uses' => 'FrontController@addBookingDetails'])->name('addBookingDetails');
    Route::post('/delete-product/{id}', ['uses' => 'FrontController@deleteProduct'])->name('deleteProduct');
    Route::post('/delete-front-product/{id}', ['uses' => 'FrontController@deleteProduct'])->name('deleteFrontProduct');
    Route::post('/update-cart', ['uses' => 'FrontController@updateCart'])->name('updateCart');

    Route::post('/save-booking', ['uses' => 'FrontController@saveBooking'])->name('saveBooking');

    Route::group(['middleware' => 'mobileVerifyRedirect'], function () {
        Route::get('payment-gateway', array('as' => 'payment-gateway', 'uses' => 'FrontController@paymentGateway'));
        Route::get('offline-payment/{bookingId?}/{return_url?}', array('as' => 'offline-payment', 'uses' => 'FrontController@offlinePayment'));
        Route::get('/payment-success/{paymentID?}', ['uses' => 'FrontController@paymentSuccess'])->name('payment.success');
        Route::get('/payment-fail/{paymentID?}', ['uses' => 'FrontController@paymentFail'])->name('payment.fail');
    });
    Route::post('/booking-slots', ['uses' => 'FrontController@bookingSlots'])->name('bookingSlots');
    Route::post('contact', ['uses' => 'FrontController@contact'])->name('contact');

    Route::get('paypal-recurring', array('as' => 'paypal-recurring', 'uses' => 'PaypalController@payWithPaypalRecurrring',));

    // route for view/blade file
    Route::get('paywithpaypal', array('as' => 'paywithpaypal', 'uses' => 'PaypalController@payWithPaypal',));
    // route for post request
    Route::get('paypal/{bookingId?}', array('as' => 'paypal', 'uses' => 'PaypalController@paymentWithpaypal',));
    // route for check status responce
    Route::get('paypal-status/{status?}', array('as' => 'status', 'uses' => 'PaypalController@getPaymentStatus',));
    Route::post('stripe/{bookingId?}', array('as' => 'stripe', 'uses' => 'StripeController@paymentWithStripe'));

    Route::group(['middleware' => 'auth'], function () {
        // razorpay routes
        // Route::get('razorpay/{paymentId}', 'RazorPayController@paymentWithRazorpay')->name('razorpay');
        Route::post('razorpay/create-account-id', 'RazorPayController@createAccount')->name('razorpay.createAccount');
        Route::post('razorpay/create-order', 'RazorPayController@createOrder')->name('razorpay.createOrder');
        Route::post('razorpay/verify-payment/', 'RazorPayController@verifyPayment')->name('razorpay.verifyPayment');

        // stripe routes
        Route::get('create-account-link', 'StripeController@createAccountLink')->name('createAccountLink');
        Route::get('afterStripePayment/{return_url}/{bookingId?}', 'StripeController@afterStripePayment')->name('afterStripePayment');
        Route::get('redirectToErrorPage', 'StripeController@redirectToErrorPage')->name('redirectToErrorPage');
    });

    Route::post('change-language/{code}', 'FrontController@changeLanguage')->name('changeLanguage');
    Route::get('/deal/{dealSlug}', ['uses' => 'FrontController@dealDetail'])->name('dealDetail');

    Route::get('/blog/{slug}', ['uses' => 'FrontController@articleDetail'])->name('blog.article');

    Route::get('/service/{companyId}/{serviceSlug}', ['uses' => 'FrontController@serviceDetail'])->name('serviceDetail');

    Route::get('get-all-locations', ['uses' => 'FrontController@allLocations'])->name('get-all-locations');

    Route::get('/{slug}', ['uses' => 'FrontController@page'])->name('page');

    Route::post('/grabDeal', ['uses' => 'FrontController@grabDeal'])->name('grabDeal');

    Route::post('/check-user-availability', ['uses' => 'FrontController@checkUserAvailability'])->name('checkUserAvailability');
    Route::get('/deals/{slug}', ['uses' => 'FrontController@allCompanyDeals'])->name('companyDeals');
    Route::get('/vendor/{slug}', ['uses' => 'FrontController@vendorPage'])->name('vendorPage');
});


