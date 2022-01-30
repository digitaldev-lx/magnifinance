    <div class="row border p-2">
        <div class='col-md-12'><h6>{{ ucwords($customer->name) }}</h6></div>
        <div class='col-md-6'><i class='fa fa-envelope'></i>: {{ $customer->email ?? "--" }}</div>
        <div class='col-md-6'><i class='fa fa-phone'></i>: {{ $customer->mobile ? $customer->formatted_mobile : "--" }}</div>
    </div>
