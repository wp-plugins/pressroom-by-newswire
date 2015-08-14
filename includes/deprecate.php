<?php

/**
*/
function maybe_autocreate_roles() {
    
}


/**
 * action hook wp_head
 */
function newswire_print_pressroom_styles() {

    foreach (array('header', 'footer', 'body') as $part) {
        print_custom_styles($part, 'pressroom');
    }

}

function newswire_print_newsroom_styles() {

    foreach (array('body') as $part) {
        print_custom_styles($part, 'newsroom');
    }

}

/**
 * Custom Block Styles
 * Print newsroom/pressroom body, header, footer style
 */
function print_custom_styles($part, $page) {

    $options = newswire_options();

    if ('newsroom' == $page) {

        $styles = $options['newsroom_styles'];

    } else {

        $styles = $options['pressroom_styles'];
    }

    extract($styles);

    $style = (object) $$part;

    echo '<style type="text/css">';
    //echo "<!-- Custom Styles from PressRoom by Newswire -{$part} //-->";
    if ($part == 'header') {

        $part_style_selector = ' .block-header ';

    } elseif ($part == 'footer') {

        $part_style_selector = ' .block-footer ';

    } elseif ($part == 'body') {

        $part_style_selector = ' .block-content ';
    }
    //var_dump($style->bg);

    //background image
    if ( $style->bg == '_image_active' && $style->bg_image_url ) {
        
        echo " #tiles > li {$part_style_selector} {  background: url('$style->bg_image_url') $style->bg_repeat; }";

    } elseif ( $style->bg == '_color_active' && $style->bg_color ) {

        //bg color
    
        echo " #tiles > li {$part_style_selector} {  background-color: $style->bg_color; }";
        if ('pressroom' == $page) {
            //echo " #tiles > li .content {  background-color: $style->bg_color; }";
            //echo " #tiles > li .block-content {  background-color: $style->bg_color; }";
        }

    } else {
        
    }

    //border
    if ($style->border) {
        if ($part == 'footer' && $style->border_thickness && $style->border_color) {

            echo " #tiles > li {$part_style_selector} { border-bottom: {$style->border_thickness} solid {$style->border_color}; }";
        }

        if ($part == 'footer' && $style->border_thickness && $style->border_color) {

            echo " #tiles > li {$part_style_selector} { border-top: {$style->border_thickness} solid {$style->border_color}; }";
        }

        echo " #tiles > li {$part_style_selector}  {  ";

        $style->top_radius = $style->top_radius ? $style->top_radius : 0;
        $style->right_radius = $style->right_radius ? $style->right_radius : 0;
        $style->bottom_radius = $style->right_radius ? $style->right_radius : 0;
        $style->left_radius = $style->left_radius ? $style->left_radius : 0;

        echo "  border-radius: {$style->top_radius}px {$style->right_radius}px {$style->bottom_radius}px {$style->left_radius}px ;
                -moz-border-radius: {$style->top_radius}px {$style->right_radius}px {$style->bottom_radius}px {$style->left_radius}px ;
                -webkit-border-radius: {$style->top_radius}px {$style->right_radius}px {$style->bottom_radius}px {$style->left_radius}px ; ";

        if ($style->border_thickness && $style->border_color) {
            echo " border: {$style->border_thickness}px solid {$style->border_color}; ";
        }

        echo "}";
    }

    //box shadow
    //-webkit-box-shadow: 10px 10px 10px rgba(0, 0, 0, 0.5);
    //-moz-box-shadow: 10px 10px 10px rgba(0, 0, 0, 0.5);
    //box-shadow: 10px 10px 10px rgba(0, 0, 0, 0.5);
    //box-shadow: none|h-shadow v-shadow blur spread color |inset|initial|inherit;
    if ($style->box_shadow) {

        $style->h_shadow = $style->h_shadow ? $style->h_shadow : 0;
        $style->v_shadow = $style->v_shadow ? $style->v_shadow : 0;
        $style->spread_shadow = $style->spread_shadow ? $style->spread_shadow : 0;
        $style->opacity_shadow = $style->opacity_shadow ? $style->opacity_shadow : 0;

        if (function_exists('hex2rgb')) {
            $color = hex2rgb($style->shadow_color);
            echo " #tiles > li {
            box-shadow: {$style->h_shadow}px {$style->v_shadow}px {$style->blur_shadow}px {$style->spread_shadow}px rgba($color[0], $color[1], $color[2], {$style->opacity_shadow}) ;
            -moz-box-shadow: {$style->h_shadow}px {$style->v_shadow}px {$style->blur_shadow}px {$style->spread_shadow}px rgba($color[0], $color[1], $color[2], {$style->opacity_shadow}) ;
            -webkit-box-shadow: {$style->h_shadow}px {$style->v_shadow}px {$style->blur_shadow}px {$style->spread_shadow}px rgba($color[0], $color[1], $color[2], {$style->opacity_shadow}) ;
         }";

        } else {
            echo " #tiles > li {
            box-shadow: {$style->h_shadow}px {$style->v_shadow}px {$style->blur_shadow}px {$style->spread_shadow}px {$style->shadow_color};
            -moz-box-shadow: {$style->h_shadow}px {$style->v_shadow}px {$style->blur_shadow}px {$style->spread_shadow}px {$style->shadow_color};
            -webkit-box-shadow: {$style->h_shadow}px {$style->v_shadow}px {$style->blur_shadow}px {$style->spread_shadow}px {$style->shadow_color};
         }";
        }

    } else {
        /*echo " #tiles > li: {
    box-shadow: none ;
    -moz-box-shadow: none ;
    -webkit-box-shadow: none ;
    }";  */
    }
    echo "\n";

    echo $options['pressroom_custom_css'];
    echo '</style>';
}

function get_pressroom_template($file) {
    ob_start();

    @include $file;
    return ob_get_clean();
}

/**
 *
 */
function is_disable_submission($post_id) {
    $value = get_post_meta($post_id, 'newswire_submission', $single = true);
    return ('disable' === $value) ? true : false;
}

function make_blog_copy($post_id) {
    $value = get_post_meta($post_id, 'newswire_copytoblog', $single = true);
    return ('enable' === $value) ? true : false;
}

function is_disable_submission_bonus($post_id) {
    $value = get_post_meta($post_id, 'newswire_submission_bonus', $single = true);
    return ('disable' === $value) ? true : false;
}

/**
 *
 */
function newswire_post_exists($permalink) {

    global $wpdb;

    //$wpdb->flush();

    $result = $wpdb->get_results("SELECT post_id FROM " . $wpdb->postmeta . " WHERE meta_key = 'rss_source_url' AND meta_value ='" . $permalink . "'  LIMIT 1");

    return (empty($result)) ? false : $result[0]->post_id;

    /*
$args = array(
'post_status' => 'any',
'post_type' => 'any',
'meta_key' => 'rss_source_url',
'meta_value' => esc_url($permalink)
);

$posts = get_posts( $args );

// Not already imported
return(count($posts) > 0);
 */
}
/**
 *
 */
function newswire_newsroom_url() {

    $options = newswire_options();

    $url = get_permalink($options['newsroom_page_template']);

    return $url;
}
/*
 *
 */
function newswire_pressroom_url() {

    $options = newswire_options();

    $url = get_permalink($options['pressroom_page_template']);

    return $url;
}

/**
 * print markup'd string only when val is not empty
 */
function newswire_ifprint($string, $val) {
    if (is_string($val) && $val != '' && $val != ' ') {
        printf($string, $val);
    } elseif (is_array($val)) {
        vprintf($string, $val);
    }
}

/**
 * enable us to set variable scope local to the function
 */
function newswire_include($file) {
    if (file_exists($file)) {
        include $file;
    } else {
        var_dump($file);
    }

}

/**
 * More pin markup for pressroom
 */
function pressroom_morepin_html() {

    // print some js to make this work
    $html .= '<div id="pressroom-pin-navigation">' .
    '<ul class="pager">'
    . '<li id="pressroom-navigation-next">' . apply_filters('pressroom_morepin_link', '') . '</li>'
    . ' <li id="pressroom-navigation-previous"><a href="#"></a></li>'
    . '</ul>'
    . '</div>';

    do_action('newswire_pressroom_after');

    return $html;
}


/**
 * Pressroom Tab links
 */
add_filter('pressroom_tab_link', 'newswire_pressroom_tab_links', 10, 1);
function newswire_pressroom_tab_links($slug) {
    $opt = newswire_options();
    switch ($slug) {
        case 'newsroom_page':
            $post = get_post($opt['newsroom_page_template']);
            return get_permalink($post);
            # code...
            break;
        case 'write_release':
            return admin_url('post-new.php?post_type=pr');
            break;
        case 'manage_page':
            return admin_url('edit.php?post_type=pr&page=pressroom_settings');
            break;

        default:
            # code...
            break;
    }
}





/**
 * Array of pressroom templates
 *
 * @used in
 *
 * @return array
 */
function newswire_pressroom_templates() {
    return array('pressroom-template-standard.php' => __('Pressroom - Custom', 'newswire'), 'pressroom-template-current_theme.php' => 'Pressroom - Current Theme');
}

/**
 * return pressroom custom post types as array
 */
function newswire_pressroom_types() {
    return array('pin_as_text', 'pin_as_embed', 'pin_as_image', 'pin_as_quote', 'pin_as_social', 'pin_as_link', 'pin_as_contact');
}


function newswire_pressroom_object() {

    global $newswire_pressroom;

    if ($newswire_pressroom) {
        return $newswire_pressroom;
    } else {
        return $newswire_pressroom = new Newswire_Pressroom();
    }
}





