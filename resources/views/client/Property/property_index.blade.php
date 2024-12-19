@extends('layouts.client.client')

@section('content')

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
                                By <a href="#">John Snow</a>
                                in <a href="blog.html">Webdesign</a>
                            </p>
                        </div>
                        <div class="col-sm-6 right">
                            <p class="date-comments">
                                <a href="single.html"><i class="fa fa-calendar-o"></i> June 20, 2013</a>
                                <a href="single.html"><i class="fa fa-comment-o"></i> 8 Comments</a>
                            </p>
                        </div>
                    </div>
                    <div class="image wow fadeInLeft animated">
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

                                <ul id="image-gallery" class="gallery list-unstyled cS-hidden" style="max-height:300px">
                                    @if($property->sliders->isNotEmpty())
                                    @foreach($property->sliders as $slider)
                                    <li data-thumb="{{ $slider->getFirstMediaUrl('slider', 'thumb') }}">
                                        <a href="{{ route('property.show', $property->id) }}">
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
                        <a href="single.html" class="btn btn-default btn-border">Continue reading</a>
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
                        <p>Improved own provided blessing may peculiar domestic. Sight house has sex never. No visited raising gravity outward subject my cottage mr be. Hold do at tore in park feet near my case.
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

@endsection