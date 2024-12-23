<style>
 .progress-ring {
    width: 150px;
    height: 150px;
}

.progress-ring-circle {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    position: relative;
}

.progress-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    height: 80%;
    background: white;
    border-radius: 50%;
}
</style>
</style>
<div class="d-flex justify-content-between align-items-start">
    <div>
        <h5 class="card-title card-title-dash pt-1"><b>{{$title ?? ''}}</b></h5>
    </div>
    <div>

    </div>
</div>
<div class="circular-progress-container">
    <div class="position-relative d-flex justify-content-center align-items-center">
        <div class="progress-ring" data-value="{{ $percentage ?? 0 }}">
            <div class="progress-ring-circle" style="background: conic-gradient(#4CAF50 {{ $percentage ?? 0 }}%, #f0f0f0 0);">
                <div class="progress-center d-flex flex-column align-items-center justify-content-center">
                    <span class="progress-value h3 mb-0">{{ number_format($percentage ?? 0, 1) }}%</span>
                    <small class="text-muted">{{ $title ?? '' }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    
</script>
