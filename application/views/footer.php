<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
    <footer class="sticky-footer">
        <div class="container">
            <div class="text-center">
                <small>Copyright © 2018. All Rights Reserved</small>
            </div>
        </div>
    </footer>
</div>

<div class="modal fade" id="customModal" tabindex="-1" role="dialog" aria-labelledby="customModalLabel" aria-hidden="true" ng-blur="customModalData.action.close()">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customModalLabel">{{customModalData.content.header}}</h5>
                <button type="button" class="close" data-dismiss="modal" ng-click="customModalData.action.close()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{customModalData.content.body}}
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" ng-click="customModalData.action.confirm()">{{customModalData.content.confirmName}}</button>
                <button type="button" class="btn btn-secondary" ng-click="customModalData.action.close()" data-dismiss="modal">{{customModalData.content.closeName}}</button>
            </div>
        </div>
    </div>
</div>

<!-- CSS spinner taken from http://cssload.net -->
<style>
.cssload-fond{
	position:fixed;
    z-index:9999;
    width:100%;
    height:100%;
    top:0;bottom:0;
    background-color:rgba(0.2,0.2,0.2,0.5);
}

.cssload-container-general
{
	animation:cssload-animball_two 1.15s infinite;
		-o-animation:cssload-animball_two 1.15s infinite;
		-ms-animation:cssload-animball_two 1.15s infinite;
		-webkit-animation:cssload-animball_two 1.15s infinite;
		-moz-animation:cssload-animball_two 1.15s infinite;
	width:43px; height:43px;
}
.cssload-internal
{
	width:43px; height:43px; position:absolute;
}
.cssload-ballcolor
{
	width: 19px;
	height: 19px;
	border-radius: 50%;
}
.cssload-ball_1, .cssload-ball_2, .cssload-ball_3, .cssload-ball_4
{
	position: absolute;
	animation:cssload-animball_one 1.15s infinite ease;
		-o-animation:cssload-animball_one 1.15s infinite ease;
		-ms-animation:cssload-animball_one 1.15s infinite ease;
		-webkit-animation:cssload-animball_one 1.15s infinite ease;
		-moz-animation:cssload-animball_one 1.15s infinite ease;
}
.cssload-ball_1
{
	background-color:rgb(203,32,37);
	top:0; left:0;
}
.cssload-ball_2
{
	background-color:rgb(248,179,52);
	top:0; left:23px;
}
.cssload-ball_3
{
	background-color:rgb(0,160,150);
	top:23px; left:0;
}
.cssload-ball_4
{
	background-color:rgb(151,191,13);
	top:23px; left:23px;
}

@keyframes cssload-animball_one
{
	0%{ position: absolute;}
	50%{top:12px; left:12px; position: absolute;opacity:0.5;}
	100%{ position: absolute;}
}

@-o-keyframes cssload-animball_one
{
	0%{ position: absolute;}
	50%{top:12px; left:12px; position: absolute;opacity:0.5;}
	100%{ position: absolute;}
}

@-ms-keyframes cssload-animball_one
{
	0%{ position: absolute;}
	50%{top:12px; left:12px; position: absolute;opacity:0.5;}
	100%{ position: absolute;}
}

@-webkit-keyframes cssload-animball_one
{
	0%{ position: absolute;}
	50%{top:12px; left:12px; position: absolute;opacity:0.5;}
	100%{ position: absolute;}
}

@-moz-keyframes cssload-animball_one
{
	0%{ position: absolute;}
	50%{top:12px; left:12px; position: absolute;opacity:0.5;}
	100%{ position: absolute;}
}

@keyframes cssload-animball_two
{
	0%{transform:rotate(0deg) scale(1);}
	50%{transform:rotate(360deg) scale(1.3);}
	100%{transform:rotate(720deg) scale(1);}
}

@-o-keyframes cssload-animball_two
{
	0%{-o-transform:rotate(0deg) scale(1);}
	50%{-o-transform:rotate(360deg) scale(1.3);}
	100%{-o-transform:rotate(720deg) scale(1);}
}

@-ms-keyframes cssload-animball_two
{
	0%{-ms-transform:rotate(0deg) scale(1);}
	50%{-ms-transform:rotate(360deg) scale(1.3);}
	100%{-ms-transform:rotate(720deg) scale(1);}
}

@-webkit-keyframes cssload-animball_two
{
	0%{-webkit-transform:rotate(0deg) scale(1);}
	50%{-webkit-transform:rotate(360deg) scale(1.3);}
	100%{-webkit-transform:rotate(720deg) scale(1);}
}

@-moz-keyframes cssload-animball_two
{
	0%{-moz-transform:rotate(0deg) scale(1);}
	50%{-moz-transform:rotate(360deg) scale(1.3);}
	100%{-moz-transform:rotate(720deg) scale(1);}
}
</style>

<div ng-if="busy" align="center" class="cssload-fond">
	<div class="cssload-container-general" style="margin-top:40vh">
			<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_1"> </div></div>
			<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_2"> </div></div>
			<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_3"> </div></div>
			<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_4"> </div></div>
	</div>
</div>


</body>
<script>
(function($) {
  "use strict"; // Start of use strict
  // Configure tooltips for collapsed side navigation
  $('.navbar-sidenav [data-toggle="tooltip"]').tooltip({
    template: '<div class="tooltip navbar-sidenav-tooltip" role="tooltip" style="pointer-events: none;"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
  })
  // Toggle the side navigation
  $("#sidenavToggler").click(function(e) {
    e.preventDefault();
    $("body").toggleClass("sidenav-toggled");
    $(".navbar-sidenav .nav-link-collapse").addClass("collapsed");
    $(".navbar-sidenav .sidenav-second-level, .navbar-sidenav .sidenav-third-level").removeClass("show");
  });
  // Force the toggled class to be removed when a collapsible nav link is clicked
  $(".navbar-sidenav .nav-link-collapse").click(function(e) {
    e.preventDefault();
    $("body").removeClass("sidenav-toggled");
  });
  // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
  $('body.fixed-nav .navbar-sidenav, body.fixed-nav .sidenav-toggler, body.fixed-nav .navbar-collapse').on('mousewheel DOMMouseScroll', function(e) {
    var e0 = e.originalEvent,
      delta = e0.wheelDelta || -e0.detail;
    this.scrollTop += (delta < 0 ? 1 : -1) * 30;
    e.preventDefault();
  });
  // Scroll to top button appear
  $(document).scroll(function() {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
      $('.scroll-to-top').fadeIn();
    } else {
      $('.scroll-to-top').fadeOut();
    }
  });
  // Configure tooltips globally
  $('[data-toggle="tooltip"]').tooltip()
  // Smooth scrolling using jQuery easing
  $(document).on('click', 'a.scroll-to-top', function(event) {
    var $anchor = $(this);
    $('html, body').stop().animate({
      scrollTop: ($($anchor.attr('href')).offset().top)
    }, 1000, 'easeInOutExpo');
    event.preventDefault();
  });
})(jQuery); // End of use strict
</script>

</html>
