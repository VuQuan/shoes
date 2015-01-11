<?php
   2 /**
   3  * WooCommerce Template
   4  *
   5  * Functions for the templating system.
   6  *
   7  * @author      WooThemes
   8  * @category    Core
   9  * @package     WooCommerce/Functions
  10  * @version     2.1.0
  11  */
  12 
  13 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
  14 
  15 /**
  16  * Handle redirects before content is output - hooked into template_redirect so is_page works.
  17  *
  18  * @return void
  19  */
  20 function wc_template_redirect() {
  21     global $wp_query, $wp;
  22 
  23     // When default permalinks are enabled, redirect shop page to post type archive url
  24     if ( ! empty( $_GET['page_id'] ) && get_option( 'permalink_structure' ) == "" && $_GET['page_id'] == wc_get_page_id( 'shop' ) ) {
  25         wp_safe_redirect( get_post_type_archive_link('product') );
  26         exit;
  27     }
  28 
  29     // When on the checkout with an empty cart, redirect to cart page
  30     elseif ( is_page( wc_get_page_id( 'checkout' ) ) && sizeof( WC()->cart->get_cart() ) == 0 && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ) {
  31         wp_redirect( get_permalink( wc_get_page_id( 'cart' ) ) );
  32         exit;
  33     }
  34 
  35     // Logout
  36     elseif ( isset( $wp->query_vars['customer-logout'] ) ) {
  37         wp_redirect( str_replace( '&amp;', '&', wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ) ) );
  38         exit;
  39     }
  40 
  41     // Redirect to the product page if we have a single product
  42     elseif ( is_search() && is_post_type_archive( 'product' ) && apply_filters( 'woocommerce_redirect_single_search_result', true ) && $wp_query->post_count == 1 ) {
  43         $product = wc_get_product( $wp_query->post );
  44 
  45         if ( $product->is_visible() ) {
  46             wp_safe_redirect( get_permalink( $product->id ), 302 );
  47             exit;
  48         }
  49     }
  50 
  51     // Ensure payment gateways are loaded early
  52     elseif ( is_add_payment_method_page() ) {
  53 
  54         WC()->payment_gateways();
  55 
  56     }
  57 
  58     // Checkout pages handling
  59     elseif ( is_checkout() ) {
  60         // Buffer the checkout page
  61         ob_start();
  62 
  63         // Ensure gateways and shipping methods are loaded early
  64         WC()->payment_gateways();
  65         WC()->shipping();
  66     }
  67 }
  68 add_action( 'template_redirect', 'wc_template_redirect' );
  69 
  70 /**
  71  * When the_post is called, put product data into a global.
  72  *
  73  * @param mixed $post
  74  * @return WC_Product
  75  */
  76 function wc_setup_product_data( $post ) {
  77     unset( $GLOBALS['product'] );
  78 
  79     if ( is_int( $post ) )
  80         $post = get_post( $post );
  81 
  82     if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'product', 'product_variation' ) ) )
  83         return;
  84 
  85     $GLOBALS['product'] = wc_get_product( $post );
  86 
  87     return $GLOBALS['product'];
  88 }
  89 add_action( 'the_post', 'wc_setup_product_data' );
  90 
  91 if ( ! function_exists( 'woocommerce_reset_loop' ) ) {
  92 
  93     /**
  94      * Reset the loop's index and columns when we're done outputting a product loop.
  95      *
  96      * @access public
  97      * @subpackage  Loop
  98      * @return void
  99      */
 100     function woocommerce_reset_loop() {
 101         global $woocommerce_loop;
 102         // Reset loop/columns globals when starting a new loop
 103         $woocommerce_loop['loop'] = $woocommerce_loop['columns'] = '';
 104     }
 105 }
 106 add_filter( 'loop_end', 'woocommerce_reset_loop' );
 107 
 108 /**
 109  * Products RSS Feed.
 110  *
 111  * @access public
 112  * @return void
 113  */
 114 function wc_products_rss_feed() {
 115     // Product RSS
 116     if ( is_post_type_archive( 'product' ) || is_singular( 'product' ) ) {
 117 
 118         $feed = get_post_type_archive_feed_link( 'product' );
 119 
 120         echo '<link rel="alternate" type="application/rss+xml"  title="' . __( 'New products', 'woocommerce' ) . '" href="' . esc_attr( $feed ) . '" />';
 121 
 122     } elseif ( is_tax( 'product_cat' ) ) {
 123 
 124         $term = get_term_by('slug', esc_attr( get_query_var('product_cat') ), 'product_cat');
 125 
 126         $feed = add_query_arg('product_cat', $term->slug, get_post_type_archive_feed_link( 'product' ));
 127 
 128         echo '<link rel="alternate" type="application/rss+xml"  title="' . sprintf(__( 'New products added to %s', 'woocommerce' ), urlencode($term->name)) . '" href="' . esc_attr( $feed ) . '" />';
 129 
 130     } elseif ( is_tax( 'product_tag' ) ) {
 131 
 132         $term = get_term_by('slug', esc_attr( get_query_var('product_tag') ), 'product_tag');
 133 
 134         $feed = add_query_arg('product_tag', $term->slug, get_post_type_archive_feed_link( 'product' ));
 135 
 136         echo '<link rel="alternate" type="application/rss+xml"  title="' . sprintf(__( 'New products tagged %s', 'woocommerce' ), urlencode($term->name)) . '" href="' . esc_attr( $feed ) . '" />';
 137 
 138     }
 139 }
 140 
 141 /**
 142  * Output generator tag to aid debugging.
 143  *
 144  * @access public
 145  * @return void
 146  */
 147 function wc_generator_tag( $gen, $type ) {
 148     switch ( $type ) {
 149         case 'html':
 150             $gen .= "\n" . '<meta name="generator" content="WooCommerce ' . esc_attr( WC_VERSION ) . '">';
 151             break;
 152         case 'xhtml':
 153             $gen .= "\n" . '<meta name="generator" content="WooCommerce ' . esc_attr( WC_VERSION ) . '" />';
 154             break;
 155     }
 156     return $gen;
 157 }
 158 
 159 /**
 160  * Add body classes for WC pages
 161  *
 162  * @param  array $classes
 163  * @return array
 164  */
 165 function wc_body_class( $classes ) {
 166     $classes = (array) $classes;
 167 
 168     if ( is_woocommerce() ) {
 169         $classes[] = 'woocommerce';
 170         $classes[] = 'woocommerce-page';
 171     }
 172 
 173     elseif ( is_checkout() ) {
 174         $classes[] = 'woocommerce-checkout';
 175         $classes[] = 'woocommerce-page';
 176     }
 177 
 178     elseif ( is_cart() ) {
 179         $classes[] = 'woocommerce-cart';
 180         $classes[] = 'woocommerce-page';
 181     }
 182 
 183     elseif ( is_account_page() ) {
 184         $classes[] = 'woocommerce-account';
 185         $classes[] = 'woocommerce-page';
 186     }
 187 
 188     if ( is_store_notice_showing() ) {
 189         $classes[] = 'woocommerce-demo-store';
 190     }
 191 
 192     return array_unique( $classes );
 193 }
 194 
 195 /**
 196  * Adds extra post classes for products
 197  *
 198  * @since 2.1.0
 199  * @param array $classes
 200  * @param string|array $class
 201  * @param int $post_id
 202  * @return array
 203  */
 204 function wc_product_post_class( $classes, $class = '', $post_id = '' ) {
 205     if ( ! $post_id || 'product' !== get_post_type( $post_id ) ) {
 206         return $classes;
 207     }
 208 
 209     $product = wc_get_product( $post_id );
 210 
 211     if ( $product ) {
 212         if ( $product->is_on_sale() ) {
 213             $classes[] = 'sale';
 214         }
 215         if ( $product->is_featured() ) {
 216             $classes[] = 'featured';
 217         }
 218         if ( $product->is_downloadable() ) {
 219             $classes[] = 'downloadable';
 220         }
 221         if ( $product->is_virtual() ) {
 222             $classes[] = 'virtual';
 223         }
 224         if ( $product->is_sold_individually() ) {
 225             $classes[] = 'sold-individually';
 226         }
 227         if ( $product->is_taxable() ) {
 228             $classes[] = 'taxable';
 229         }
 230         if ( $product->is_shipping_taxable() ) {
 231             $classes[] = 'shipping-taxable';
 232         }
 233         if ( $product->is_purchasable() ) {
 234             $classes[] = 'purchasable';
 235         }
 236         if ( isset( $product->product_type ) ) {
 237             $classes[] = "product-type-" . $product->product_type;
 238         }
 239 
 240         // add category slugs
 241         $categories = get_the_terms( $product->id, 'product_cat' );
 242         if ( ! empty( $categories ) ) {
 243             foreach ( $categories as $key => $value ) {
 244                 $classes[] = 'product-cat-' . $value->slug;
 245             }
 246         }
 247 
 248         // add tag slugs
 249         $tags = get_the_terms( $product->id, 'product_tag' );
 250         if ( ! empty( $tags ) ) {
 251             foreach ( $tags as $key => $value ) {
 252                 $classes[] = 'product-tag-' . $value->slug;
 253             }
 254         }
 255 
 256         $classes[] = $product->stock_status;
 257     }
 258 
 259     if ( false !== ( $key = array_search( 'hentry', $classes ) ) ) {
 260         unset( $classes[ $key ] );
 261     }
 262 
 263     return $classes;
 264 }
 265 
 266 /** Template pages ********************************************************/
 267 
 268 if ( ! function_exists( 'woocommerce_content' ) ) {
 269 
 270     /**
 271      * Output WooCommerce content.
 272      *
 273      * This function is only used in the optional 'woocommerce.php' template
 274      * which people can add to their themes to add basic woocommerce support
 275      * without hooks or modifying core templates.
 276      *
 277      * @access public
 278      * @return void
 279      */
 280     function woocommerce_content() {
 281 
 282         if ( is_singular( 'product' ) ) {
 283 
 284             while ( have_posts() ) : the_post();
 285 
 286                 wc_get_template_part( 'content', 'single-product' );
 287 
 288             endwhile;
 289 
 290         } else { ?>
 291 
 292             <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
 293 
 294                 <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
 295 
 296             <?php endif; ?>
 297 
 298             <?php do_action( 'woocommerce_archive_description' ); ?>
 299 
 300             <?php if ( have_posts() ) : ?>
 301 
 302                 <?php do_action('woocommerce_before_shop_loop'); ?>
 303 
 304                 <?php woocommerce_product_loop_start(); ?>
 305 
 306                     <?php woocommerce_product_subcategories(); ?>
 307 
 308                     <?php while ( have_posts() ) : the_post(); ?>
 309 
 310                         <?php wc_get_template_part( 'content', 'product' ); ?>
 311 
 312                     <?php endwhile; // end of the loop. ?>
 313 
 314                 <?php woocommerce_product_loop_end(); ?>
 315 
 316                 <?php do_action('woocommerce_after_shop_loop'); ?>
 317 
 318             <?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
 319 
 320                 <?php wc_get_template( 'loop/no-products-found.php' ); ?>
 321 
 322             <?php endif;
 323 
 324         }
 325     }
 326 }
 327 
 328 /** Global ****************************************************************/
 329 
 330 if ( ! function_exists( 'woocommerce_output_content_wrapper' ) ) {
 331 
 332     /**
 333      * Output the start of the page wrapper.
 334      *
 335      * @access public
 336      * @return void
 337      */
 338     function woocommerce_output_content_wrapper() {
 339         wc_get_template( 'global/wrapper-start.php' );
 340     }
 341 }
 342 if ( ! function_exists( 'woocommerce_output_content_wrapper_end' ) ) {
 343 
 344     /**
 345      * Output the end of the page wrapper.
 346      *
 347      * @access public
 348      * @return void
 349      */
 350     function woocommerce_output_content_wrapper_end() {
 351         wc_get_template( 'global/wrapper-end.php' );
 352     }
 353 }
 354 
 355 if ( ! function_exists( 'woocommerce_get_sidebar' ) ) {
 356 
 357     /**
 358      * Get the shop sidebar template.
 359      *
 360      * @access public
 361      * @return void
 362      */
 363     function woocommerce_get_sidebar() {
 364         wc_get_template( 'global/sidebar.php' );
 365     }
 366 }
 367 
 368 if ( ! function_exists( 'woocommerce_demo_store' ) ) {
 369 
 370     /**
 371      * Adds a demo store banner to the site if enabled
 372      *
 373      * @access public
 374      * @return void
 375      */
 376     function woocommerce_demo_store() {
 377         if ( !is_store_notice_showing() )
 378             return;
 379 
 380         $notice = get_option( 'woocommerce_demo_store_notice' );
 381         if ( empty( $notice ) )
 382             $notice = __( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'woocommerce' );
 383 
 384         echo apply_filters( 'woocommerce_demo_store', '<p class="demo_store">' . $notice . '</p>'  );
 385     }
 386 }
 387 
 388 /** Loop ******************************************************************/
 389 
 390 if ( ! function_exists( 'woocommerce_page_title' ) ) {
 391 
 392     /**
 393      * woocommerce_page_title function.
 394      *
 395      * @param  boolean $echo
 396      * @return string
 397      */
 398     function woocommerce_page_title( $echo = true ) {
 399 
 400         if ( is_search() ) {
 401             $page_title = sprintf( __( 'Search Results: &ldquo;%s&rdquo;', 'woocommerce' ), get_search_query() );
 402 
 403             if ( get_query_var( 'paged' ) )
 404                 $page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'woocommerce' ), get_query_var( 'paged' ) );
 405 
 406         } elseif ( is_tax() ) {
 407 
 408             $page_title = single_term_title( "", false );
 409 
 410         } else {
 411 
 412             $shop_page_id = wc_get_page_id( 'shop' );
 413             $page_title   = get_the_title( $shop_page_id );
 414 
 415         }
 416 
 417         $page_title = apply_filters( 'woocommerce_page_title', $page_title );
 418 
 419         if ( $echo )
 420             echo $page_title;
 421         else
 422             return $page_title;
 423     }
 424 }
 425 
 426 if ( ! function_exists( 'woocommerce_product_loop_start' ) ) {
 427 
 428     /**
 429      * Output the start of a product loop. By default this is a UL
 430      *
 431      * @access public
 432      * @param bool $echo
 433      * @return string
 434      */
 435     function woocommerce_product_loop_start( $echo = true ) {
 436         ob_start();
 437         wc_get_template( 'loop/loop-start.php' );
 438         if ( $echo )
 439             echo ob_get_clean();
 440         else
 441             return ob_get_clean();
 442     }
 443 }
 444 if ( ! function_exists( 'woocommerce_product_loop_end' ) ) {
 445 
 446     /**
 447      * Output the end of a product loop. By default this is a UL
 448      *
 449      * @access public
 450      * @param bool $echo
 451      * @return string
 452      */
 453     function woocommerce_product_loop_end( $echo = true ) {
 454         ob_start();
 455 
 456         wc_get_template( 'loop/loop-end.php' );
 457 
 458         if ( $echo )
 459             echo ob_get_clean();
 460         else
 461             return ob_get_clean();
 462     }
 463 }
 464 if ( ! function_exists( 'woocommerce_taxonomy_archive_description' ) ) {
 465 
 466     /**
 467      * Show an archive description on taxonomy archives
 468      *
 469      * @access public
 470      * @subpackage  Archives
 471      * @return void
 472      */
 473     function woocommerce_taxonomy_archive_description() {
 474         if ( is_tax( array( 'product_cat', 'product_tag' ) ) && get_query_var( 'paged' ) == 0 ) {
 475             $description = wpautop( do_shortcode( term_description() ) );
 476             if ( $description ) {
 477                 echo '<div class="term-description">' . $description . '</div>';
 478             }
 479         }
 480     }
 481 }
 482 if ( ! function_exists( 'woocommerce_product_archive_description' ) ) {
 483 
 484     /**
 485      * Show a shop page description on product archives
 486      *
 487      * @access public
 488      * @subpackage  Archives
 489      * @return void
 490      */
 491     function woocommerce_product_archive_description() {
 492         if ( is_post_type_archive( 'product' ) && get_query_var( 'paged' ) == 0 ) {
 493             $shop_page   = get_post( wc_get_page_id( 'shop' ) );
 494             if ( $shop_page ) {
 495                 $description = wpautop( do_shortcode( $shop_page->post_content ) );
 496                 if ( $description ) {
 497                     echo '<div class="page-description">' . $description . '</div>';
 498                 }
 499             }
 500         }
 501     }
 502 }
 503 
 504 if ( ! function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {
 505 
 506     /**
 507      * Get the add to cart template for the loop.
 508      *
 509      * @access public
 510      * @subpackage  Loop
 511      * @return void
 512      */
 513     function woocommerce_template_loop_add_to_cart( $args = array() ) {
 514         wc_get_template( 'loop/add-to-cart.php' , $args );
 515     }
 516 }
 517 if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {
 518 
 519     /**
 520      * Get the product thumbnail for the loop.
 521      *
 522      * @access public
 523      * @subpackage  Loop
 524      * @return void
 525      */
 526     function woocommerce_template_loop_product_thumbnail() {
 527         echo woocommerce_get_product_thumbnail();
 528     }
 529 }
 530 if ( ! function_exists( 'woocommerce_template_loop_price' ) ) {
 531 
 532     /**
 533      * Get the product price for the loop.
 534      *
 535      * @access public
 536      * @subpackage  Loop
 537      * @return void
 538      */
 539     function woocommerce_template_loop_price() {
 540         wc_get_template( 'loop/price.php' );
 541     }
 542 }
 543 if ( ! function_exists( 'woocommerce_template_loop_rating' ) ) {
 544 
 545     /**
 546      * Display the average rating in the loop
 547      *
 548      * @access public
 549      * @subpackage  Loop
 550      * @return void
 551      */
 552     function woocommerce_template_loop_rating() {
 553         wc_get_template( 'loop/rating.php' );
 554     }
 555 }
 556 if ( ! function_exists( 'woocommerce_show_product_loop_sale_flash' ) ) {
 557 
 558     /**
 559      * Get the sale flash for the loop.
 560      *
 561      * @access public
 562      * @subpackage  Loop
 563      * @return void
 564      */
 565     function woocommerce_show_product_loop_sale_flash() {
 566         wc_get_template( 'loop/sale-flash.php' );
 567     }
 568 }
 569 
 570 if ( ! function_exists( 'woocommerce_get_product_schema' ) ) {
 571 
 572     /**
 573      * Get a products Schema
 574      * @return string
 575      */
 576     function woocommerce_get_product_schema() {
 577         global $product;
 578 
 579         $schema = "Product";
 580 
 581         // Downloadable product schema handling
 582         if ( $product->is_downloadable() ) {
 583             switch ( $product->download_type ) {
 584                 case 'application' :
 585                     $schema = "SoftwareApplication";
 586                 break;
 587                 case 'music' :
 588                     $schema = "MusicAlbum";
 589                 break;
 590                 default :
 591                     $schema = "Product";
 592                 break;
 593             }
 594         }
 595 
 596         return 'http://schema.org/' . $schema;
 597     }
 598 }
 599 
 600 if ( ! function_exists( 'woocommerce_get_product_thumbnail' ) ) {
 601 
 602     /**
 603      * Get the product thumbnail, or the placeholder if not set.
 604      *
 605      * @access public
 606      * @subpackage  Loop
 607      * @param string $size (default: 'shop_catalog')
 608      * @param int $placeholder_width (default: 0)
 609      * @param int $placeholder_height (default: 0)
 610      * @return string
 611      */
 612     function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0  ) {
 613         global $post;
 614 
 615         if ( has_post_thumbnail() )
 616             return get_the_post_thumbnail( $post->ID, $size );
 617         elseif ( wc_placeholder_img_src() )
 618             return wc_placeholder_img( $size );
 619     }
 620 }
 621 
 622 if ( ! function_exists( 'woocommerce_result_count' ) ) {
 623 
 624     /**
 625      * Output the result count text (Showing x - x of x results).
 626      *
 627      * @access public
 628      * @subpackage  Loop
 629      * @return void
 630      */
 631     function woocommerce_result_count() {
 632         wc_get_template( 'loop/result-count.php' );
 633     }
 634 }
 635 
 636 if ( ! function_exists( 'woocommerce_catalog_ordering' ) ) {
 637 
 638     /**
 639      * Output the product sorting options.
 640      *
 641      * @access public
 642      * @subpackage  Loop
 643      * @return void
 644      */
 645     function woocommerce_catalog_ordering() {
 646         global $wp_query;
 647 
 648         if ( 1 == $wp_query->found_posts || ! woocommerce_products_will_display() ) {
 649             return;
 650         }
 651 
 652         $orderby                 = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
 653         $show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
 654         $catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
 655             'menu_order' => __( 'Default sorting', 'woocommerce' ),
 656             'popularity' => __( 'Sort by popularity', 'woocommerce' ),
 657             'rating'     => __( 'Sort by average rating', 'woocommerce' ),
 658             'date'       => __( 'Sort by newness', 'woocommerce' ),
 659             'price'      => __( 'Sort by price: low to high', 'woocommerce' ),
 660             'price-desc' => __( 'Sort by price: high to low', 'woocommerce' )
 661         ) );
 662 
 663         if ( ! $show_default_orderby ) {
 664             unset( $catalog_orderby_options['menu_order'] );
 665         }
 666 
 667         if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
 668             unset( $catalog_orderby_options['rating'] );
 669         }
 670 
 671         wc_get_template( 'loop/orderby.php', array( 'catalog_orderby_options' => $catalog_orderby_options, 'orderby' => $orderby, 'show_default_orderby' => $show_default_orderby ) );
 672     }
 673 }
 674 
 675 if ( ! function_exists( 'woocommerce_pagination' ) ) {
 676 
 677     /**
 678      * Output the pagination.
 679      *
 680      * @access public
 681      * @subpackage  Loop
 682      * @return void
 683      */
 684     function woocommerce_pagination() {
 685         wc_get_template( 'loop/pagination.php' );
 686     }
 687 }
 688 
 689 /** Single Product ********************************************************/
 690 
 691 if ( ! function_exists( 'woocommerce_show_product_images' ) ) {
 692 
 693     /**
 694      * Output the product image before the single product summary.
 695      *
 696      * @access public
 697      * @subpackage  Product
 698      * @return void
 699      */
 700     function woocommerce_show_product_images() {
 701         wc_get_template( 'single-product/product-image.php' );
 702     }
 703 }
 704 if ( ! function_exists( 'woocommerce_show_product_thumbnails' ) ) {
 705 
 706     /**
 707      * Output the product thumbnails.
 708      *
 709      * @access public
 710      * @subpackage  Product
 711      * @return void
 712      */
 713     function woocommerce_show_product_thumbnails() {
 714         wc_get_template( 'single-product/product-thumbnails.php' );
 715     }
 716 }
 717 if ( ! function_exists( 'woocommerce_output_product_data_tabs' ) ) {
 718 
 719     /**
 720      * Output the product tabs.
 721      *
 722      * @access public
 723      * @subpackage  Product/Tabs
 724      * @return void
 725      */
 726     function woocommerce_output_product_data_tabs() {
 727         wc_get_template( 'single-product/tabs/tabs.php' );
 728     }
 729 }
 730 if ( ! function_exists( 'woocommerce_template_single_title' ) ) {
 731 
 732     /**
 733      * Output the product title.
 734      *
 735      * @access public
 736      * @subpackage  Product
 737      * @return void
 738      */
 739     function woocommerce_template_single_title() {
 740         wc_get_template( 'single-product/title.php' );
 741     }
 742 }
 743 if ( ! function_exists( 'woocommerce_template_single_rating' ) ) {
 744 
 745     /**
 746      * Output the product rating.
 747      *
 748      * @access public
 749      * @subpackage  Product
 750      * @return void
 751      */
 752     function woocommerce_template_single_rating() {
 753         wc_get_template( 'single-product/rating.php' );
 754     }
 755 }
 756 if ( ! function_exists( 'woocommerce_template_single_price' ) ) {
 757 
 758     /**
 759      * Output the product price.
 760      *
 761      * @access public
 762      * @subpackage  Product
 763      * @return void
 764      */
 765     function woocommerce_template_single_price() {
 766         wc_get_template( 'single-product/price.php' );
 767     }
 768 }
 769 if ( ! function_exists( 'woocommerce_template_single_excerpt' ) ) {
 770 
 771     /**
 772      * Output the product short description (excerpt).
 773      *
 774      * @access public
 775      * @subpackage  Product
 776      * @return void
 777      */
 778     function woocommerce_template_single_excerpt() {
 779         wc_get_template( 'single-product/short-description.php' );
 780     }
 781 }
 782 if ( ! function_exists( 'woocommerce_template_single_meta' ) ) {
 783 
 784     /**
 785      * Output the product meta.
 786      *
 787      * @access public
 788      * @subpackage  Product
 789      * @return void
 790      */
 791     function woocommerce_template_single_meta() {
 792         wc_get_template( 'single-product/meta.php' );
 793     }
 794 }
 795 if ( ! function_exists( 'woocommerce_template_single_sharing' ) ) {
 796 
 797     /**
 798      * Output the product sharing.
 799      *
 800      * @access public
 801      * @subpackage  Product
 802      * @return void
 803      */
 804     function woocommerce_template_single_sharing() {
 805         wc_get_template( 'single-product/share.php' );
 806     }
 807 }
 808 if ( ! function_exists( 'woocommerce_show_product_sale_flash' ) ) {
 809 
 810     /**
 811      * Output the product sale flash.
 812      *
 813      * @access public
 814      * @subpackage  Product
 815      * @return void
 816      */
 817     function woocommerce_show_product_sale_flash() {
 818         wc_get_template( 'single-product/sale-flash.php' );
 819     }
 820 }
 821 
 822 if ( ! function_exists( 'woocommerce_template_single_add_to_cart' ) ) {
 823 
 824     /**
 825      * Trigger the single product add to cart action.
 826      *
 827      * @access public
 828      * @subpackage  Product
 829      * @return void
 830      */
 831     function woocommerce_template_single_add_to_cart() {
 832         global $product;
 833         do_action( 'woocommerce_' . $product->product_type . '_add_to_cart'  );
 834     }
 835 }
 836 if ( ! function_exists( 'woocommerce_simple_add_to_cart' ) ) {
 837 
 838     /**
 839      * Output the simple product add to cart area.
 840      *
 841      * @access public
 842      * @subpackage  Product
 843      * @return void
 844      */
 845     function woocommerce_simple_add_to_cart() {
 846         wc_get_template( 'single-product/add-to-cart/simple.php' );
 847     }
 848 }
 849 if ( ! function_exists( 'woocommerce_grouped_add_to_cart' ) ) {
 850 
 851     /**
 852      * Output the grouped product add to cart area.
 853      *
 854      * @access public
 855      * @subpackage  Product
 856      * @return void
 857      */
 858     function woocommerce_grouped_add_to_cart() {
 859         global $product;
 860 
 861         wc_get_template( 'single-product/add-to-cart/grouped.php', array(
 862             'grouped_product'    => $product,
 863             'grouped_products'   => $product->get_children(),
 864             'quantites_required' => false
 865         ) );
 866     }
 867 }
 868 if ( ! function_exists( 'woocommerce_variable_add_to_cart' ) ) {
 869 
 870     /**
 871      * Output the variable product add to cart area.
 872      *
 873      * @access public
 874      * @subpackage  Product
 875      * @return void
 876      */
 877     function woocommerce_variable_add_to_cart() {
 878         global $product;
 879 
 880         // Enqueue variation scripts
 881         wp_enqueue_script( 'wc-add-to-cart-variation' );
 882 
 883         // Load the template
 884         wc_get_template( 'single-product/add-to-cart/variable.php', array(
 885                 'available_variations'  => $product->get_available_variations(),
 886                 'attributes'            => $product->get_variation_attributes(),
 887                 'selected_attributes'   => $product->get_variation_default_attributes()
 888             ) );
 889     }
 890 }
 891 if ( ! function_exists( 'woocommerce_external_add_to_cart' ) ) {
 892 
 893     /**
 894      * Output the external product add to cart area.
 895      *
 896      * @access public
 897      * @subpackage  Product
 898      * @return void
 899      */
 900     function woocommerce_external_add_to_cart() {
 901         global $product;
 902 
 903         if ( ! $product->get_product_url() )
 904             return;
 905 
 906         wc_get_template( 'single-product/add-to-cart/external.php', array(
 907                 'product_url' => $product->get_product_url(),
 908                 'button_text' => $product->single_add_to_cart_text()
 909             ) );
 910     }
 911 }
 912 
 913 if ( ! function_exists( 'woocommerce_quantity_input' ) ) {
 914 
 915     /**
 916      * Output the quantity input for add to cart forms.
 917      *
 918      * @param  array $args Args for the input
 919      * @param  WC_Product|null $product
 920      * @param  boolean $echo Whether to return or echo
 921      * @return void|string
 922      */
 923     function woocommerce_quantity_input( $args = array(), $product = null, $echo = true ) {
 924         if ( is_null( $product ) )
 925             $product = $GLOBALS['product'];
 926 
 927         $defaults = array(
 928             'input_name'    => 'quantity',
 929             'input_value'   => '1',
 930             'max_value'     => apply_filters( 'woocommerce_quantity_input_max', '', $product ),
 931             'min_value'     => apply_filters( 'woocommerce_quantity_input_min', '', $product ),
 932             'step'          => apply_filters( 'woocommerce_quantity_input_step', '1', $product )
 933         );
 934 
 935         $args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );
 936 
 937         ob_start();
 938 
 939         wc_get_template( 'global/quantity-input.php', $args );
 940 
 941         if ( $echo ) {
 942             echo ob_get_clean();
 943         } else {
 944             return ob_get_clean();
 945         }
 946     }
 947 }
 948 
 949 if ( ! function_exists( 'woocommerce_product_description_tab' ) ) {
 950 
 951     /**
 952      * Output the description tab content.
 953      *
 954      * @access public
 955      * @subpackage  Product/Tabs
 956      * @return void
 957      */
 958     function woocommerce_product_description_tab() {
 959         wc_get_template( 'single-product/tabs/description.php' );
 960     }
 961 }
 962 if ( ! function_exists( 'woocommerce_product_additional_information_tab' ) ) {
 963 
 964     /**
 965      * Output the attributes tab content.
 966      *
 967      * @access public
 968      * @subpackage  Product/Tabs
 969      * @return void
 970      */
 971     function woocommerce_product_additional_information_tab() {
 972         wc_get_template( 'single-product/tabs/additional-information.php' );
 973     }
 974 }
 975 if ( ! function_exists( 'woocommerce_product_reviews_tab' ) ) {
 976 
 977     /**
 978      * Output the reviews tab content.
 979      *
 980      * @access public
 981      * @subpackage  Product/Tabs
 982      * @return void
 983      */
 984     function woocommerce_product_reviews_tab() {
 985         wc_get_template( 'single-product/tabs/reviews.php' );
 986     }
 987 }
 988 
 989 if ( ! function_exists( 'woocommerce_default_product_tabs' ) ) {
 990 
 991     /**
 992      * Add default product tabs to product pages.
 993      *
 994      * @access public
 995      * @param array $tabs
 996      * @return array
 997      */
 998     function woocommerce_default_product_tabs( $tabs = array() ) {
 999         global $product, $post;
1000 
1001         // Description tab - shows product content
1002         if ( $post->post_content ) {
1003             $tabs['description'] = array(
1004                 'title'    => __( 'Description', 'woocommerce' ),
1005                 'priority' => 10,
1006                 'callback' => 'woocommerce_product_description_tab'
1007             );
1008         }
1009 
1010         // Additional information tab - shows attributes
1011         if ( $product && ( $product->has_attributes() || ( $product->enable_dimensions_display() && ( $product->has_dimensions() || $product->has_weight() ) ) ) ) {
1012             $tabs['additional_information'] = array(
1013                 'title'    => __( 'Additional Information', 'woocommerce' ),
1014                 'priority' => 20,
1015                 'callback' => 'woocommerce_product_additional_information_tab'
1016             );
1017         }
1018 
1019         // Reviews tab - shows comments
1020         if ( comments_open() ) {
1021             $tabs['reviews'] = array(
1022                 'title'    => sprintf( __( 'Reviews (%d)', 'woocommerce' ), get_comments_number( $post->ID ) ),
1023                 'priority' => 30,
1024                 'callback' => 'comments_template'
1025             );
1026         }
1027 
1028         return $tabs;
1029     }
1030 }
1031 
1032 if ( ! function_exists( 'woocommerce_sort_product_tabs' ) ) {
1033 
1034     /**
1035      * Sort tabs by priority
1036      *
1037      * @access public
1038      * @param array $tabs
1039      * @return array
1040      */
1041     function woocommerce_sort_product_tabs( $tabs = array() ) {
1042 
1043         // Make sure the $tabs parameter is an array
1044         if ( ! is_array( $tabs ) ) {
1045             trigger_error( "Function woocommerce_sort_product_tabs() expects an array as the first parameter. Defaulting to empty array." );
1046             $tabs = array( );
1047         }
1048 
1049         // Re-order tabs by priority
1050         if ( ! function_exists( '_sort_priority_callback' ) ) {
1051             function _sort_priority_callback( $a, $b ) {
1052                 if ( $a['priority'] == $b['priority'] )
1053                     return 0;
1054                 return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
1055             }
1056         }
1057 
1058         uasort( $tabs, '_sort_priority_callback' );
1059 
1060         return $tabs;
1061     }
1062 }
1063 
1064 if ( ! function_exists( 'woocommerce_comments' ) ) {
1065 
1066     /**
1067      * Output the Review comments template.
1068      *
1069      * @access public
1070      * @subpackage  Product
1071      * @return void
1072      */
1073     function woocommerce_comments( $comment, $args, $depth ) {
1074         $GLOBALS['comment'] = $comment;
1075         wc_get_template( 'single-product/review.php', array( 'comment' => $comment, 'args' => $args, 'depth' => $depth ) );
1076     }
1077 }
1078 
1079 if ( ! function_exists( 'woocommerce_output_related_products' ) ) {
1080 
1081     /**
1082      * Output the related products.
1083      *
1084      * @access public
1085      * @subpackage  Product
1086      * @return void
1087      */
1088     function woocommerce_output_related_products() {
1089 
1090         $args = array(
1091             'posts_per_page' => 2,
1092             'columns' => 2,
1093             'orderby' => 'rand'
1094         );
1095 
1096         woocommerce_related_products( apply_filters( 'woocommerce_output_related_products_args', $args ) );
1097     }
1098 }
1099 
1100 if ( ! function_exists( 'woocommerce_related_products' ) ) {
1101 
1102     /**
1103      * Output the related products.
1104      *
1105      * @access public
1106      * @param array Provided arguments
1107      * @param bool Columns argument for backwards compat
1108      * @param bool Order by argument for backwards compat
1109      * @return void
1110      */
1111     function woocommerce_related_products( $args = array(), $columns = false, $orderby = false ) {
1112         if ( ! is_array( $args ) ) {
1113             _deprecated_argument( __FUNCTION__, '2.1', __( 'Use $args argument as an array instead. Deprecated argument will be removed in WC 2.2.', 'woocommerce' ) );
1114 
1115             $argsvalue = $args;
1116 
1117             $args = array(
1118                 'posts_per_page' => $argsvalue,
1119                 'columns'        => $columns,
1120                 'orderby'        => $orderby,
1121             );
1122         }
1123 
1124         $defaults = array(
1125             'posts_per_page' => 2,
1126             'columns'        => 2,
1127             'orderby'        => 'rand'
1128         );
1129 
1130         $args = wp_parse_args( $args, $defaults );
1131 
1132         wc_get_template( 'single-product/related.php', $args );
1133     }
1134 }
1135 
1136 if ( ! function_exists( 'woocommerce_upsell_display' ) ) {
1137 
1138     /**
1139      * Output product up sells.
1140      *
1141      * @access public
1142      * @param int $posts_per_page (default: -1)
1143      * @param int $columns (default: 2)
1144      * @param string $orderby (default: 'rand')
1145      * @return void
1146      */
1147     function woocommerce_upsell_display( $posts_per_page = '-1', $columns = 2, $orderby = 'rand' ) {
1148         wc_get_template( 'single-product/up-sells.php', array(
1149                 'posts_per_page'    => $posts_per_page,
1150                 'orderby'           => apply_filters( 'woocommerce_upsells_orderby', $orderby ),
1151                 'columns'           => $columns
1152             ) );
1153     }
1154 }
1155 
1156 /** Cart ******************************************************************/
1157 
1158 if ( ! function_exists( 'woocommerce_shipping_calculator' ) ) {
1159 
1160     /**
1161      * Output the cart shipping calculator.
1162      *
1163      * @access public
1164      * @subpackage  Cart
1165      * @return void
1166      */
1167     function woocommerce_shipping_calculator() {
1168         wc_get_template( 'cart/shipping-calculator.php' );
1169     }
1170 }
1171 
1172 if ( ! function_exists( 'woocommerce_cart_totals' ) ) {
1173 
1174     /**
1175      * Output the cart totals.
1176      *
1177      * @access public
1178      * @subpackage  Cart
1179      * @return void
1180      */
1181     function woocommerce_cart_totals() {
1182         wc_get_template( 'cart/cart-totals.php' );
1183     }
1184 }
1185 
1186 if ( ! function_exists( 'woocommerce_cross_sell_display' ) ) {
1187 
1188     /**
1189      * Output the cart cross-sells.
1190      *
1191      * @param  integer $posts_per_page
1192      * @param  integer $columns
1193      * @param  string $orderby
1194      */
1195     function woocommerce_cross_sell_display( $posts_per_page = 2, $columns = 2, $orderby = 'rand' ) {
1196         wc_get_template( 'cart/cross-sells.php', array(
1197                 'posts_per_page' => $posts_per_page,
1198                 'orderby'        => $orderby,
1199                 'columns'        => $columns
1200             ) );
1201     }
1202 }
1203 
1204 /** Mini-Cart *************************************************************/
1205 
1206 if ( ! function_exists( 'woocommerce_mini_cart' ) ) {
1207 
1208     /**
1209      * Output the Mini-cart - used by cart widget
1210      *
1211      * @access public
1212      * @return void
1213      */
1214     function woocommerce_mini_cart( $args = array() ) {
1215 
1216         $defaults = array(
1217             'list_class' => ''
1218         );
1219 
1220         $args = wp_parse_args( $args, $defaults );
1221 
1222         wc_get_template( 'cart/mini-cart.php', $args );
1223     }
1224 }
1225 
1226 /** Login *****************************************************************/
1227 
1228 if ( ! function_exists( 'woocommerce_login_form' ) ) {
1229 
1230     /**
1231      * Output the WooCommerce Login Form
1232      *
1233      * @access public
1234      * @subpackage  Forms
1235      * @return void
1236      */
1237     function woocommerce_login_form( $args = array() ) {
1238 
1239         $defaults = array(
1240             'message'  => '',
1241             'redirect' => '',
1242             'hidden'   => false
1243         );
1244 
1245         $args = wp_parse_args( $args, $defaults  );
1246 
1247         wc_get_template( 'global/form-login.php', $args );
1248     }
1249 }
1250 
1251 if ( ! function_exists( 'woocommerce_checkout_login_form' ) ) {
1252 
1253     /**
1254      * Output the WooCommerce Checkout Login Form
1255      *
1256      * @access public
1257      * @subpackage  Checkout
1258      * @return void
1259      */
1260     function woocommerce_checkout_login_form() {
1261         wc_get_template( 'checkout/form-login.php', array( 'checkout' => WC()->checkout() ) );
1262     }
1263 }
1264 
1265 if ( ! function_exists( 'woocommerce_breadcrumb' ) ) {
1266 
1267     /**
1268      * Output the WooCommerce Breadcrumb
1269      *
1270      * @access public
1271      * @return void
1272      */
1273     function woocommerce_breadcrumb( $args = array() ) {
1274 
1275         $defaults = apply_filters( 'woocommerce_breadcrumb_defaults', array(
1276             'delimiter'   => ' &#47; ',
1277             'wrap_before' => '<nav class="woocommerce-breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
1278             'wrap_after'  => '</nav>',
1279             'before'      => '',
1280             'after'       => '',
1281             'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
1282         ) );
1283 
1284         $args = wp_parse_args( $args, $defaults );
1285 
1286         wc_get_template( 'global/breadcrumb.php', $args );
1287     }
1288 }
1289 
1290 if ( ! function_exists( 'woocommerce_order_review' ) ) {
1291 
1292     /**
1293      * Output the Order review table for the checkout.
1294      *
1295      * @access public
1296      * @subpackage  Checkout
1297      * @return void
1298      */
1299     function woocommerce_order_review( $is_ajax = false ) {
1300         wc_get_template( 'checkout/review-order.php', array( 'checkout' => WC()->checkout(), 'is_ajax' => $is_ajax ) );
1301     }
1302 }
1303 
1304 if ( ! function_exists( 'woocommerce_checkout_coupon_form' ) ) {
1305 
1306     /**
1307      * Output the Coupon form for the checkout.
1308      *
1309      * @access public
1310      * @subpackage  Checkout
1311      * @return void
1312      */
1313     function woocommerce_checkout_coupon_form() {
1314         wc_get_template( 'checkout/form-coupon.php', array( 'checkout' => WC()->checkout() ) );
1315     }
1316 }
1317 
1318 if ( ! function_exists( 'woocommerce_products_will_display' ) ) {
1319 
1320     /**
1321      * Check if we will be showing products or not (and not subcats only)
1322      *
1323      * @access public
1324      * @subpackage  Loop
1325      * @return bool
1326      */
1327     function woocommerce_products_will_display() {
1328         if ( is_shop() )
1329             return get_option( 'woocommerce_shop_page_display' ) != 'subcategories';
1330 
1331         if ( ! is_product_taxonomy() )
1332             return false;
1333 
1334         if ( is_search() || is_filtered() || is_paged() )
1335             return true;
1336 
1337         $term = get_queried_object();
1338 
1339         if ( is_product_category() ) {
1340             switch ( get_woocommerce_term_meta( $term->term_id, 'display_type', true ) ) {
1341                 case 'subcategories' :
1342                     // Nothing - we want to continue to see if there are products/subcats
1343                 break;
1344                 case 'products' :
1345                 case 'both' :
1346                     return true;
1347                 break;
1348                 default :
1349                     // Default - no setting
1350                     if ( get_option( 'woocommerce_category_archive_display' ) != 'subcategories' )
1351                         return true;
1352                 break;
1353             }
1354         }
1355 
1356         // Begin subcategory logic
1357         global $wpdb;
1358 
1359         $parent_id             = empty( $term->term_id ) ? 0 : $term->term_id;
1360         $taxonomy              = empty( $term->taxonomy ) ? '' : $term->taxonomy;
1361         $products_will_display = true;
1362 
1363         if ( ! $parent_id && ! $taxonomy ) {
1364             return true;
1365         }
1366 
1367         $transient_name = 'wc_products_will_display_' . $parent_id . WC_Cache_Helper::get_transient_version( 'product_query' );
1368 
1369         if ( false === ( $products_will_display = get_transient( $transient_name ) ) ) {
1370             $has_children = $wpdb->get_col( $wpdb->prepare( "SELECT term_id FROM {$wpdb->term_taxonomy} WHERE parent = %d AND taxonomy = %s", $parent_id, $taxonomy ) );
1371 
1372             if ( $has_children ) {
1373                 // Check terms have products inside - parents first. If products are found inside, subcats will be shown instead of products so we can return false.
1374                 if ( sizeof( get_objects_in_term( $has_children, $taxonomy ) ) > 0 ) {
1375                     $products_will_display = false;
1376                 } else {
1377                     // If we get here, the parents were empty so we're forced to check children
1378                     foreach ( $has_children as $term ) {
1379                         $children = get_term_children( $term, $taxonomy );
1380 
1381                         if ( sizeof( get_objects_in_term( $children, $taxonomy ) ) > 0 ) {
1382                             $products_will_display = false;
1383                             break;
1384                         }
1385                     }
1386                 }
1387             } else {
1388                 $products_will_display = true;
1389             }
1390         }
1391 
1392         set_transient( $transient_name, $products_will_display, YEAR_IN_SECONDS );
1393 
1394         return $products_will_display;
1395     }
1396 }
1397 
1398 if ( ! function_exists( 'woocommerce_product_subcategories' ) ) {
1399 
1400     /**
1401      * Display product sub categories as thumbnails.
1402      *
1403      * @access public
1404      * @subpackage  Loop
1405      * @param array $args
1406      * @return null|boolean
1407      */
1408     function woocommerce_product_subcategories( $args = array() ) {
1409         global $wp_query;
1410 
1411         $defaults = array(
1412             'before'        => '',
1413             'after'         => '',
1414             'force_display' => false
1415         );
1416 
1417         $args = wp_parse_args( $args, $defaults );
1418 
1419         extract( $args );
1420 
1421         // Main query only
1422         if ( ! is_main_query() && ! $force_display ) {
1423             return;
1424         }
1425 
1426         // Don't show when filtering, searching or when on page > 1 and ensure we're on a product archive
1427         if ( is_search() || is_filtered() || is_paged() || ( ! is_product_category() && ! is_shop() ) ) {
1428             return;
1429         }
1430 
1431         // Check categories are enabled
1432         if ( is_shop() && get_option( 'woocommerce_shop_page_display' ) == '' ) {
1433             return;
1434         }
1435 
1436         // Find the category + category parent, if applicable
1437         $term           = get_queried_object();
1438         $parent_id      = empty( $term->term_id ) ? 0 : $term->term_id;
1439 
1440         if ( is_product_category() ) {
1441             $display_type = get_woocommerce_term_meta( $term->term_id, 'display_type', true );
1442 
1443             switch ( $display_type ) {
1444                 case 'products' :
1445                     return;
1446                 break;
1447                 case '' :
1448                     if ( get_option( 'woocommerce_category_archive_display' ) == '' ) {
1449                         return;
1450                     }
1451                 break;
1452             }
1453         }
1454 
1455         // NOTE: using child_of instead of parent - this is not ideal but due to a WP bug ( http://core.trac.wordpress.org/ticket/15626 ) pad_counts won't work
1456         $args = apply_filters( 'woocommerce_product_subcategories_args', array(
1457             'parent'        => $parent_id,
1458             'menu_order'    => 'ASC',
1459             'hide_empty'    => 1,
1460             'hierarchical'  => 1,
1461             'taxonomy'      => 'product_cat',
1462             'pad_counts'    => 1
1463         ) );
1464 
1465         $product_categories     = get_categories( $args );
1466         $product_category_found = false;
1467 
1468         if ( $product_categories ) {
1469             echo $before;
1470 
1471             foreach ( $product_categories as $category ) {
1472                 wc_get_template( 'content-product_cat.php', array(
1473                     'category' => $category
1474                 ) );
1475             }
1476 
1477             // If we are hiding products disable the loop and pagination
1478             if ( is_product_category() ) {
1479                 $display_type = get_woocommerce_term_meta( $term->term_id, 'display_type', true );
1480 
1481                 switch ( $display_type ) {
1482                     case 'subcategories' :
1483                         $wp_query->post_count    = 0;
1484                         $wp_query->max_num_pages = 0;
1485                     break;
1486                     case '' :
1487                         if ( get_option( 'woocommerce_category_archive_display' ) == 'subcategories' ) {
1488                             $wp_query->post_count    = 0;
1489                             $wp_query->max_num_pages = 0;
1490                         }
1491                     break;
1492                 }
1493             }
1494 
1495             if ( is_shop() && get_option( 'woocommerce_shop_page_display' ) == 'subcategories' ) {
1496                 $wp_query->post_count    = 0;
1497                 $wp_query->max_num_pages = 0;
1498             }
1499 
1500             echo $after;
1501         }
1502 
1503         return true;
1504     }
1505 }
1506 
1507 if ( ! function_exists( 'woocommerce_subcategory_thumbnail' ) ) {
1508 
1509     /**
1510      * Show subcategory thumbnails.
1511      *
1512      * @access public
1513      * @param mixed $category
1514      * @subpackage  Loop
1515      * @return void
1516      */
1517     function woocommerce_subcategory_thumbnail( $category ) {
1518         $small_thumbnail_size   = apply_filters( 'single_product_small_thumbnail_size', 'shop_catalog' );
1519         $dimensions             = wc_get_image_size( $small_thumbnail_size );
1520         $thumbnail_id           = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true  );
1521 
1522         if ( $thumbnail_id ) {
1523             $image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size  );
1524             $image = $image[0];
1525         } else {
1526             $image = wc_placeholder_img_src();
1527         }
1528 
1529         if ( $image ) {
1530             // Prevent esc_url from breaking spaces in urls for image embeds
1531             // Ref: http://core.trac.wordpress.org/ticket/23605
1532             $image = str_replace( ' ', '%20', $image );
1533 
1534             echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
1535         }
1536     }
1537 }
1538 
1539 if ( ! function_exists( 'woocommerce_order_details_table' ) ) {
1540 
1541     /**
1542      * Displays order details in a table.
1543      *
1544      * @access public
1545      * @param mixed $order_id
1546      * @subpackage  Orders
1547      * @return void
1548      */
1549     function woocommerce_order_details_table( $order_id ) {
1550         if ( ! $order_id ) return;
1551 
1552         wc_get_template( 'order/order-details.php', array(
1553             'order_id' => $order_id
1554         ) );
1555     }
1556 }
1557 
1558 
1559 if ( ! function_exists( 'woocommerce_order_again_button' ) ) {
1560 
1561     /**
1562      * Display an 'order again' button on the view order page.
1563      *
1564      * @access public
1565      * @param object $order
1566      * @subpackage  Orders
1567      * @return void
1568      */
1569     function woocommerce_order_again_button( $order ) {
1570         if ( ! $order || ! $order->has_status( 'completed' ) ) {
1571             return;
1572         }
1573         ?>
1574         <p class="order-again">
1575             <a href="<?php echo wp_nonce_url( add_query_arg( 'order_again', $order->id ) , 'woocommerce-order_again' ); ?>" class="button"><?php _e( 'Order Again', 'woocommerce' ); ?></a>
1576         </p>
1577         <?php
1578     }
1579 }
1580 
1581 /** Forms ****************************************************************/
1582 
1583 if ( ! function_exists( 'woocommerce_form_field' ) ) {
1584 
1585     /**
1586      * Outputs a checkout/address form field.
1587      *
1588      * @access public
1589      * @subpackage  Forms
1590      * @param mixed $key
1591      * @param mixed $args
1592      * @param string $value (default: null)
1593      * @return void
1594      * @todo This function needs to be broken up in smaller pieces
1595      */
1596     function woocommerce_form_field( $key, $args, $value = null ) {
1597         $defaults = array(
1598             'type'              => 'text',
1599             'label'             => '',
1600             'description'       => '',
1601             'placeholder'       => '',
1602             'maxlength'         => false,
1603             'required'          => false,
1604             'id'                => $key,
1605             'class'             => array(),
1606             'label_class'       => array(),
1607             'input_class'       => array(),
1608             'return'            => false,
1609             'options'           => array(),
1610             'custom_attributes' => array(),
1611             'validate'          => array(),
1612             'default'           => '',
1613         );
1614 
1615         $args = wp_parse_args( $args, $defaults  );
1616 
1617         if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
1618 
1619         if ( $args['required'] ) {
1620             $args['class'][] = 'validate-required';
1621             $required = ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce'  ) . '">*</abbr>';
1622         } else {
1623             $required = '';
1624         }
1625 
1626         $args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';
1627 
1628         if ( is_string( $args['label_class'] ) )
1629             $args['label_class'] = array( $args['label_class'] );
1630 
1631         if ( is_null( $value ) )
1632             $value = $args['default'];
1633 
1634         // Custom attribute handling
1635         $custom_attributes = array();
1636 
1637         if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) )
1638             foreach ( $args['custom_attributes'] as $attribute => $attribute_value )
1639                 $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
1640 
1641         if ( ! empty( $args['validate'] ) )
1642             foreach( $args['validate'] as $validate )
1643                 $args['class'][] = 'validate-' . $validate;
1644 
1645         switch ( $args['type'] ) {
1646         case "country" :
1647 
1648             $countries = $key == 'shipping_country' ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();
1649 
1650             if ( sizeof( $countries ) == 1 ) {
1651 
1652                 $field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">';
1653 
1654                 if ( $args['label'] )
1655                     $field .= '<label class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label']  . '</label>';
1656 
1657                 $field .= '<strong>' . current( array_values( $countries ) ) . '</strong>';
1658 
1659                 $field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys($countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" />';
1660 
1661                 if ( $args['description'] )
1662                     $field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
1663 
1664                 $field .= '</p>' . $after;
1665 
1666             } else {
1667 
1668                 $field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">'
1669                         . '<label for="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label'] . $required  . '</label>'
1670                         . '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="country_to_state country_select" ' . implode( ' ', $custom_attributes ) . '>'
1671                         . '<option value="">'.__( 'Select a country&hellip;', 'woocommerce' ) .'</option>';
1672 
1673                 foreach ( $countries as $ckey => $cvalue )
1674                     $field .= '<option value="' . esc_attr( $ckey ) . '" '.selected( $value, $ckey, false ) .'>'.__( $cvalue, 'woocommerce' ) .'</option>';
1675 
1676                 $field .= '</select>';
1677 
1678                 $field .= '<noscript><input type="submit" name="woocommerce_checkout_update_totals" value="' . __( 'Update country', 'woocommerce' ) . '" /></noscript>';
1679 
1680                 if ( $args['description'] )
1681                     $field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
1682 
1683                 $field .= '</p>' . $after;
1684 
1685             }
1686 
1687             break;
1688         case "state" :
1689 
1690             /* Get Country */
1691             $country_key = $key == 'billing_state'? 'billing_country' : 'shipping_country';
1692 
1693             if ( isset( $_POST[ $country_key ] ) ) {
1694                 $current_cc = wc_clean( $_POST[ $country_key ] );
1695             } elseif ( is_user_logged_in() ) {
1696                 $current_cc = get_user_meta( get_current_user_id() , $country_key, true );
1697                 if ( ! $current_cc) {
1698                     $current_cc = apply_filters('default_checkout_country', (WC()->customer->get_country()) ? WC()->customer->get_country() : WC()->countries->get_base_country());
1699                 }
1700             } elseif ( $country_key == 'billing_country' ) {
1701                 $current_cc = apply_filters('default_checkout_country', (WC()->customer->get_country()) ? WC()->customer->get_country() : WC()->countries->get_base_country());
1702             } else {
1703                 $current_cc = apply_filters('default_checkout_country', (WC()->customer->get_shipping_country()) ? WC()->customer->get_shipping_country() : WC()->countries->get_base_country());
1704             }
1705 
1706             $states = WC()->countries->get_states( $current_cc );
1707 
1708             if ( is_array( $states ) && empty( $states ) ) {
1709 
1710                 $field  = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field" style="display: none">';
1711 
1712                 if ( $args['label'] )
1713                     $field .= '<label for="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label'] . $required . '</label>';
1714                 $field .= '<input type="hidden" class="hidden" name="' . esc_attr( $key )  . '" id="' . esc_attr( $args['id'] ) . '" value="" ' . implode( ' ', $custom_attributes ) . ' placeholder="' . esc_attr( $args['placeholder'] ) . '" />';
1715 
1716                 if ( $args['description'] )
1717                     $field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
1718 
1719                 $field .= '</p>' . $after;
1720 
1721             } elseif ( is_array( $states ) ) {
1722 
1723                 $field  = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">';
1724 
1725                 if ( $args['label'] )
1726                     $field .= '<label for="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label']. $required . '</label>';
1727                 $field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="state_select" ' . implode( ' ', $custom_attributes ) . ' placeholder="' . esc_attr( $args['placeholder'] ) . '">
1728                     <option value="">'.__( 'Select a state&hellip;', 'woocommerce' ) .'</option>';
1729 
1730                 foreach ( $states as $ckey => $cvalue )
1731                     $field .= '<option value="' . esc_attr( $ckey ) . '" '.selected( $value, $ckey, false ) .'>'.__( $cvalue, 'woocommerce' ) .'</option>';
1732 
1733                 $field .= '</select>';
1734 
1735                 if ( $args['description'] )
1736                     $field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
1737 
1738                 $field .= '</p>' . $after;
1739 
1740             } else {
1741 
1742                 $field  = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">';
1743 
1744                 if ( $args['label'] )
1745                     $field .= '<label for="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label']. $required . '</label>';
1746                 $field .= '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" value="' . esc_attr( $value ) . '"  placeholder="' . esc_attr( $args['placeholder'] ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
1747 
1748                 if ( $args['description'] )
1749                     $field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
1750 
1751                 $field .= '</p>' . $after;
1752 
1753             }
1754 
1755             break;
1756         case "textarea" :
1757 
1758             $field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">';
1759 
1760             if ( $args['label'] )
1761                 $field .= '<label for="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label']. $required  . '</label>';
1762 
1763             $field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>'. esc_textarea( $value  ) .'</textarea>';
1764 
1765             if ( $args['description'] )
1766                 $field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
1767 
1768             $field .= '</p>' . $after;
1769 
1770             break;
1771         case "checkbox" :
1772 
1773             $field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">
1774                     <input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" '.checked( $value, 1, false ) .' />
1775                     <label for="' . esc_attr( $args['id'] ) . '" class="checkbox ' . implode( ' ', $args['label_class'] ) .'" ' . implode( ' ', $custom_attributes ) . '>' . $args['label'] . $required . '</label>';
1776 
1777             if ( $args['description'] )
1778                 $field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
1779 
1780             $field .= '</p>' . $after;
1781 
1782             break;
1783         case "password" :
1784 
1785             $field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">';
1786 
1787             if ( $args['label'] )
1788                 $field .= '<label for="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label']. $required . '</label>';
1789 
1790             $field .= '<input type="password" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
1791 
1792             if ( $args['description'] )
1793                 $field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
1794 
1795             $field .= '</p>' . $after;
1796 
1797             break;
1798         case "text" :
1799 
1800             $field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">';
1801 
1802             if ( $args['label'] )
1803                 $field .= '<label for="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label'] . $required . '</label>';
1804 
1805             $field .= '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
1806 
1807             if ( $args['description'] )
1808                 $field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
1809 
1810             $field .= '</p>' . $after;
1811 
1812             break;
1813         case "select" :
1814 
1815             $options = '';
1816 
1817             if ( ! empty( $args['options'] ) )
1818                 foreach ( $args['options'] as $option_key => $option_text )
1819                     $options .= '<option value="' . esc_attr( $option_key ) . '" '. selected( $value, $option_key, false ) . '>' . esc_attr( $option_text ) .'</option>';
1820 
1821                 $field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">';
1822 
1823                 if ( $args['label'] )
1824                     $field .= '<label for="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label']. $required . '</label>';
1825 
1826                 $field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select" ' . implode( ' ', $custom_attributes ) . '>
1827                         ' . $options . '
1828                     </select>';
1829 
1830                 if ( $args['description'] )
1831                     $field .= '<span class="description">' . esc_attr( $args['description'] ) . '</span>';
1832 
1833                 $field .= '</p>' . $after;
1834 
1835             break;
1836         case "radio" :
1837 
1838             $field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '_field">';
1839 
1840             if ( $args['label'] )
1841                 $field .= '<label for="' . esc_attr( current( array_keys( $args['options'] ) ) ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label']. $required  . '</label>';
1842 
1843             if ( ! empty( $args['options'] ) ) {
1844                 foreach ( $args['options'] as $option_key => $option_text ) {
1845                     $field .= '<input type="radio" class="input-radio" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
1846                     $field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) .'">' . $option_text . '</label>';
1847                 }
1848             }
1849 
1850             $field .= '</p>' . $after;
1851 
1852             break;
1853         default :
1854 
1855             $field = apply_filters( 'woocommerce_form_field_' . $args['type'], '', $key, $args, $value );
1856 
1857             break;
1858         }
1859 
1860         if ( $args['return'] ) return $field; else echo $field;
1861     }
1862 }
1863 
1864 if ( ! function_exists( 'get_product_search_form' ) ) {
1865 
1866     /**
1867      * Output Product search forms.
1868      *
1869      * @access public
1870      * @subpackage  Forms
1871      * @param bool $echo (default: true)
1872      * @return string
1873      * @todo This function needs to be broken up in smaller pieces
1874      */
1875     function get_product_search_form( $echo = true  ) {
1876         do_action( 'get_product_search_form'  );
1877 
1878         $search_form_template = locate_template( 'product-searchform.php' );
1879         if ( '' != $search_form_template  ) {
1880             require $search_form_template;
1881             return;
1882         }
1883 
1884         $form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/'  ) ) . '">
1885             <div>
1886                 <label class="screen-reader-text" for="s">' . __( 'Search for:', 'woocommerce' ) . '</label>
1887                 <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __( 'Search for products', 'woocommerce' ) . '" />
1888                 <input type="submit" id="searchsubmit" value="'. esc_attr__( 'Search', 'woocommerce' ) .'" />
1889                 <input type="hidden" name="post_type" value="product" />
1890             </div>
1891         </form>';
1892 
1893         if ( $echo  )
1894             echo apply_filters( 'get_product_search_form', $form );
1895         else
1896             return apply_filters( 'get_product_search_form', $form );
1897     }
1898 }