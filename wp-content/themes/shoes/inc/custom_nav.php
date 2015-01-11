<?php
class Description_Walker extends Walker_Nav_Menu {

//start of the sub menu wrap
    function start_lvl(&$output, $depth) {
        $output .= '<div class="megapanel">
						<div class="row">

								<div class="h_nav">
                                    <ul>';
    }

//end of the sub menu wrap
    function end_lvl(&$output, $depth) {
        $output .= '
                        </ul>
                    </div>
			</div>
		</div>';
    }

//add the description to the menu item output
    function start_el(&$output, $item, $depth, $args) {
        global $wp_query;
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = ' class="' . esc_attr( $class_names ) . '"';

        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

        if($depth == 0){
            $attributes .= 'class="color2"';
        }elseif($depth == 1 ){
            $attributes .= 'class="color12"';
        }

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        //if(strlen($item->description)>2){ $item_output .= '<br /><span class="sub">' . $item->description . '</span>'; }
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
    function end_el(&$output, $item, $depth, $args) {}
}

/******Dropdown Menu**********/
class Walker_Nav_Menu_Dropdown extends Walker_Nav_Menu {

    function start_lvl(&$output, $depth) {    }

    function end_lvl(&$output, $depth) {    }

    function start_el(&$output, $item, $depth, $args) {
        // Here is where we create each option.
        $item_output = '';

        // add spacing to the title based on the depth
        $item->title = str_repeat("&amp;nbsp;", $depth * 4) . $item->title;

        // Get the attributes.. Though we likely don't need them for this...
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' value="'   . esc_attr( $item->url        ) .'"' : '';

        // Add the html
        $item_output .= '<option'. $attributes .'>';
        $item_output .= apply_filters( 'the_title_attribute', $item->title );

        // Add this new item to the output string.
        $output .= $item_output;

    }

    function end_el(&$output, $item, $depth) {
        // Close the item.
        $output .= "</option>\n";
    }

}







?>