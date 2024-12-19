@extends('layouts.client.client')

@section('content')

<div class="content-area blog-page padding-top-40" style="background-color: #FCFCFC; padding-bottom: 55px;">
    <div class="container">
        <div class="row">
            <div class="blog-lst col-md-9">
                <section class="post">
                    <div>
                        <h2 class="wow fadeInLeft animated">FASHIN NOW 2015</h2>
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
                        <a href="single.html">
                            <img src="assets/img/blog2.jpg" class="img-responsive" alt="Example blog post alt">
                        </a>
                    </div>
                    <p class="intro">Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>
                    <p class="read-more">
                        <a href="single.html" class="btn btn-default btn-border">Continue reading</a>
                    </p>
                </section>

            </div>

            <!---- Right Side --->
            <div class="blog-asside-right col-md-3">
                <div class="panel panel-default sidebar-menu wow fadeInRight animated">
                    <div class="panel-heading">
                        <h3 class="panel-title">Text widget</h3>
                    </div>
                    <div class="panel-body text-widget">
                        <p>Improved own provided blessing may peculiar domestic. Sight house has sex never. No visited raising gravity outward subject my cottage mr be. Hold do at tore in park feet near my case.
                        </p>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection