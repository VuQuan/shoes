<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php echo bloginfo('charset'); ?>" />
	
	<?php if (is_search()) { ?>
	   <meta name="robots" content="noindex, nofollow" /> 
	<?php } ?>

	<title>
		   <?php
		      if (function_exists('is_tag') && is_tag()) {
		         single_tag_title("Tag Archive for &quot;"); echo '&quot; - '; }
		      elseif (is_archive()) {
		         wp_title(''); echo ' Archive - '; }
		      elseif (is_search()) {
		         echo 'Search for &quot;'.wp_specialchars($s).'&quot; - '; }
		      elseif (!(is_404()) && (is_single()) || (is_page())) {
		         wp_title(''); echo ' - '; }
		      elseif (is_404()) {
		         echo 'Not Found - '; }
		      if (is_home()) {
		         bloginfo('name'); echo ' - '; bloginfo('description'); }
		      else {
		          bloginfo('name'); }
		      if ($paged>1) {
		         echo ' - page '. $paged; }
		   ?>
	</title>
	
	<link rel="shortcut icon" href="/favicon.ico">
	
	
	
	<link rel="pingback" href="<?php echo bloginfo('pingback_url'); ?>">

	<?php if ( is_singular() ) wp_enqueue_script('comment-reply'); ?>

	<?php wp_head(); ?>


    <!--is_single()--->


	<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
	<!---webfonts---->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
	<!----//webfonts---->
	<!----start-alert-scroller---->
	<script src="<?php echo bloginfo('template_directory');?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo bloginfo('template_directory');?>/js/jquery.easy-ticker.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#demo').hide();
		$('.vticker').easyTicker();
	});
	</script>
	<!----start-alert-scroller---->
	<link href="<?php echo bloginfo('template_directory');?>/css/jstarbox.css" rel="stylesheet" type="text/css" media="all" />
	<script src="<?php echo bloginfo('template_directory');?>/js/jstarbox.js" type="text/javascript"></script>
	
	<!-- start menu -->
	<link href="<?php echo bloginfo('template_directory');?>/css/megamenu.css" rel="stylesheet" type="text/css" media="all" />
	<script type="text/javascript" src="<?php echo bloginfo('template_directory');?>/js/megamenu.js"></script>
	<script>$(document).ready(function(){$(".megamenu").megamenu();});</script>
	<script src="<?php echo bloginfo('template_directory');?>/js/menu_jquery.js"></script>
	<!-- //End menu -->
	<!---slider---->
	<link rel="stylesheet" href="<?php echo bloginfo('template_directory');?>/css/slippry.css">
	<script src="<?php echo bloginfo('template_directory');?>/js/jquery-ui.js" type="text/javascript"></script>
	<script src="<?php echo bloginfo('template_directory');?>/js/scripts-f0e4e0c2.js" type="text/javascript"></script>
	<script>
		  jQuery('#jquery-demo').slippry({
		  // general elements & wrapper
		  slippryWrapper: '<div class="sy-box jquery-demo" />', // wrapper to wrap everything, including pager
		  // options
		  adaptiveHeight: false, // height of the sliders adapts to current slide
		  useCSS: false, // true, false -> fallback to js if no browser support
		  autoHover: false,
		  transition: 'fade'
		});
	</script>
	<!----start-pricerage-seletion---->
	<script type="text/javascript" src="<?php echo bloginfo('template_directory');?>/js/jquery-ui.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_directory');?>/css/jquery-ui.css">
	<script type='text/javascript'>//<![CDATA[ 
		$(window).load(function(){
		 $( "#slider-range" ).slider({
					range: true,
					min: 0,
					max: 500,
					values: [ 100, 400 ],
					slide: function( event, ui ) {  $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
					}
		 });
		$( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) + " - $" + $( "#slider-range" ).slider( "values", 1 ) );
		
		});//]]>  
	</script>
	<!----//End-pricerage-seletion---->
	<!---move-top-top---->
	<script type="text/javascript" src="<?php echo bloginfo('template_directory');?>/js/move-top.js"></script>
	<script type="text/javascript" src="<?php echo bloginfo('template_directory');?>/js/easing.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(".scroll").click(function(event){		
				event.preventDefault();
				$('html,body').animate({scrollTop:$(this.hash).offset().top},1200);
			});
		});
	</script>
	<!---//move-top-top---->

    <!-- Owl Carousel Assets -->
    <link href="<?php echo bloginfo('template_directory');?>/css/owl.carousel.css" rel="stylesheet" type="text/css" />
    <!-- Prettify -->
    <script src="<?php echo bloginfo('template_directory');?>/js/owl.carousel.js"></script>
    <script>
        $(document).ready(function() {

            $("#owl-demo").owlCarousel({
                items : 3,
                lazyLoad : true,
                autoPlay : true,
                navigation : true,
                navigationText : ["",""],
                rewindNav : false,
                scrollPerPage : false,
                pagination : false,
                paginationNumbers: false
            });

        });
    </script>
    <!-- //Owl Carousel Assets -->
	<link rel="stylesheet" href="<?php echo bloginfo('stylesheet_url'); ?>">
	<link href="<?php echo bloginfo('template_directory');?>/css/woocommerce_shoes.css" rel="stylesheet" type="text/css" media="all" />
</head>

<body <?php body_class(); ?>>
	
	<!---start-wrap---->
		<!---start-header---->
		<div class="header">
			<div class="top-header">
				<div class="wrap">
					<div class="top-header-left">
						<ul>
							<!---cart-tonggle-script---->
							<script type="text/javascript">
								$(function(){
									var $cart = $('#cart');
										$('#clickme').click(function(e) {
										 e.stopPropagation();
									   if ($cart.is(":hidden")) {
										   $cart.slideDown("slow");
									   } else {
										   $cart.slideUp("slow");
									   }
									});
									$(document.body).click(function () {
									   if ($cart.not(":hidden")) {
										   $cart.slideUp("slow");
									   } 
									});
									});
							</script>
							<!---//cart-tonggle-script---->
							<li><a class="cart" href="#"><span id="clickme"> </span></a></li>
							<!---start-cart-bag---->
							<div id="cart">Your Cart is Empty <span>(0)</span></div>
							<!---start-cart-bag---->
							<li><a class="info" href="#"><span> </span></a></li>
						</ul>
					</div>

					<div class="top-header-center">
						<div class="top-header-center-alert-left">
							<h3>HÀNG GIÁ SỐC</h3>
						</div>
						<div class="top-header-center-alert-right">
							<div class="vticker">
							  <ul>
								  <li>Giày lười cao cấp. <label>Sale online 70%.</label></li>
							  </ul>
							</div>
						</div>
						<div class="clear"> </div>
					</div>
					<div class="top-header-right">
						<ul>
							<li><a href="login.html">Login</a><span> </span></li>
							<li><a href="register.html">Join</a></li>
						</ul>
					</div>
					<div class="clear"> </div>
				</div>
			</div>

			<!----start-mid-head---->
			<div class="mid-header">
				<div class="wrap">
					<div class="mid-grid-left">
						<form>
							<input type="text" placeholder="Bạn muốn tìm sản phẩm nào ?" />
						</form>
					</div>
					<div class="mid-grid-right">
						<a class="logo" href="index.html"><span> </span></a>
					</div>
					<div class="clear"> </div>
				</div>
			</div>
			<!----//End-mid-head---->

			<!----start-bottom-header---->
			<div class="header-bottom">
				<div class="wrap">
				<!-- start header menu -->
                    <?php
                        wp_nav_menu(array(
                            'menu'=>'Page Menu',
                            'container'=>false,
                            'menu_class'=>'megamenu skyblue',
                            'depth'=>0,
                            'walker'=>new Description_Walker()
                        ));
                    ?>

                 <!-- end header menu -->
                </div>
			</div>
			</div>
			<!----//End-bottom-header---->
		<!---//End-header---->