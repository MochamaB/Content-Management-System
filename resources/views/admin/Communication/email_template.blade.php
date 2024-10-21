{{-- In email.template.blade.php --}}
<div class="message-body">
    <div class="sender-details">
        <div class="details">
            
           <p class="defaulttext"> Dear {{$user->firstname ?? 'Firstname'}} {{$user->lastname ?? 'Lastname'}}</p>
        @if (is_array($data)) 
           @foreach ($data as $line => $content)
            @if (!empty($content))
            @if ($line === 'action')
            <div class="text-center">

                <a href="{{url($content) }}" class="btn-primary" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 0px; text-transform: capitalize; background-color: #1F3BB3; margin: 0; border-color: #1F3BB3; border-style: solid; border-width: 8px 16px;">
                    {{ $linkmessage ?? 'GO TO SITE'}}
                </a>
            </div>
            @else
            <p>{{ $content ?? '' }}</p>
            @endif
            @endif
            @endforeach
            @else
            <p>{{ $data ?? '' }}</p> <!-- Display the message directly if it's not an array -->
        @endif
        </div>
    </div>
</div>