        <!--TESTIMONIALS -->
        <div class="testimonial-area recent-property" style="background-color: #FCFCFC; padding-bottom: 15px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1 col-sm-12 text-center page-title">
                        <!-- /.feature title -->
                        <h2>Our Customers Said  </h2> 
                    </div>
                </div>

                <div class="row">
                    <div class="row testimonial">
                        <div class="col-md-12">
                            <div id="testimonial-slider">
                                @foreach($testimonial as $item)
                                <div class="item">
                                    <div class="client-text">                                
                                        <p>{{$item->testimonial}}</p>
                                        <h4><strong>{{$item->client_name}}, </strong><i>{{$item->client_title}}</i></h4>
                                    </div>
                                    <div class="client-face wow fadeInRight" data-wow-delay=".9s"> 
                                    <img src="{{ url('resources/uploads/images/'. ($item->client_picture ?? 'noimage.jpg')) }}" style="height: 88px; width: 88px;">
                                    </div>
                                </div>
                               @endforeach
                    
                            
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>