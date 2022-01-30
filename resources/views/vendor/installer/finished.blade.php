@extends('vendor.installer.layouts.master')

<style>
    #paragraph {
        text-align: center;
    }
</style>

@section('title', trans('installer_messages.final.title'))
@section('container')
    <p class="paragraph" id="paragraph">{{ session('message')['message'] }}</p>
    <div class="buttons">
        <a href="{{ url('/') }}" class="button">{{ trans('installer_messages.final.exit') }}</a>
    </div>
@stop
