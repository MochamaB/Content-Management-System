<!-- Footer area-->


<script src="{{ asset('styles/client/assets/js/jquery-1.10.2.min.js') }}"></script>
<script src="{{ asset('styles/client/assets/js/modernizr-2.6.2.min.js') }}"></script>


<script src="{{ asset('styles/client/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('styles/client/assets/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('styles/client/assets/js/bootstrap-hover-dropdown.js') }}"></script>

<script src="{{ asset('styles/client/assets/js/easypiechart.min.js') }}"></script>
<script src="{{ asset('styles/client/assets/js/jquery.easypiechart.min.js') }}"></script>

<script src="{{ asset('styles/client/assets/js/owl.carousel.min.js') }}"></script>

<script src="{{ asset('styles/client/assets/js/wow.js') }}"></script>

<script src="{{ asset('styles/client/assets/js/icheck.min.js') }}"></script>
<script src="{{ asset('styles/client/assets/js/price-range.js') }}"></script>


<script src="{{ asset('styles/client/assets/js/jquery.ba-cond.min.js') }}"></script>
<script src="{{ asset('styles/client/assets/js/jquery.slitslider.js') }}"></script>
<script src="{{ asset('styles/client/assets/js/lightslider.min.js') }}"></script>

<script src="{{ asset('styles/client/assets/js/main.js') }}"></script>

<script type="text/javascript">
    $(function() {

        var Page = (function() {

            var $nav = $('#nav-dots > span'),
                slitslider = $('#slider').slitslider({
                    onBeforeChange: function(slide, pos) {

                        $nav.removeClass('nav-dot-current');
                        $nav.eq(pos).addClass('nav-dot-current');

                    }
                }),
                init = function() {

                    initEvents();

                },
                initEvents = function() {

                    $nav.each(function(i) {

                        $(this).on('click', function(event) {

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

            return {
                init: init
            };

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
@stack('scripts')


