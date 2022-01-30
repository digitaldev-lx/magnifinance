<?php

return array (
  'areYouSure' => 'Are you sure?',
  'deleteWarning' => 'You will not be able to recover the deleted record!',
  'closeWarning' => 'You want to close this ticket!',
  'changeCurrency' => 'This will not change your product value its only changes your Currency!',
  'fieldRequired' => 'field is required',
  'alreadyTaken' => 'has already been taken. Try another.',
  'nexmoKeyRequired' => 'Nexmo Key is required for Active Status',
  'nexmoSecretRequired' => 'Nexmo Secret is required for Active Status',
  'nexmoFromRequired' => 'Nexmo From is required for Active Status',
  'msg91KeyRequired' => 'Msg91 Key is required',
  'msg91FromRequired' => 'Msg91 From is required',
  'updateWarning' => 'Employee will not be available on leave day !',
  'updateStatusWarning' => 'Change status will impact on front ratings !',
  'locationDeleteWarning' => 'You will not able to recover the deleted records! All services, deals, and products related to this location will also be deleted',
  'coupon' =>
  array (
    'required' => 'Coupon code can not be blank',
    'serviceRequired' => 'Add atleast one item to cart.',
    'customerRequired' => 'Select customer to continue.',
  ),
    'description' =>
        array (
            'fieldName' => 'Description',
            'min' => 'Your description must have a minimum of 150 characters',
            'max' => 'Your description must have a maximum of 200 characters',
        ),
  'bookingTime' =>
  array (
    'startTime' =>
    array (
      'dateFormat' => 'Open Time must be in format 09:00 AM.',
      'requiredIf' => 'Open Time is required when :other is :value.',
    ),
    'endTime' =>
    array (
      'dateFormat' => 'Close Time must be in format 09:00 AM.',
    ),
    'slotDuration' =>
    array (
      'integer' => 'Slot Duration must be an integer.',
      'requiredIf' => 'Slot Duration is required when :other is :value.',
      'min' => 'Minimum value of Slot Duration must be 1.',
    ),
    'maxBooking' =>
    array (
      'integer' => 'Maximum Number of Booking must be an integer.',
      'requiredIf' => 'Maximum Number of Booking is required when :other is :value.',
      'min' => 'Minimum value of Maximum Number of Booking must be 0.',
    ),
    'perDayMaxBooking' =>
    array (
      'integer' => 'Maximum Number of Per Day Booking must be an integer.',
      'required' => 'Maximum Number of Per Day Booking is required',
      'min' => 'Minimum value of Maximum Number of Per Day Booking must be 0.',
    ),
  ),
  'payment' =>
  array (
    'requiredIf' => 'The :attribute field is required when status is active',
  ),

  'perSlotMaxBooking' =>
  array (
    'integer' => 'Maximum Number of Per Slot Booking must be an integer.',
    'required' => 'Maximum Number of Per Slot Booking is required',
    'min' => 'Minimum value of Maximum Number of Per Slot Booking must be 0.',
  ),
  'error' => 'error?',
  'errorMessage' => 'Some error occur, sorry for inconvenient',
  'invalidLink' => 'This link is not a valid url',
  'adsAtLeast3Days' => 'Your advertisement must be at least 3 days long.',
  'connectionTimeOut' => 'Connection timeout',
  'unknownError' => 'Unknown error occurred',
  'paymentFailed' => 'Payment failed',
  'reCaptchaWarning' => 'Error..! reCaptcha key and secret are required.',
  'invalidReCaptcha' => 'Invalid reCaptcha credentials.',
  'invalidFile' => 'Invalid file type or file size.',
  'loginAsVendor' => 'You will be logout from Super Admin account. Click Login to proceed.',
);
