
<ul class="nav nav-tabs mb-3" id="ex1" role="tablist" >
@foreach($tabTitles as $index => $title)
        <li class="nav-item" role="presentation">
        <a class="nav-link @if($loop->first) active @endif" id="ex1-tab-{{ $loop->iteration }}" data-bs-toggle="tab" href="#ex1-tabs-{{ $loop->iteration }}" role="tab" aria-controls="ex1-tabs-{{ $loop->iteration }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                {{ $title }}
            </a>
        </li>
    @endforeach
</ul>


<div class="tab-content" id="ex1-content">
            <!----------- ------------------>
            @foreach($tabContents as $index => $content)
            <div class="tab-pane fade @if($loop->first) show active @endif" id="ex1-tabs-{{ $loop->iteration }}" role="tabpanel" aria-labelledby="ex1-tab-{{ $loop->iteration }}">
            {!! $content !!}
        </div>

        <!----------- ------------------>  
        @endforeach
</div>