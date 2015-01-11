<?php get_header(); ?>

<?php include(TEMPLATEPATH . '/inc/slider.php');?>



<!--- start-content---->
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
                </div>
                <div class="content-right">
                    <div class="product-grids">
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
                        <!---caption-script---->
                        <!---//caption-script---->

                    <!---Product One--->
                    <?php   $shoes_nike = new WP_Query("showposts=6&cat=7&orderby=rand");
                            while($shoes_nike->have_posts()): $shoes_nike->the_post();
                    ?>
                        <div onclick="location.href='details.html';" class="product-grid fade">
                            <div class="product-grid-head">
                                <ul class="grid-social">
                                    <li><a class="facebook" href="#"><span> </span></a></li>
                                    <li><a class="twitter" href="#"><span> </span></a></li>
                                    <li><a class="googlep" href="#"><span> </span></a></li>
                                    <div class="clear"> </div>
                                </ul>
                                <div class="block">
                                    <div class="starbox small ghosting"> </div> <span> (46)</span>
                                </div>
                            </div>
                            <div class="product-pic">
                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                                <p>
                                    <a href="<?php the_permalink(); ?>"><small><?php the_title();?></small></a>
                                    <span></span>
                                </p>
                            </div>
                            <div class="product-info">
                                <div class="product-info-cust">
                                    <a href="details.html">Details</a>
                                </div>
                                <div class="product-info-price">
                                    <a href="details.html">&#163; 380</a>
                                </div>
                                <div class="clear"> </div>
                            </div>
                            <div class="more-product-info">
                                <span> </span>
                            </div>
                        </div>
                        <?php endwhile; wp_reset_postdata();?>
                    <!---Product One--->

                        <div class="clear"> </div>
                        </div>
                </div>

            <!--End content-right-->
			<div class="clear"> </div>
		</div>
	</div>
	
<!--- //End-content---->

<?php include(TEMPLATEPATH .'/inc/grids.php');?>

<?php get_footer(); ?>