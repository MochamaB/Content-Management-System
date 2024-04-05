@extends('layouts.admin.admin')

@section('content')
<div class=" contwrapper">

    <a href="" class="btn btn-primary btn-lg text-white mb-0 me-0  float-end" role="button" style="text-transform: capitalize;">
        <i class="mdi mdi-plus-circle-outline"></i>
        Send Notification
    </a><br />
    <h4 style="text-transform: capitalize;"><b> Notification Center</b></h4>
    <hr>

    <div class="row">
        <div class="col-md-6">
            <div id="dragula-left" class="py-2">
                <div class="card mb-2" style="border-left:5px solid blue;margin-bottom:15px !important">
                    <div class="card-body p-3">
                        <div class="media d-flex align-items-center">
                            <a href="{{ url('notification/email') }}" class="genlink d-flex align-items-center text-decoration-none">
                                <i class="ti-email icon-lg me-3"></i>
                                <div class="media-body">
                                    Email Notifications
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card  mb-2" style="border-left:5px solid blue;margin-bottom:15px !important">
                    <div class="card-body p-3">
                        <div class="media d-flex align-items-center">
                            <a href="{{ url('notification/email') }}" class="genlink d-flex align-items-center text-decoration-none">
                                <i class="ti-comment-alt icon-lg me-3"></i>
                                <div class="media-body">
                                    Text Messages
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card  mb-2" style="border-left:5px solid blue;margin-bottom:15px !important">
                    <div class="card-body p-3">
                        <div class="media d-flex align-items-center">
                            <a href="{{ url('notification/email') }}" class="genlink d-flex align-items-center text-decoration-none">
                                <i class="ti-announcement icon-lg me-3"></i>
                                <div class="media-body">
                                    Announcements
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card  mb-2" style="border-left:5px solid blue;margin-bottom:15px !important">
                    <div class="card-body p-3">
                    <div class="media d-flex align-items-center">
                            <a href="{{ url('notification/email') }}" class="genlink d-flex align-items-center text-decoration-none">
                                <i class="ti-mobile icon-lg me-3"></i>
                                <div class="media-body">
                                    Chart and Forum
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-6 d-none d-md-block">
            <img class="img-fluid" src="{{ url('uploads/vectors/texting.png') }}">
        </div>

    </div>



</div>
@endsection