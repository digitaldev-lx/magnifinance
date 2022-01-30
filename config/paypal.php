<?php
/**
 * Created by PhpStorm.
 * User: DEXTER
 * Date: 02/08/17
 * Time: 6:17 PM
 */

/** set your paypal credential **/
return array(
    'client_id' => 'AXK3uuszHo2BqBEb8Rq23nedIC1U9k_gkoYJe83bXksXlWI1bFmdFg-KqXWw7URXPQ89gVSXkXURVPts',
    'secret' => 'EKbqc-VerYwFPbEHunoGlcU0ohoViVPyncEDGS35Kgy0bU66Kavu04oA7i9U940NLYiJ35Psal-P52UW',
    /**
     * SDK configuration
     */
    'settings' => array(
        /**
         * Available option 'sandbox' or 'live'
         */
        'mode' => 'sandbox',
        /**
         * Specify the max request time in seconds
         */
        'http.ConnectionTimeOut' => 100000000,
        /**
         * Whether want to log to a file
         */
        'log.LogEnabled' => true,
        /**
         * Specify the file that want to write on
         */
        'log.FileName' => storage_path() . '/logs/paypal.log',
        /**
         * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
         *
         * Logging is most verbose in the 'FINE' level and decreases as you
         * proceed towards ERROR
         */
        'log.LogLevel' => 'FINE'
    ),
);
