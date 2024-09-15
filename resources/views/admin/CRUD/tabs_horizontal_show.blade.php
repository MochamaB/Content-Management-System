
<ul class="nav nav-tabs mb-0" id="fx1" role="tablist">
@foreach($tabTitles as $index => $title)
        <li class="nav-item " role="presentation">
        <a class="nav-link @if($loop->first) active @endif" 
            id="fx1-tab-{{ $loop->iteration }}" 
            data-bs-toggle="tab" href="#fx1-tabs-{{ $loop->iteration }}" 
            role="tab" aria-controls="fx1-tabs-{{ $loop->iteration }}" 
            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
            data-tab="{{ $title }}"
            style="font-size:0.95rem;padding:0px 20px 14px 20px">

                {{ $title }} ({{ $tabCounts[$title] ?? 0 }})
            </a>
        </li>
    @endforeach
</ul>


<div class="tab-content" id="fx1-content" style="padding-top:1.6rem;">
            <!----------- ------------------>
            @foreach($tabContents as $index => $content)
            <div class="tab-pane fade @if($loop->first) show active @endif" 
            id="fx1-tabs-{{ $loop->iteration }}" 
            role="tabpanel" 
            aria-labelledby="fx1-tab-{{ $loop->iteration }}">
            {!! $content !!}
        </div>

        <!----------- ------------------>  
        @endforeach
</div>

