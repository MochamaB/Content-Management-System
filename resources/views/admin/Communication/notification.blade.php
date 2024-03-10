@extends('layouts.admin.admin')

@section('content')

<div class="row email-wrapper wrapper" style="margin-left:0px;margin-right:0px;">
    <div class="mail-sidebar d-none d-lg-block col-md-3 pt-3 bg-white" style="border:1px solid #dee2e6;">
        <div class="menu-bar" style="">
            <ul class="menu-items">
                <li class="compose mb-3"><button class="btn btn-primary btn-lg text-white">Compose</button></li>
                <li class="active"><a class="label" href="#"><i class="ti-email"></i> Inbox</a><span class="badge badge-pill badge-success">8</span></li>
                <li><a class="label" href="#"><i class="ti-share"></i> Sent</a></li>
                <li><a class="label" href="#"><i class="ti-file"></i> Draft</a><span class="badge badge-pill badge-warning">4</span></li>
                <li><a class="label" href="#"><i class="ti-upload"></i> Outbox</a><span class="badge badge-pill badge-danger">3</span></li>
                <li><a class="label" href="#"><i class="ti-star"></i> Starred</a></li>
                <li><a class="label" href="#"><i class="ti-trash"></i> Trash</a></li>
            </ul>
        </div>
    </div>
    @if ($routeParts[1] === 'email' && count($routeParts) === 2)
        @include('admin.communication.email_summary')
     @else
     @include('admin.communication.email_details')
        @endif
</div>
    @endsection