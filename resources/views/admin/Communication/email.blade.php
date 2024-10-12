@extends('layouts.admin.admin')

@section('content')

<div class="row" style="margin-left:0px">
    <div class="col-4 notificationtab pt-1" style="padding:0px;">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                @foreach($inboxNotifications as $index => $notification)
                @php
                    $data = json_decode($notification->data, true);
                    $subject = $data['subject'] ?? 'No Subject';
                    $from = $data['user_email'] ?? 'Unknown';
                    $date = $notification->created_at->format('M d, Y');
                @endphp
                <a class="list-group-item list-group-item-action @if($loop->first) active @endif" 
                   id="email-{{ $notification->id }}-list" 
                   data-toggle="list" 
                   href="#email-{{ $notification->id }}" 
                   role="tab" 
                   aria-controls="email-{{ $notification->id }}">
                   <h6>{{ $subject }}</h6>
                    <div class="d-flex w-100 justify-content-between">
                        <p class="mb-1">{{ $from }}</p>
                        <p>{{ $date }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    <div class="col-8 notificationtabcontent " style="padding:10px 20px">

        <div class="tab-content" id="nav-tabContent">
        @foreach($inboxNotifications as $index => $notification)
                @php
                    $data = json_decode($notification->data, true);
                    $subject = $data['subject'] ?? 'No Subject';
                    $from = $data['from'] ?? 'Unknown';
                    $to = $data['user_email'] ?? 'Unknown';
                    $body = $data['body'] ?? 'No content';
                    $date = $notification->created_at->format('M d, Y H:i:s');
                @endphp
                <div class="tab-pane fade @if($loop->first) show active @endif" 
                     id="email-{{ $notification->id }}" 
                     role="tabpanel" 
                     aria-labelledby="email-{{ $notification->id }}-list">
                    <h5>{{ $subject }}</h5>
                    <p><strong>From:</strong> {{$sitesettings->company_name }}</p>
                    <p><strong>To:</strong> {{ $to }}</p>
                    <p><strong>Date:</strong> {{ $date }}</p>
                    <hr>
                    <div class="email-body">
                        {!! $body !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<form action="{{ url('notification/text/sendText') }}" method="POST" style="display: inline;">
    @csrf
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