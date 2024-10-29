@extends('layouts.admin.admin')

@section('content')
<div class=" contwrapper">

   
    <h4 style="text-transform: capitalize;"><b> Notification Center</b></h4>
    <hr>
    <!-- Button to open the modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#sendTextModal">
    Test Sending Message Modal
</button>

<!-- Modal -->
<form action="{{ url('notification/text/sendText') }}" method="POST" style="display: inline;">
    @csrf
    <div class="modal fade" id="sendTextModal" tabindex="-1" role="dialog" aria-labelledby="sendTextModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#ffaf00;">
                    <h5 class="modal-title" id="sendTextModalLabel" style="color:white;">Send Message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 40px; color: white;">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form content goes here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-warning btn-lg text-warning mb-0 me-0" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-lg text-white mb-0 me-0">Send</button>
                </div>
            </div>
        </div>
    </div>
</form>



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
                            <a href="{{ url('textmessage') }}" class="genlink d-flex align-items-center text-decoration-none">
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