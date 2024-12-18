<div class="slide-2">
    <div id="slider" class="sl-slider-wrapper">
        <!-- Single sl-slider container -->
        <div class="sl-slider">
            @foreach($slider as $item)
            <div class="sl-slide"
                data-orientation="horizontal"
                data-slice1-rotation="-25"
                data-slice2-rotation="-25"
                data-slice1-scale="2"
                data-slice2-scale="2">

                <div class="sl-slide-inner">
                    <div class="bg-img bg-img-1"
                        style="background-image: url('{{ $item->getFirstMediaUrl('slider','original') }}')">
                    </div>
                    <blockquote>
                        <cite><a href="property.html">{{ $item->slider_title }}</a></cite>
                        <p>{{ $item->slider_desc }}</p>
                        <span class="proerty-price pull-right"><i class="pe-7s-map-marker strong"> </i>
                        {{$item->property->property_location}}, {{$item->property->property_streetname}}
                        </span>
                        <div class="property-icon">

                        </div>


                    </blockquote>
                </div>
            </div><!-- /sl-slide -->
            @endforeach
        </div><!-- /sl-slider -->
        <nav id="nav-arrows" class="nav-arrows">
            <span class="nav-arrow-prev">Previous</span>
            <span class="nav-arrow-next">Next</span>
        </nav>

        <!-- Navigation Dots -->
        <nav id="nav-dots" class="nav-dots">
            @foreach($slider as $key => $item)
            <span class="{{ $key == 0 ? 'nav-dot-current' : '' }}"></span>
            @endforeach
        </nav>
    </div><!-- /slider-wrapper -->
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        var slider = $('#slider').slitslider({
            // Configuration options
        });

        if (slider) {
            console.log('SlitSlider initialized successfully.');
        } else {
            console.log('SlitSlider initialization failed.');
        }
    });
</script>
@endpush