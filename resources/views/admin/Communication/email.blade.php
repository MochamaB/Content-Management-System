@extends('layouts.admin.admin')

@section('content')
<div class="row" style="margin-left:0px">
    <div class="col-3 tab" style="padding:0px;">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
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
    <div class="col-9 tabcontent ">

        <div class="tab-content" id="v-pills-tabContent">
            @foreach($tabContents as $index => $content)
            <div class="tab-pane fade @if($loop->first) show active @endif" id="v-pills-{{ $loop->iteration }}" role="tabpanel" aria-labelledby="v-pills-{{ $loop->iteration }}-tab">
                {!! $content !!}
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection