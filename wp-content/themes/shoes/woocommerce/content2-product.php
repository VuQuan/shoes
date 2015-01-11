
<?php
	$args = array( 'post_type' => 'product', 'stock' => 1, 'posts_per_page' => 3, 'orderby' =>'date','order' => 'DESC' );
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>

			<li class="abc">    

				
					
					<!--Hiển thị ảnh của sản phẩm--->
					<?php 
					if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); 
					else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" class=""/>'; ?>
					
					<div class="slider-detils">
						<h3><?php the_title(); ?>
						
						<span></span>
						
						</h3>

							<!--Hiển thị giá của sản phẩm--->
								<span class="price"><?php //echo $product->get_price_html(); ?></span>
								
						   <!--Xem thông tin chi tiết sản phẩm--->
						   <?php woocommerce_template_loop_add_to_cart( $loop->post, $product ); ?>
					</div>
				

				
			</li><!-- /span3 -->
<?php endwhile; ?>
<?php wp_reset_query(); ?>

