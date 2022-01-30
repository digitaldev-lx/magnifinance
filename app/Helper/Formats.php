<?php

namespace App\Helper;

class Formats
{

    public static function dateFormats()
    {
        // below array is the association of date format with datetimepicker format
        // for datetimepicker formats visit https://momentjs.com/docs/#/displaying/format/
        return [
            // date format => datetimepicker format
            'd-m-Y' => 'DD-MM-YYYY',
            'm-d-Y' => 'MM-DD-YYYY',
            'Y-m-d' => 'YYYY-MM-DD',
            'd.m.Y' => 'DD.MM.YYYY',
            'm.d.Y' => 'MM.DD.YYYY',
            'Y.m.d' => 'YYYY.MM.DD',
            'd/m/Y' => 'DD/MM/YYYY',
            'm/d/Y' => 'MM/DD/YYYY',
            'Y/m/d' => 'YYYY/MM/DD',
            'd-M-Y' => 'DD-MMM-YYYY',
            'd/M/Y' => 'DD/MMM/YYYY',
            'd.M.Y' => 'DD.MMM.YYYY',
            'd M Y' => 'DD MMM YYYY',
            'd F, Y' => 'DD MMMM, YYYY',
            'D/M/Y' => 'ddd/MMM/YYYY',
            'D.M.Y' => 'ddd.MMM.YYYY',
            'D-M-Y' => 'ddd-MMM-YYYY',
            'D M Y' => 'ddd MMM YYYY',
            'd D M Y' => 'DD ddd MMM YYYY',
            'D d M Y' => 'ddd DD MMM YYYY',
            'dS M Y' => 'Do MMM YYYY',
        ];
    }

    public static function datePickerFormats()
    {
        // below array is the association of date format with bootstrap datepicker format
        // for bootstrap datepicker formats visit https://bootstrap-datepicker.readthedocs.io/en/latest/options.html#format
        return [
            // date format => datetimepicker format
            'd-m-Y'   => 'dd-mm-yyyy',
            'm-d-Y'   => 'mm-dd-yyyy',
            'Y-m-d'   => 'yyyy-mm-dd',
            'd.m.Y'   => 'dd.mm.yyyy',
            'm.d.Y'   => 'mm.dd.yyyy',
            'Y.m.d'   => 'yyyy.mm.dd',
            'd/m/Y'   => 'dd/mm/yyyy',
            'm/d/Y'   => 'mm/dd/yyyy',
            'Y/m/d'   => 'yyyy/mm/dd',
            'd-M-Y'   => 'dd-M-yyyy',
            'd/M/Y'   => 'dd/M/yyyy',
            'd.M.Y'   => 'dd.M.yyyy',
            'd M Y'   => 'dd M yyyy',
            'd F, Y'  => 'dd MM, yyyy',
            'D/M/Y'   => 'D/M/yyyy',
            'D.M.Y'   => 'D.M.yyyy',
            'D-M-Y'   => 'D-M-yyyy',
            'D M Y'   => 'D M yyyy',
            'd D M Y' => 'dd D M yyyy',
            'D d M Y' => 'D dd M yyyy',
            'dS M Y'  => 'Do M yyyy',
        ];
    }

    public static function timeFormats()
    {
        return [
            'h:i A' => 'hh:mm A',
            'h:i a' => 'hh:mm a',
            'H:i' => 'HH:mm'
        ];
    }

}

