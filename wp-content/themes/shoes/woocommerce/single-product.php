<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header(); ?>

<div class="content details-page">
	<div class="product-details">
		<div class="wrap">
	
			<?php
				/**
				 * woocommerce_before_main_content hook
				 *
				 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
				 * @hooked woocommerce_breadcrumb - 20
				 */
				//do_action( 'woocommerce_before_main_content' );
			?>
			<ul class="product-head">
				<li><a href="#">SẢN PHẨM</a> <span>::</span></li>
				<li class="active-page"><a href="#"><?php  the_title();?></a></li>
				<div class="clear"> </div>
			</ul>
			<!----details-product-slider--->
            <!-- Include the Etalage files -->
                <link rel="stylesheet" href="<?php echo bloginfo('template_directory');?>/css/etalage.css">
                <script src="<?php echo bloginfo('template_directory');?>/js/jquery.etalage.min.js"></script>
                <!-- Include the Etalage files -->
                <script>
                    jQuery(document).ready(function($){

                        $('#etalage').etalage({
                            thumb_image_width: 300,
                            thumb_image_height: 400,
                            source_image_width: 900,
                            source_image_height: 1000,
                            show_hint: true,
                            click_callback: function(image_anchor, instance_id){
                                alert('Callback example:\nYou clicked on an image with the anchor: "'+image_anchor+'"\n(in Etalage instance: "'+instance_id+'")');
                            }
                        });
                        // This is for the dropdown list example:
                        $('.dropdownlist').change(function(){
                            etalage_show( $(this).find('option:selected').attr('class') );
                        });

                    });
                </script>
				<!----//details-product-slider--->
				
				<!--Thông tin chi tiết sản phẩm---->
					<div class="details-left">
						
								<?php while ( have_posts() ) : the_post(); ?>

									<?php wc_get_template_part( 'content', 'single-product' ); ?>
									

								<?php endwhile; // end of the loop. ?>
						
						
							<div class="summary entry-summary details-right-info">
								<!-----Thông tin chi ti-------->
								<?php
									/**
									 * woocommerce_single_product_summary hook
									 *
									 * @hooked woocommerce_template_single_title - 5
									 * @hooked woocommerce_template_single_rating - 10
									 * @hooked woocommerce_template_single_price - 10
									 * @hooked woocommerce_template_single_excerpt - 20
									 * @hooked woocommerce_template_single_add_to_cart - 30
									 * @hooked woocommerce_template_single_meta - 40
									 * @hooked woocommerce_template_single_sharing - 50
									 */
									do_action( 'woocommerce_single_product_summary' );
									//do_action( 'woocommerce_template_single_price' );
									
								?>
							
							</div><!-- .summary -->
						<div class="clear"></div>
					
					</div><!---End details-left--->
				
					<div class="details-right">
						<a href="#">SEE MORE</a>
					</div>
					<div class="clear"> </div>
		</div>
		
		<div class="product-reviwes">
			<div class="wrap">
			<!--vertical Tabs-script-->
                <!---responsive-tabs---->
                <script src="<?php echo bloginfo('template_directory');?>/js/easyResponsiveTabs.js" type="text/javascript"></script>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#horizontalTab').easyResponsiveTabs({
                            type: 'default', //Types: default, vertical, accordion
                            width: 'auto', //auto or any width like 600px
                            fit: true,   // 100% fit in a container
                            closed: 'accordion', // Start closed if in accordion view
                            activate: function(event) { // Callback function if tab is switched
                                var $tab = $(this);
                                var $info = $('#tabInfo');
                                var $name = $('span', $info);
                                $name.text($tab.text());
                                $info.show();
                            }
                        });

                        $('#verticalTab').easyResponsiveTabs({
                            type: 'vertical',
                            width: 'auto',
                            fit: true
                        });
                    });
                </script>
            <!---//responsive-tabs---->
            <!--//vertical Tabs-script-->
            <!--vertical Tabs-->
            <div id="verticalTab">
                <ul class="resp-tabs-list">
                    <li>Chi tiết sản phẩm</li>
                    <li>Product tags</li>
                    <li>Product reviews</li>
                </ul>
                <div class="resp-tabs-container vertical-tabs">
                    <div>                       
                        <p><?php the_content();?></p>
                    </div>
                    <div>
                        <h3>Product Tags</h3>
                        <h4>Add Your Tags:</h4>
                        <form>
                            <input type="text"> <input type="submit" value="ADD TAGS"/>
                            <span>Use spaces to separate tags. Use single quotes (') for phrases.</span>
                        </form>
                    </div>
                    <div>
                        <h3>Customer Reviews</h3>
                        <p>There are no customer reviews yet.</p>
                    </div>
                </div>
            </div>
            <div class="clear"> </div>
            <!--- start-similar-products--->
                <div class="similar-products">
                    <div class="similar-products-left">
                        <h3>Sản phẩm liên quan</h3>
                        <p>Quý khách vui lòng liên hệ để được hỗ trợ.</p>
                    </div>
					
					<div class="similar-products-right">
						 <!-- start content_slider -->
						<!--- start-rate---->
						<script src="<?php echo bloginfo('template_directory');?>/js/jstarbox.js"></script>
						<link rel="stylesheet" href="<?php echo bloginfo('template_directory');?>/css/jstarbox.css" type="text/css" media="screen" charset="utf-8" />
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

						<!--Slide details--->
						<div id="owl-demo" class="owl-carousel owl-theme" style="opacity: 1; display: block;">

							<?php do_shortcode('[test_shortcode]');?>
                          
						</div><!--- //End-owl-demo&owl-carowsel--->

						<!----//End-img-cursual---->
					</div><!--- //End-similar-products-right--->
                        <div class="clear"> </div>
                    </div> <!--- //End-similar-products--->
                </div><!---//End-wrap--->
            </div><!---//End-product-reviews--->

            <div class="clear"> </div>
            <!--//vertical Tabs-->
            <!----//product-rewies---->
			
			</div>
		</div>

				<?php
					/**
					 * woocommerce_after_main_content hook
					 *
					 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
					 */
					 
					do_action( 'woocommerce_after_main_content' );
				?>
				
	
		<?php
			/**
			 * woocommerce_sidebar hook
			 *
			 * @hooked woocommerce_get_sidebar - 10
			 */
			do_action( 'woocommerce_sidebar' );
			
		?>
	</div><!--End product-details --->
</div><!--End Content - details-page --->

<?php include(TEMPLATEPATH . '/inc/grids.php');?>
<?php get_footer( ); ?>