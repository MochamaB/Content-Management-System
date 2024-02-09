
<div class="slide-2">
            
            <div id="slider" class="sl-slider-wrapper">
            @foreach($slider as $item)   
                <div class="sl-slider">
                
                    <div class="sl-slide" data-orientation="horizontal" data-slice1-rotation="-25" data-slice2-rotation="-25" data-slice1-scale="2" data-slice2-scale="2">
                     
                    <div class="sl-slide-inner ">

                            <div class="bg-img bg-img-1" 
                            style="background-image: url('{{ $item->getFirstMediaUrl('slider') }}')">
                            </div>                             
                            <blockquote><cite><a href="property.html">{{$item->slider_title}}</a></cite>
                                <p>{{$item->slider_desc}}
                                </p>
                                <span class="pull-left"><b> Area :</b> 120m </span>
                                <span class="proerty-price pull-right"> $ 300,000</span>
                                <div class="property-icon">
                                    <img src="assets/img/icon/bed.png">(5)|
                                    <img src="assets/img/icon/shawer.png">(2)|
                                    <img src="assets/img/icon/cars.png">(1)  
                                </div>
                            </blockquote>
                        </div>
                    </div> 
                   

               
                </div><!-- /sl-slider -->
                @endforeach
                <nav id="nav-dots" class="nav-dots">
                    <span class="nav-dot-current"></span>
                   
                </nav>
            </div><!-- /slider-wrapper -->
            
        </div>
      