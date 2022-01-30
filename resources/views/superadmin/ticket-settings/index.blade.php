<div class="tab-content" id="ticketSettingsTabContent">
    <ul class="nav nav-tabs mb-5" id="ticketSettingsSettingsTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="ticketAgent-tab" data-toggle="tab" href="#ticketAgent"
                role="tab" aria-controls="ticketAgent" aria-selected="true">@lang('app.ticket')
                @lang('app.agent')</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="ticketType-tab" data-toggle="tab" href="#ticketType"
                role="tab" aria-controls="ticketType" aria-selected="true">@lang('app.ticket')
                @lang('app.type')</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="ticketPriority-tab" data-toggle="tab" href="#ticketPriority"
                role="tab" aria-controls="ticketPriority" aria-selected="true">@lang('app.ticket')
                @lang('app.priority')</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="ticketTemplate-tab" data-toggle="tab" href="#ticketTemplate"
                role="tab" aria-controls="ticketTemplate" aria-selected="true">@lang('app.ticket')
                @lang('app.template')</a>
        </li>
    </ul>

    <div class="tab-pane fade show active" id="ticketAgent" role="tabpanel"
        aria-labelledby="ticketAgent-tab">
        @include('superadmin.ticket-settings.ticket-agent')
    </div>

    <div class="tab-pane fade" id="ticketType" role="tabpanel"
        aria-labelledby="ticketType-tab">
        @include('superadmin.ticket-settings.ticket-type')
    </div>

    <div class="tab-pane fade" id="ticketPriority" role="tabpanel"
        aria-labelledby="ticketPriority-tab">
        @include('superadmin.ticket-settings.ticket-priority')
    </div>

    <div class="tab-pane fade" id="ticketTemplate" role="tabpanel"
        aria-labelledby="ticketTemplate-tab">
        @include('superadmin.ticket-settings.ticket-template')
    </div>
</div>
