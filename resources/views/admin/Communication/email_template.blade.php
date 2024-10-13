{{-- In email.template.blade.php --}}
<div class="message-body">
    <div class="sender-details">
        <div class="details">
            <p class="msg-subject">
                {{ $heading ?? 'Default Heading' }} 
            </p>
            <p class="message-body">
                {{ $linkmessage ?? 'Default link message' }}
            </p>
            {{-- Loop through data if it's an array --}}
            @if(is_array($data))
                @foreach($data as $line)
                    <p>{{ $line }}</p>
                @endforeach
            @endif
        </div>
    </div>
</div>
