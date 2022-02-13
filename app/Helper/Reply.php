<?php

namespace App\Helper;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\JsonResponse;

class Reply
{

    /**
     * success
     *
     * @param  mixed $message
     * @return array
     */
    public static function success($message)
    {
        return [
            'status' => 'success',
            'message' => Reply::getTranslated($message)
        ];
    }

    /**
     * success
     *
     * @param  mixed $message
     * @return JsonResponse
     */
    public static function jsonSuccess($message)
    {
        return JsonResponse::create([
            'status' => 'success',
            'message' => Reply::getTranslated($message)
        ], 200, ['Content-Type'=>'application/json; charset=utf-8']);
    }

    public static function successWithData($message, $data)
    {
        $response = Reply::success($message);

        return array_merge($response, $data);
    }

    /**
     * error
     *
     * @param  mixed $message
     * @param  mixed $error_name
     * @param  mixed $errorData
     * @return array
     */
    public static function error($message, $error_name = null, $errorData = [])
    {
        return [
            'status' => 'fail',
            'error_name' => $error_name,
            'data' => $errorData,
            'message' => Reply::getTranslated($message)
        ];
    }

    /**
     * errorWithoutMessage
     *
     * @return array
     */
    public static function errorWithoutMessage()
    {
        return [
            'status' => 'fail',
        ];
    }

    /**
     * formErrors
     *
     * @param  mixed $validator
     * @return array
     */
    public static function formErrors($validator)
    {
        return [
            'status' => 'fail',
            'errors' => $validator->getMessageBag()->toArray()
        ];
    }

    /**
     * redirect
     *
     * @param  mixed $url
     * @param  mixed $message
     * @return array
     */
    public static function redirect($url, $message = null)
    {
        if ($message) {
            return [
                'status' => 'success',
                'message' => Reply::getTranslated($message),
                'action' => 'redirect',
                'url' => $url
            ];
        }
        else {
            return [
                'status' => 'success',
                'action' => 'redirect',
                'url' => $url
            ];
        }
    }

    private static function getTranslated($message)
    {
        $trans = Lang::get($message);

        if ($trans == $message) {
            return $message;
        }
        else {
            return $trans;
        }
    }

    public static function dataOnly($data)
    {
        return $data;
    }

    public static function redirectWithError($url, $message = null)
    {
        if ($message) {
            return [
                'status' => 'fail',
                'message' => Reply::getTranslated($message),
                'action' => 'redirect',
                'url' => $url
            ];
        }
        else {
            return [
                'status' => 'fail',
                'action' => 'redirect',
                'url' => $url
            ];
        }
    }

}
