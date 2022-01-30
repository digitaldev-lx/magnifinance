<div class="modal-header">
   <h4 class="modal-title">@lang('app.edit') @lang('app.currency')</h4>
   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
   <form id="updateCurrency" class="ajax-form" method="POST" autocomplete="off">
      @csrf
      @method('PUT')
      <div class="form-body">
         <div class="row">
            <div class="col-md-12">
               <div class="form-group">
                  <label class="control-label">@lang('app.currency') @lang('app.name')</label>
                  <input type="hidden" class="form-control form-control-lg"  name="id" value="{{ $currency->id }}">
                  <input type="text" class="form-control form-control-lg" id="currency_name" name="currency_name" value="{{ $currency->currency_name }}">
               </div>
               <div class="form-group">
                  <label class="control-label">@lang('app.currencySymbol')</label>
                  <input type="text" class="form-control form-control-lg" id="currency_symbol" name="currency_symbol" value="{{ $currency->currency_symbol }}">
               </div>
               <div class="form-group">
                  <label class="control-label">@lang('app.currencyCode')</label>
                  <input type="text" class="form-control form-control-lg" id="currency_code" {{($currency->currency_code === 'USD')? 'readonly':''}} name="currency_code" value="{{ $currency->currency_code }}">
               </div>
               <div class="form-group">
                  <label class="control-label">@lang('app.exchangeRate')</label>
                  <input type="number" class="form-control form-control-lg" id="exchange_rate" name="exchange_rate" value="{{ $currency->exchange_rate }}">
               </div>
            </div>
         </div>
      </div>
   </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="update-currency" data-row-id="{{ $currency->id }}" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>

