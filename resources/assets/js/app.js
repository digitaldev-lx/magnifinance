import jQuery from 'jquery/src/jquery'
window.$ = window.jQuery = jQuery;
import 'bootstrap/dist/js/bootstrap.bundle.min'

window._ = require( 'lodash' );
window.$ = window.jQuery = require( 'jquery' );
window.Popper = require('popper.js').default;
import 'summernote/dist/summernote-bs4.min'

var moment = require('moment');
window.moment = require('moment');
window.daterangepicker = require('daterangepicker');
import Chart from 'chart.js';

// bootstrap datatables...
require( 'jszip' );
require( 'datatables.net-bs4' );
require( 'datatables.net-buttons-bs4' );
require( 'datatables.net-buttons/js/buttons.colVis.js' );
require( 'datatables.net-buttons/js/buttons.flash.js' );
require( 'datatables.net-buttons/js/buttons.html5.js' );
require( 'datatables.net-buttons/js/buttons.print.js' );
require( 'datatables.net-autofill-bs4' );
require( 'datatables.net-colreorder-bs4' );
require( 'datatables.net-fixedcolumns-bs4' );
require( 'datatables.net-fixedheader-bs4' );
require( 'datatables.net-responsive-bs4' );
require( 'datatables.net-rowreorder-bs4' );
require( 'datatables.net-scroller-bs4' );
require( 'datatables.net-select-bs4' );
// bs4 no js - require direct component
// styling only packages for bs4
require( 'datatables.net-keytable' );
require( 'datatables.net-rowgroup' );

require('eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min')
require('dropify/src/js/dropify');
require('sweetalert/dist/sweetalert.min');
import toastr from 'toastr/build/toastr.min'
window.toastr = toastr;
require('./helper')
require('select2')
import Dropzone from 'dropzone'
window.Dropzone = Dropzone


import ControlSidebar from './ControlSidebar'
import Layout from './Layout'
import PushMenu from './PushMenu'
import Treeview from './Treeview'
import Widget from './Widget'

export {
    jQuery,
    ControlSidebar,
    Layout,
    PushMenu,
    Treeview,
    Widget,
    toastr,
    Dropzone
}
