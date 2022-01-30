<html>
<body>
    <iframe id="printArea" src="{{ route('admin.bookings.invocePdf', $id) }}"
        width="100%" height="100%"></iframe>
</body>
</html>
<script>
    window.onload = function () {
        printDiv();
    }

    function printDiv(){
        document.getElementById('printArea').contentWindow.print();
    }
</script>
