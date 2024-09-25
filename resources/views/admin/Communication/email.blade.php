@extends('layouts.admin.admin')

@section('content')

<div class="row" style="margin-left:0px">
    <div class="col-3 tab" style="padding:0px;">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <button class="btn " style="width:70%;margin:15px 20px;line-height: 20px;font-size: 0.85rem;background-color: #ffaf00;"
            role="button" data-toggle="modal" data-target="#sendTextModal">
                Compose Email
            </button>
            @foreach($tabTitles as $index => $title)
            @php
            $isDisabled = ($routeParts[1] === 'create') ? 'disabled' : '';
            @endphp
            <button class="tablinks @if($loop->first) active @endif" id="v-pills-{{ $loop->iteration }}-tab" data-toggle="pill" href="#v-pills-{{ $loop->iteration }}" role="tab" aria-controls="v-pills-{{ $loop->iteration }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" >
                {{ $title }}
            </button>
            @endforeach
        </div>
    </div>
    <div class="col-9 tabcontent " style="padding:0px 0px">

        <div class="tab-content" id="v-pills-tabContent">
            @foreach($tabContents as $index => $content)
            <div class="tab-pane fade @if($loop->first) show active @endif" id="v-pills-{{ $loop->iteration }}" role="tabpanel" aria-labelledby="v-pills-{{ $loop->iteration }}-tab">
                {!! $content !!}
            </div>
            @endforeach
        </div>
    </div>
</div>
<form action="{{ url('notification/text/sendText') }}" method="POST" style="display: inline;">
    @csrf
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