<script>
    function currency_format(number)
        {
            // Strip all characters but numerical ones.
            var decimals = '{{currencyFormatSetting()->no_of_decimal}}';
            var dec_point = '{{currencyFormatSetting()->decimal_separator}}';
            var thousands_sep = '{{currencyFormatSetting()->thousand_separator}}';
            var currency_position = '{{currencyFormatSetting()->currency_position}}';
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var currency_symbol = '{{myCurrencySymbol()}}';
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            // number = dec_point == '' ? s[0] : s.join(dec);
            number = s.join(dec);
            switch (currency_position) {
                case 'left':
                        number = number+currency_symbol;
                    break;
                case 'right':
                        number = currency_symbol+number;
                    break;
                case 'left_with_space':
                        number = number+' '+currency_symbol;
                    break;
                case 'right_with_space':
                        number = currency_symbol+' '+number;
                    break;
                default:
                    number = currency_symbol+number;
                    break;
            }
            return number;
        }
</script>
