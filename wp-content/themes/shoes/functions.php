<?php

    // Add file custom menu
    include (TEMPLATEPATH . "/inc/custom_nav.php");

	// Add RSS links to <head> section
	automatic_feed_links();
	
	// Load jQuery
	if ( !is_admin() ) {
	   wp_deregister_script('jquery');
	   wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"), false);
	   wp_enqueue_script('jquery');
	}
	
	// Clean up the <head>
	function removeHeadLinks() {
    	remove_action('wp_head', 'rsd_link');
    	remove_action('wp_head', 'wlwmanifest_link');
    }
    add_action('init', 'removeHeadLinks');
    remove_action('wp_head', 'wp_generator');
    
	// Declare sidebar widget zone
    if (function_exists('register_sidebar')) {
    	register_sidebar(array(
    		'name' => 'Left Widgets',
    		'id'   => 'left-widgets',
    		'description'   => 'These are widgets for the sidebar.',
    		'before_widget' => '<div id="%1$s" class="widget %2$s">',
    		'after_widget'  => '</div>',
    		'before_title'  => '<h2>',
    		'after_title'   => '</h2>'
    	));
    }

    //Create menu support for theme
    if(function_exists('register_nav_menu')){
        register_nav_menus(array(
            'main_nav'=>'Main Navigation Menu',
            'quantity_menu'=>'Quantity Dropdown menu'
        ));
    }
	
    /**
	 * Thay đổi tên nhãn nút ADD Cart trong product archives
	 */
	add_filter( 'add_to_cart_text', 'woo_custom_cart_button_text' );                        // < 2.1
	add_filter( 'woocommerce_product_add_to_cart_text', 'woo_custom_cart_button_text' );    // 2.1 +
	  
	function woo_custom_cart_button_text() {
	  
			return __( 'Details', 'woocommerce' );
	  
	}
	/**
	 * Thay đổi tên nhãn nút ADD Cart trong product details
	 */
	add_filter( 'add_to_cart_text', 'woo_custom_cart_button_text' );                                // < 2.1
	add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text2' );    // 2.1 +
	  
	function woo_custom_cart_button_text2() {
	  
			return __( 'Mua hàng', 'woocommerce' );
	  
	}
	
	// Vô  hiệu hóa CSS của woocommerce
	define('WOOCOMMERCE_USE_CSS', false);

	//Xoa nut add cart
	function remove_loop_button(){
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	}
	add_action('init','remove_loop_button');
	

	//Khởi tạo function cho shortcode
	function create_shortcode() {
		   
			  $args = array(
				   'post_type' => 'product',
				   'posts_per_page' => 3
			  );
			  $loop = new WP_Query( $args );
			  if ( $loop->have_posts() )
			  {
				   while ( $loop->have_posts() ) : $loop->the_post();
				   ?> <div class="item" onclick="location.href='details.html';"><ul><?php
						woocommerce_get_template_part( 'content', 'product' );
					?></ul></div><?php
				   endwhile;
			  }
			  else
			  {
				   echo __( 'No products found' );
			  }
			  wp_reset_postdata();
		

	}
	//Tạo shortcode tên là [test_shortcode] và sẽ thực thi code từ function create_shortcode
	add_shortcode( 'test_shortcode', 'create_shortcode' );
	
	
	function shortcode_slide (){
			$args = array(
				'per_page' => '3',
				'columns' => '1'
			);
			$slider = new WP_Query($args);
			while($slider->have_posts()) : $slider->the_post();?>
				<li>
					<?php woocommerce_get_template_part('content2','product');?>
				</li>
		<?php endwhile; wp_reset_postdata();
	}
	add_shortcode('slider','shortcode_slide');
?>
	