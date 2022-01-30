<div class="col-12  mb-2 card-body @if ($reply->user->id == $user->id) bg-owner-reply
    @else bg-other-reply @endif "
        id="replyMessageBox_{{ $reply->id }}">

        <div class="row">

            <div class="col-xs-2 col-md-1">
                <img src="{{ $reply->user->user_image_url }}" alt="user" class="rounded-circle"
                    width="40" height="40">
            </div>
            <div class="col-xs-8 col-md-10">
                <h5 class="m-t-0 font-bold">
                    <div class="text-dark">{{ ucwords($reply->user->name) }}
                        <span
                            class="text-muted text-sm font-weight-normal">{{ $reply->created_at->timezone($settings->timezone)->format($settings->date_format . ' ' . $settings->time_format) }}</span>
                    </div>
                </h5>

                <div class="font-light">
                    {!! ucfirst(nl2br($reply->comment)) !!}
                </div>
            </div>

            <div class="col-xs-2 col-md-1">
                <a href="javascript:;" data-toggle="tooltip" data-placement='left'
                    data-original-title="@lang('app.delete')" data-file-id="{{ $reply->id }}"
                    class="btn btn-outline-dark sa-params" data-pk="list"><i
                        class="fa fa-trash"></i></a>
            </div>

        </div>
        @if ($reply->files)
            <div class="col-12" id="list">
                <ul class="list-group" id="files-list">
                    @forelse($reply->files as $file)
                        <li class="list-group-item col-md-12">
                            <div class="row">
                                <div class="col-md-8">
                                    {{ $file }}
                                </div>
                                <div class="col-md-4 text-right">

                                    <a target="_blank"
                                        href="{{ asset_url('ticket/' . $ticket->id . '/' . $file) }}"
                                        data-toggle="tooltip" data-original-title="@lang('app.view')"
                                        class="btn btn-outline-info"><i
                                            class="fa fa-search"></i></a>


                                    &nbsp;&nbsp;
                                    <a href="{{ asset_url('ticket/' . $ticket->id . '/' . $file) }}"
                                        download data-toggle="tooltip"
                                        data-original-title="@lang('app.download')"
                                        class="btn btn-outline-dark"><i
                                            class="fa fa-download"></i></a>

                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-10">
                                    @lang('messages.noFileUploaded')
                                </div>
                            </div>
                        </li>
                    @endforelse

                </ul>
            </div>
            <!--/row-->
        @endif
    </div>
