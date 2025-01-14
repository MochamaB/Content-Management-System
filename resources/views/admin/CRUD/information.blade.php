@if(isset($message))
<div class="media alert-info mb-3 p-3" style="border-left: 5px solid #0000ff;">
    <i class="ti-info-alt icon-md text-information d-flex align-self-start me-2 mb-3"></i>
    <div class="media-body">
        <p class="card-text">{!! $message !!}</p>
    </div>
</div>
@endif