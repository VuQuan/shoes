<?php
/*
Template Name: Products
*/
?>

<?php get_header(); ?>
    <!------------ Service --------------->
    <!--- start-content---->
    <div class="content product-box-main">
        <div class="wrap">
            <div class="content-left">
                <div class="content-left-top-brands">
                    <h3>Categories</h3>
                    <?php wp_nav_menu(array(
                        'menu'=>'Categories Menu',
                        'container'=>''
                    ));?>
                </div>
            </div>
            <div class="content-right product-box">
            <div class="product-box-head">
                <div class="product-box-head-left">
                    <h3>Products <span>(500)</span></h3>
                </div>
                <div class="product-box-head-right">
                    <ul>
                        <li><span>Sort ::</span><a href="#"> </a></li>
                        <li><label> </label> <a href="#"> Popular</a></li>
                        <li><label> </label> <a href="#"> New</a></li>
                        <li><label> </label> <a href="#"> Discount</a></li>
                        <li><span>Price ::</span><a href="#">Low High</a></li>
                    </ul>
                </div>
                <div class="clear"> </div>
            </div>
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

            <?php
                global $post;
                $args = array('numberposts'=>3,'category'=> 7,14, 'orderby'=>'rand');
                $custom_posts = get_posts($args);
                foreach($custom_posts as $post) : setup_postdata($post)
            ?>
            <div class="product-grid fade" onclick="location.href='details.html';">
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
                        <a href="<?php the_permalink(); ?>"><small><?php the_title(); ?></small></a>
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
           <?php endforeach;wp_reset_postdata();?>

            <div class="clear"> </div>
            </div>
            <!----start-load-more-products---->
            <div class="loadmore-products">
                <a href="#">Loadmore</a>
            </div>
            <!----//End-load-more-products---->
            </div>
            <div class="clear"> </div>
        </div>
    </div>
    <!--- end-content---->
    <!------------ End Service --------------->
<?php get_footer(); ?>