        <!-- property area -->
        <style>
            .item-thumb img {
                width: 100%;
                /* Make the image take 100% of the container width */
                height: 150px;
                /* Set a consistent height for all images */
                object-fit: cover;
                /* Ensures the image covers the area without distortion */
                display: block;
                /* Ensures no extra space below the image */
            }
        </style>

        <div class="content-area recent-property" style="background-color: #FCFCFC; padding-bottom: 55px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1 col-sm-12 text-center page-title">
                        <!-- /.feature title -->
                        <h2>Top Featured Properties</h2>
                        <p>These are the main properties that are currently available for sale or occupancy. Check them out below.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="proerty-th">
                        @forelse ($properties as $property)
                        <div class="col-sm-12 col-md-{{ count($properties) == 1 ? '6' : '4' }} p0">
                            <div class="box-two proerty-item">
                                <div class="item-thumb">
                                    @if($property->sliders->isNotEmpty())
                                    <a href="{{ url('/properties/'.$property->id)}}">
                                        <img src="{{ $property->sliders->first()->getFirstMediaUrl('slider', 'thumb') }}" alt="Property Image">
                                    </a>
                                    @else
                                    <a href="{{ url('/properties/'.$property->id)}}">
                                        <img src="{{ url('uploads/default/defaultproperty2.jpg') }}" alt="Default Property Image">
                                    </a>
                                    @endif
                                </div>
                                <div class="item-entry overflow">
                                    <h5>
                                        <a href="">{{ $property->property_name }}</a>
                                    </h5>
                                    <div class="dot-hr"></div>
                                    <span class="pull-left"><b>Type :</b> {{ $property->propertyType->property_category }}</span>
                                    <span class="proerty-price pull-right"><i class="pe-7s-map-marker strong"> </i>
                                        {{$property->property_location}}, {{$property->property_streetname}}
                                    </span>
                                </div>
                            </div>
                        </div>
                         <!-- "Show All Properties" box -->
                         <div class="col-sm-12 col-md-3 p0">
                            <div class="box-tree more-proerty text-center">
                                <div class="item-tree-icon">
                                    <i class="fa fa-th"></i>
                                </div>
                                <div class="more-entry overflow">
                                    <h5><a href="">CAN'T DECIDE?</a></h5>
                                    <h5 class="tree-sub-ttl">Show all properties</h5>
                                    <a href="{{ url('/properties')}}" class="btn border-btn more-black">All properties</a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <!-- Show message when no properties are available -->
                        <div class="col-12 text-center">
                            <h4>No properties available at the moment.</h4>
                        </div>
                        @endforelse

                       

                    </div> <!-- /.proerty-th -->
                </div> <!-- /.row -->
            </div> <!-- /.container -->
        </div>