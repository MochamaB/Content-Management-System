<body>

    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>
    <!-- Body content -->

    <div class="header-connect">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-sm-8  col-xs-12">
                    <div class="header-half header-call">
                        <p>
                            <span><i class="pe-7s-call"></i> {{$sitesettings->company_telephone ?? '0711 111 111'}}</span>
                            <span><i class="pe-7s-mail"></i> {{$sitesettings->company_email ?? 'info@yourcompany'}}</span>
                        </p>
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-5  col-sm-3 col-sm-offset-1  col-xs-12">
                    <div class="header-half header-social">
                        <ul class="list-inline">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-vine"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-dribbble"></i></a></li>
                            <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End top header -->

    <nav class="navbar navbar-default ">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navigation">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}">
                    @if (isset($sitesettings) && method_exists($sitesettings, 'getFirstMediaUrl'))
                    <img src="{{ $sitesettings->getFirstMediaUrl('logo')}}" alt="Logo" style="height: 70px; width: 150px;">
                    @else
                    <img src="{{ url('uploads/images/default_logo.png') }}" alt="No Image" style="height: 70px; width: 150px;">
                    @endif
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse yamm" id="navigation">
                <div class="button navbar-right">
                    <button class="navbar-btn nav-button wow bounceInRight login" onclick="window.location = ('{{ route('login') }}')" data-wow-delay="0.4s">Login</button>
                    <button class="navbar-btn nav-button wow bounceInRight register" onclick="window.location = ('{{ route('register') }}')" data-wow-delay="0.4s">Registration</button>
                </div>
                <ul class="main-nav nav navbar-nav navbar-right">
                    <li class="wow fadeInDown" data-wow-delay="0.1s"><a class="" href="{{ url('/')}}">Home</a></li>

                    <li class="wow fadeInDown" data-wow-delay="0.1s"><a class="" href="">For Rent</a></li>
                    <li class="wow fadeInDown" data-wow-delay="0.1s"><a class="" href="">For Sale</a></li>

                    <li class="wow fadeInDown" data-wow-delay="0.4s"><a href="contact.html">Contact</a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <!-- End of nav bar -->