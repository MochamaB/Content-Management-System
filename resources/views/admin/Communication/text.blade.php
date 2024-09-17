@extends('layouts.admin.admin')

@section('content')

<form action="{{ url('notification/text/sendText') }}" method="POST" style="display: inline;">
    @csrf
    @method('POST')
    <button type="button" class="btn btn-primary btn-lg text-white mb-0 me-0  float-end" role="button" style="text-transform: capitalize;" style="border:0px;" data-toggle="modal" data-target="#sendTextModal">
        <i class="mdi mdi-message-text-outline"></i> New Text Message</button>

    <div class="modal fade" id="sendTextModal" tabindex="-1" role="dialog" aria-labelledby="sendTextModallLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#ffaf00;">
                    <h5 class="modal-title" id="sendTextModalLabel" style="color:white;">Send Message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 40px; color: white;">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-warning btn-lg text-warning mb-0 me-0" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-lg text-white mb-0 me-0">Send</button>
                </div>
            </div>
        </div>
    </div>
</form>


@endsection