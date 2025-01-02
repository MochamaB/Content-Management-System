@extends('layouts.client.client')

@section('content')
<style>
#image-gallery li {
    width: 100%;
    height: 350px; /* Adjust to desired height */
    overflow: hidden; /* Hide extra content */
}

#image-gallery img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensure consistent dimensions */
}
</style>
<div class="page-head"> 
            <div class="container">
                <div class="row">
                    <div class="page-head-content">
                        <h1 class="page-title">All Properties</h1>               
                    </div>
                </div>
            </div>
        </div>

<div class="content-area blog-page padding-top-40" style="background-color: #FCFCFC; padding-bottom: 55px;">
    <div class="container">
        @forelse($properties as $property)
        <div class="row">
            <div class="blog-lst col-md-9">
                <section class="post">
                    <div>
                        <h2 class="wow fadeInLeft animated">{{$property->property_name}}</h2>
                        <div class="title-line wow fadeInRight animated"></div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <p class="author-category">
                                Slogan<a href="#"> {{$property->property_slogan ?? 'Spacious units available'}}</a>
                                
                            </p>
                        </div>
                        <div class="col-sm-6 right">
                            <p class="date-comments">
                                <a href="single.html"><i class="fa fa-home"></i> Number of Units {{$property->units->count()}}</a>
                               
                            </p>
                        </div>
                    </div>
                    <div class="image wow fadeInLeft animated" style="margin-bottom:-20px">
                        <div class="light-slide-item">
                            <div class="clearfix">
                                <div class="favorite-and-print">
                                    <a class="add-to-fav" href="#login-modal" data-toggle="modal">
                                        <i class="fa fa-star-o"></i>
                                    </a>
                                    <a class="printer-icon " href="javascript:window.print()">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </div>

                                <ul id="image-gallery" class="gallery list-unstyled cS-hidden">
                                    @if($property->sliders->isNotEmpty())
                                    @foreach($property->sliders as $slider)
                                    <li data-thumb="{{ $slider->getFirstMediaUrl('slider', 'thumb') }}">
                                        <a href="{{ url('/properties/'.$property->id)}}">
                                            <img src="{{ $slider->getFirstMediaUrl('slider', 'thumb') }}" alt="Property Image">
                                        </a>
                                    </li>
                                    @endforeach
                                    @else
                                    <!-- Default Image -->
                                    <li>
                                        <a href="{{ route('property.show', $property->id) }}">
                                            <img src="{{ url('uploads/default/defaultproperty2.jpg') }}" alt="Default Property Image">
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                   
                    <p class="read-more">
                        <a href="{{ url('/properties/'.$property->id)}}" class="btn btn-default btn-border">Go To Property <i class="fa fa-arrow-circle-right"></i></a>
                    </p>
                </section>

            </div>

            <!---- Right Side --->
            <div class="blog-asside-right col-md-3">
                <div class="panel panel-default sidebar-menu wow fadeInRight animated">
                    <div class="panel-heading">
                        <h3 class="panel-title">Property Details</h3>
                    </div>
                    <div class="panel-body text-widget">
                    <p><span class=""><b>Type :</b> {{ $property->propertyType->property_category }} - {{ $property->propertyType->property_type }}</span></p>
                    <p><span class=""><b>Location :</b> {{ $property->property_location }} {{ $property->property_streetname }}</span></p>
                    <p><span class=""><b>Number of Listings :</b> {{ $property->units->count() }} </span></p>
                        <p>
                        <span class=""><b>Description :</b>
                        {{ $property->property_description }}
                        </p>

                    </div>
                </div>

            </div>
        </div>
        @empty
        <!-- Show message when no properties are available -->
        <div class="col-12 text-center">
            <h4>No properties available at the moment.</h4>
        </div>
        @endforelse


    </div>
</div>
@push('scripts')
<script>
     $(document).ready(function () {

$('#image-gallery').lightSlider({
    gallery: true,
    item: 1,
    thumbItem: 9,
    slideMargin: 0,
    speed: 500,
    auto: true,
    loop: true,
    onSliderLoad: function () {
        $('#image-gallery').removeClass('cS-hidden');
    }
});
});
</script>
@endpush

@endsection