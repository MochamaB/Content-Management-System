@extends('layouts.admin.admin')

@section('content')

<div class="row" style="margin-left:0px">
    <div class="col-4 notificationtab pt-1" style="padding:0px;">
        <div class="float-left search btn-group" style="padding:15px 10px;">
          <input class="form-control form-control-sm" type="search" style="width:200px" placeholder="Search" autocomplete="off">
          <a href="{{url('email/create')}}" class="btn btn-warning btn-lg text-white mb-0 me-0" id="">
          <i class="mdi mdi-pen"></i>  
          Compose</a>
        </div>
    @if($inboxNotifications->isEmpty())
    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">     
                <a class="list-group-item list-group-item-action" >
                   <h6>No emails in Inbox</h6>
                    <div class="d-flex w-100 justify-content-between">
                        <p class="mb-1">None</p>
                        <p>None</p>
                    </div>
                </a>
        </div>
        @else
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                @foreach($inboxNotifications as $index => $notification)
                @php
                    $data = json_decode($notification->data, true);
                    $subject = $data['subject'] ?? 'No Subject';
                    $from = $data['user_email'] ?? 'Unknown';
                    $date = $notification->created_at->format('M d, Y');
                    $backgroundColor =!$loop->first && $notification->read_at ? 'white' : '#F4F5F7';
                @endphp

                <a class="list-group-item list-group-item-action {{ $loop->first ? 'active' : '' }}"
                    id="email-{{ $notification->id }}-list" 
                    data-id="{{ $notification->id }}" 
                    data-toggle="list" 
                    href="#email-{{ $notification->id }}" 
                    role="tab" 
                    aria-controls="email-{{ $notification->id }}"
                    style="@if(!$loop->first) active background-color: {{ $notification->read_at ? 'white' : '#F4F5F7' }}; @endif"
                    onclick="markAsRead(this, event)"> <!-- Apply background color except when active -->
                   <p><b>{{ $subject }}</b></p>
                    <div class="d-flex w-100 justify-content-between">
                        <p class="mb-1">{{ $from }}</p>
                        <p>{{ $date }}</p>
                    </div>
                </a>
            @endforeach
            <div class="mt-3" style="padding: 0px 10px;">
        {{ $mailNotifications->links('pagination::bootstrap-5') }}
        </div>
        </div>
      
        @endif
    </div>
    <div class="col-8 notificationtabcontent " style="padding:10px 20px">

        <div class="tab-content" id="nav-tabContent">
        @if($inboxNotifications->isEmpty())
            <h4 style="color:blue; text-align:center;padding-top:15px"> <i>There are no emails in the inbox</i></h4>
            
            <!-- Centering the image -->
            <div style="display: flex; justify-content: center; align-items: center; height: 100%;">
                <img class="img-fluid" style="width:450px; height:300px;" src="{{ url('uploads/vectors/noemails.svg') }}">
            </div>
        @else
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
                    <div class="email-body" style="padding:20px;background-color:#F4F5F7">
                        {!! $body !!}
                    </div>
                </div>
            @endforeach
            @endif
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
<script>
function markAsRead(element) {
    var notificationId = $(element).data('id');

        // Remove active class from all list-group-item links
        $('.list-group-item').removeClass('active');

        // Add active class to the clicked link
        $(element).addClass('active');

    $.ajax({
        url: '/notification/mark-as-read/' + notificationId,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), // Include CSRF token
        },
        success: function(response) {
            if (response.status === 'success' && !$(element).hasClass('active')) {
                $(element).css('background-color', 'white');
            }
        },
        error: function(response) {
            console.error('Error:', response);
        }
    });
}

</script>

@endsection