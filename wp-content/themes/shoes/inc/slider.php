<!----start-image-slider---->
	<div class="img-slider">
		<div class="wrap">
            <ul id="jquery-demo">
				<?php
					$args = array( 'post_type' => 'product', 'stock' => 1, 'posts_per_page' => 3,  'orderby' =>'rand','order' => 'DESC' );
					$loop = new WP_Query( $args );
					while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>

							<li class="producthome">   							

							<?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" width="65px" height="115px" />'; ?>
							<div class="slider-detils">	
									<h3><?php the_title(); ?>
									<span></span>
									</h3>

									   
									<span class="price"><?php //echo $product->get_price_html(); ?></span>

								
								<!--nút Xem thông tin chi tiết sản phẩm--->
								<?php woocommerce_template_loop_add_to_cart( $loop->post, $product ); ?>
							</div>
				<?php endwhile; ?>
				<?php wp_reset_query(); ?>
			</ul>
		</div>
	</div>
	<div class="clear"> </div>
<!----//End-image-slider---->
<!----start-price-rage---
	<div class="wrap">
		<div class="price-rage">
			<h3>Weekly selection:</h3>
			<div id="slider-range">
			</div>
		</div>
	</div>
<!----//End-price-rage--->