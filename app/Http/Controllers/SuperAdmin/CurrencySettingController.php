<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Currency;
use App\Helper\Reply;
use App\CurrencyFormatSetting;
use App\Http\Requests\Currency\StoreCurrency;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Currency\UpdateCurrency;
use App\Http\Requests\Currency\UpdateCurrencyFormatSetting;

class CurrencySettingController extends SuperAdminBaseController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCurrency $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCurrency $request)
    {
        $currency = new Currency();
        $currency->currency_name = $request->currency_name;
        $currency->currency_code = $request->currency_code;
        $currency->currency_symbol = $request->currency_symbol;
        $currency->exchange_rate = $request->exchange_rate;
        $currency->save();

        return Reply::success(__('messages.createdSuccessfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $currency = Currency::find($id);
        return view('superadmin.currency.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCurrency $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCurrency $request, $id)
    {
        $currency = Currency::find($id);
        $currency->currency_name = $request->currency_name;
        $currency->currency_code = $request->currency_code;
        $currency->currency_symbol = $request->currency_symbol;
        $currency->exchange_rate = $request->exchange_rate;
        $currency->save();

        return Reply::redirect(route('superadmin.settings.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCurrencyFormatSetting $request
     * @return \Illuminate\Http\Response
     */
    public function formateUpdate(UpdateCurrencyFormatSetting $request)
    {
        $currencyFormat = CurrencyFormatSetting::first();
        $currencyFormat->currency_position = $request->currency_position;
        $currencyFormat->no_of_decimal = $request->no_of_decimal;
        $currencyFormat->thousand_separator = $request->thousand_separator;
        $currencyFormat->decimal_separator = $request->decimal_separator;
        $currencyFormat->save();
        cache()->forget('currencyFormatSetting');

        return Reply::redirect(route('superadmin.settings.index'), __('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Currency::destroy($id);

        return Reply::success(__('messages.recordDeleted'));
    }

}
