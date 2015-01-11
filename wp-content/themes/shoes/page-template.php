<?php
/*
Template Name: Page Template
*/
?>

<?php get_header(); ?>

<?php include(TEMPLATEPATH . '/inc/slider.php');?>

	<div class="content">
		<div class="wrap">
			<div class="content-left">
				<div class="content-left-top-brands">
					<h3>Danh mục</h3>
					<?php wp_nav_menu(array(
						'menu'=>'Menu Sidebar',
						'container'=>''
					));?>
				</div>
			</div><!---//End Content-left--->
			
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
				<div class="post content-right" id="post-<?php the_ID(); ?>">
				
					<!--- start-rate---->
						<script src="js/jstarbox.js"></script>
						<link rel="stylesheet" href="css/jstarbox.css" type="text/css" media="screen" charset="utf-8" />
						<script type="text/javascript">
							jQuery(function() {
								jQuery('.starbox').each(function() {
									var starbox = jQuery(this);
									starbox.starbox({
										average: starbox.attr('data-start-value'),
										changeable: starbox.hasClass('unchangeable') ? false : starbox.hasClass('clickonce') ? 'once' : true,
										ghosting: starbox.hasClass('ghosting'),
										autoUpdateAverage: starbox.hasClass('autoupdate'),
										buttons: starbox.hasClass('smooth') ? false : starbox.attr('data-button-count') || 5,
										stars: starbox.attr('data-star-count') || 5
									}).bind('starbox-value-changed', function(event, value) {
										if(starbox.hasClass('random')) {
											var val = Math.random();
											starbox.next().text(' '+val);
											return val;
										} 
									})
								});
							});
						</script>
					<!---//End-rate---->
					
				
					<div class="product-grids">
						<?php the_content(); ?>	
						<div class="clear"> </div>
					</div>				
				</div><!---//End Content-right--->
				<div class="clear"> </div>
			
			<?php // comments_template(); ?>

			<?php endwhile; endif; ?>

			</div><!---//End Wrap--->
	</div><!---//End Content--->
	
<?php include(TEMPLATEPATH . '/inc/grids.php');?>
<?php get_footer(); ?>