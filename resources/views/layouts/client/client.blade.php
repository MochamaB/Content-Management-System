<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
@include('layouts.client.header')

<body>

    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>
    <!-- Body content -->

    @include('layouts.client.navbar')

    <div class="col-md-12" style="padding:0px">
        @yield('content')
    </div>
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
                                <li><a href="properties.html">Home</a> </li>
                                <li><a href="#">For sale</a> </li>
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
                        <span> (C) <a href="http://www.realsys.co.ke">{{$sitesettings->company_name ?? 'Company Name'}}</a> , All rights reserved. Developed by Real Sys Solutions </span>
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

    @include('layouts.client.footer')

</body>

</html>