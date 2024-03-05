
        
          <!-- Footer area-->
<div class="footer-area">

<div class=" footer">
    <div class="container">
        <div class="row">

            <div class="col-md-3 col-sm-6 wow fadeInRight animated">
                <div class="single-footer">
                    <h4>About us </h4>
                    <div class="footer-title-line"></div>

                    <img src="assets/img/footer-logo.png" alt="" class="wow pulse" data-wow-delay="1s">
                    <p>{{$sitesettings->company_aboutus ?? 'Enter small description about the company'}}.</p>
                    <ul class="footer-adress">
                        <li><i class="pe-7s-map-marker strong"> </i> {{$sitesettings->company_location ?? 'KILIMANI'}}</li>
                        <li><i class="pe-7s-mail strong"> </i> {{$sitesettings->company_email ?? 'email@yourcompany.com'}}</li>
                        <li><i class="pe-7s-call strong"> </i> {{$sitesettings->company_telephone ?? '0711 111 111'}}</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 wow fadeInRight animated">
                <div class="single-footer">
                    <h4>Quick links </h4>
                    <div class="footer-title-line"></div>
                    <ul class="footer-menu">
                        <li><a href="properties.html">Home</a>  </li> 
                        <li><a href="#">For sale</a>  </li> 
                        <li><a href="submit-property.html">For Rent </a></li> 
                        <li><a href="contact.html">Contact us</a></li> 
            
                    </ul>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 wow fadeInRight animated">
                <div class="single-footer">
                    <h4>Last News</h4>
                    <div class="footer-title-line"></div>
                    <ul class="footer-blog">
                       

                        

                


                    </ul>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 wow fadeInRight animated">
                <div class="single-footer news-letter">
                    <h4>Stay in touch</h4>
                    <div class="footer-title-line"></div>
                    <p>Enter email to subscribe to our news letter.</p>

                    <form>
                        <div class="input-group">
                            <input class="form-control" type="text" placeholder="E-mail ... ">
                            <span class="input-group-btn">
                                <button class="btn btn-primary subscribe" type="button"><i class="pe-7s-paper-plane pe-2x"></i></button>
                            </span>
                        </div>
                        <!-- /input-group -->
                    </form> 

                    <div class="social pull-right"> 
                        <ul>
                            <li><a class="wow fadeInUp animated" href="https://twitter.com/"><i class="fa fa-twitter"></i></a></li>
                            <li><a class="wow fadeInUp animated" href="https://www.facebook.com/" data-wow-delay="0.2s"><i class="fa fa-facebook"></i></a></li>
                            <li><a class="wow fadeInUp animated" href="https://plus.google.com/" data-wow-delay="0.3s"><i class="fa fa-google-plus"></i></a></li>
                            <li><a class="wow fadeInUp animated" href="https://instagram.com/" data-wow-delay="0.4s"><i class="fa fa-instagram"></i></a></li>
                            <li><a class="wow fadeInUp animated" href="https://instagram.com/" data-wow-delay="0.6s"><i class="fa fa-dribbble"></i></a></li>
                        </ul> 
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="footer-copy text-center">
    <div class="container">
        <div class="row">
            <div class="pull-left">
                <span> (C) <a href="http://www.KimaroTec.com">{{$sitesettings->company_name ?? 'Company Name'}}</a> , All rights reserved. Developed by Bridgetech </span> 
            </div> 
            <div class="bottom-menu pull-right"> 
                <ul> 
                    <li><a class="wow fadeInUp animated" href="#" data-wow-delay="0.2s">Home</a></li>
                    <li><a class="wow fadeInUp animated" href="#" data-wow-delay="0.3s">Property</a></li>
                    <li><a class="wow fadeInUp animated" href="#" data-wow-delay="0.4s">Faq</a></li>
                    <li><a class="wow fadeInUp animated" href="#" data-wow-delay="0.6s">Contact</a></li>
                </ul> 
            </div>
        </div>
    </div>
</div>

</div>


<script src="{{ url('styles/client/assets/js/modernizr-2.6.2.min.js') }}"></script>

<script src="{{ url('styles/client/assets/js/jquery-1.10.2.min.js') }}"></script>
<script src="{{ url('styles/client/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ url('styles/client/assets/js/bootstrap-select.min.js') }}"></script>
<script src="{{ url('styles/client/assets/js/bootstrap-hover-dropdown.js') }}"></script>

<script src="{{ url('styles/client/assets/js/easypiechart.min.js') }}"></script>
<script src="{{ url('styles/client/assets/js/jquery.easypiechart.min.js') }}"></script>

<script src="{{ url('styles/client/assets/js/owl.carousel.min.js') }}"></script>        

<script src="{{ url('styles/client/assets/js/wow.js') }}"></script>

<script src="{{ url('styles/client/assets/js/icheck.min.js') }}"></script>
<script src="{{ url('styles/client/assets/js/price-range.js') }}"></script>


<script src="{{ url('styles/client/assets/js/jquery.ba-cond.min.js') }}"></script>
<script src="{{ url('styles/client/assets/js/jquery.slitslider.js') }}"></script>

<script src="{{ url('styles/client/assets/js/main.js') }}"></script>

<script type="text/javascript">
                $(function () {

                    var Page = (function () {

                        var $nav = $('#nav-dots > span'),
                                slitslider = $('#slider').slitslider({
                            onBeforeChange: function (slide, pos) {

                                $nav.removeClass('nav-dot-current');
                                $nav.eq(pos).addClass('nav-dot-current');

                            }
                        }),
                                init = function () {

                                    initEvents();

                                },
                                initEvents = function () {

                                    $nav.each(function (i) {

                                        $(this).on('click', function (event) {

                                            var $dot = $(this);

                                            if (!slitslider.isActive()) {

                                                $nav.removeClass('nav-dot-current');
                                                $dot.addClass('nav-dot-current');

                                            }

                                            slitslider.jump(i + 1);
                                            return false;

                                        });

                                    });

                                };

                        return {init: init};

                    })();

                    Page.init();

                    /**
                     * Notes: 
                     * 
                     * example how to add items:
                     */

                    /*
                     
                     var $items  = $('<div class="sl-slide sl-slide-color-2" data-orientation="horizontal" data-slice1-rotation="-5" data-slice2-rotation="10" data-slice1-scale="2" data-slice2-scale="1"><div class="sl-slide-inner bg-1"><div class="sl-deco" data-icon="t"></div><h2>some text</h2><blockquote><p>bla bla</p><cite>Margi Clarke</cite></blockquote></div></div>');
                     
                     // call the plugin's add method
                     ss.add($items);
                     
                     */

                });
</script>
</body>


</body>
</html>