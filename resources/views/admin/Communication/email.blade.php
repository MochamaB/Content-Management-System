@extends('layouts.admin.admin')

@section('content')

<div class="row" style="margin-left:0px">
    <div class="col-4 notificationtab pt-1" style="padding:0px;">
        <div class="float-left search btn-group" style="padding:15px 15px;">
          <input class="form-control form-control-sm" id="search-input" type="search" style="width:200px" placeholder="Search" autocomplete="off">
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
                    $backgroundColor =$notification->read_at ? 'white' : '#F4F5F7';
                @endphp

                <a class="list-group-item list-group-item-action {{ $loop->first ? 'active' : '' }} notification-item"
                    id="email-{{ $notification->id }}-list" 
                    data-id="{{ $notification->id }}" 
                    data-read="{{ $notification->read_at ? 'true' : 'false' }}"
                    data-toggle="list" 
                    href="#email-{{ $notification->id }}" 
                    role="tab" 
                    aria-controls="email-{{ $notification->id }}"
                    style="@if(!$loop->first) background-color: {{ $backgroundColor }}; @endif" 
                    onclick="markAsRead(this, event)"> <!-- Apply background color except when active -->
                   <p><b class="subject">{{ $subject }}</b></p>
                    <div class="d-flex w-100 justify-content-between">
                        <p class="mb-1 from">{{ $from }}</p>
                        <p class="date">{{ $date }}</p>
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

<script>
function markAsRead(element) {
    var notificationId = $(element).data('id');

       // Remove active class and reset background color for all list-group-item links
    $('.list-group-item').each(function() {
        $(this).removeClass('active');
        $(this).css('background-color', $(this).data('read') ? 'white' : '#F4F5F7'); // Reset background to the original read/unread state
    });
        // Add active class to the clicked link
        $(element).addClass('active');
        $(element).css('background-color', '#0d6efd');

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
$(document).ready(function() {
    // Search input keyup event
    $('#search-input').on('keyup', function() {
        var searchValue = $(this).val().toLowerCase();

        // Loop through the notifications and filter them
        $('.notification-item').filter(function() {
            // Get subject and sender values
            var subject = $(this).find('.subject').text().toLowerCase();
            var from = $(this).find('.from').text().toLowerCase();
            var date = $(this).find('.date').text().toLowerCase();

            // Toggle visibility based on matching search
            $(this).toggle(subject.includes(searchValue) || from.includes(searchValue) || date.includes(searchValue));
        });
    });
});

</script>

@endsection