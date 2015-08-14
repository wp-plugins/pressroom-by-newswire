<?php

if ( ! function_exists('newswire_download_and_activate_pro')):
/**
* Create download link for pro version
*/
add_action('load-admin_page_newswire-download-pro', 'newswire_download_and_activate_pro');
function newswire_download_and_activate_pro() {
    
    if ( ! current_user_can('install_plugins') )
        wp_die( __( 'You do not have sufficient permissions to install plugins on this site.' ) );
    
    $plugin = 'newswirexpress/newswirexpress.php';

    if ( !in_array($plugin, array_keys(get_plugins()) ) ) {

        //download url
        $download_url = 'http://www.newswire.net/newswirexpress/newswirexpress.zip';

            /* download Pro */
        if ( ! function_exists( 'get_plugins' ) )
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $uploadDir = wp_upload_dir();
        $zip_name = array('newswirexpress', 'newswirexpress.php');


        //get all plugins
        //$all_plugins = get_plugins();
        
        if ( !file_exists( WP_PLUGIN_DIR . '/' . $zip_name[0] ) ) {
            

            $received_content = file_get_contents( $download_url );

            if ( ! $received_content && $valid ) {
            $result['error'] = __( "Failed to download the zip archive. Please, upload the plugin manually", 'newswire' );
        } else {
        
            if ( is_writable( $uploadDir["path"] ) ) {
                $file_put_contents = $uploadDir["path"] . "/" . $zip_name[0] . ".zip";
                if ( file_put_contents( $file_put_contents, $received_content ) ) {
                    @chmod( $file_put_contents, octdec( 755 ) );
                    if ( class_exists( 'ZipArchive' ) ) {
                        $zip = new ZipArchive();
                        if ( $zip->open( $file_put_contents ) === TRUE ) {
                            $zip->extractTo( WP_PLUGIN_DIR );
                            $zip->close();
                        } else {
                            $result['error'] = __( "Failed to open the zip archive. Please, upload the plugin manually", 'newswire' );
                        }
                    } elseif ( class_exists( 'Phar' ) ) {
                        $phar = new PharData( $file_put_contents );
                        $phar->extractTo( WP_PLUGIN_DIR );
                    } else {
                        $result['error'] = __( "Your server does not support either ZipArchive or Phar. Please, upload the plugin manually", 'newswire' );
                    }
                    @unlink( $file_put_contents );
                } else {
                    $result['error'] = __( "Failed to download the zip archive. Please, upload the plugin manually", 'newswire' );
                }
            } else {
                $result['error'] = __( "UploadDir is not writable. Please, upload the plugin manually", 'newswire' );
            }
        }
        }

        

        //deactivate free version plugin
        deactivate_plugins( 'pressroom-by-newswire/pressroom.php' ,true);

        /* activate Pro */
        if ( validate_plugin($plugin) && file_exists( WP_PLUGIN_DIR . '/' . $zip_name[0] ) ) {
        
            if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
                /* if multisite and free plugin is network activated */
                $active_plugins = get_site_option( 'active_sitewide_plugins' );
                $active_plugins[ $plugin ] = time();
                update_site_option( 'active_sitewide_plugins', $active_plugins );
            } else {
                /* activate on a single blog */
                $active_plugins = get_option( 'active_plugins' );
                array_push( $active_plugins, $plugin );
                update_option( 'active_plugins', $active_plugins );
            }
          
            wp_safe_redirect(admin_url('plugins.php?activate=true') );

            //$result['pro_plugin_is_activated'] = true;
        } elseif ( empty( $result['error'] ) ) {
            $result['error'] = __( "Failed to download the zip archive. Please, upload the plugin manually", 'newswire' );
        }

        if ( $result['error'] ) {
            //add_action('admin_notices' , create_function($result['error'], 'function($message){ echo "<p class=\'updated error\'>$message</p>"; }'));
            wp_die( $result['error'] , 'Install Plugin', $args );
        }


    }

}//end function
endif;


/**
 * Generate pressroom query for pressroom page
 * The list include the last press release
 */
function newswire_pressroom_query() {

    global $newswire_pressroom_query;

    if ($newswire_pressroom_query && is_a($newswire_pressroom_query, 'WP_Query')) {

        return $newswire_pressroom_query;
    }
    /*
     * The WordPress Query class.
     * @link http://codex.wordpress.org/Function_Reference/WP_Query
     *
     */
    $options = newswire_options();

    $page = (get_query_var('paged')) ? get_query_var('paged') : 1;

    //include press release with pressroom tag
    $tag = get_term_by('name', 'pressroom', 'post_tag');

    $args = array(
        //Choose ^ 'any' or from below, since 'any' cannot be in an array
        'post_type' => array_unique(array_merge(array(
            'pr',
            'pin_as_text',
            'pin_as_link',
            'pin_as_contact',
            'pin_as_social',
            'pin_as_quote',
            'pin_as_embed',
            'pin_as_image',

        ), $options['supported_post_types'])),
        //'tag__not_in' => !empty($tag) ? $tag->term_id : '',
        //'tag__in' => !empty($tag) ? $tag->term_id : '',
        'post_status' => array(
            'publish',
        ),

        //Order & Orderby Parameters
        'order' => 'ASC',
        'orderby' => 'menu_order date',
        'ignore_sticky_posts' => true,

        //Pagination Parameters
        'posts_per_page' => 20,
        'posts_per_archive_page' => 20,
        'nopaging' => false,
        'paged' => $page,
        //'offset'                 => 3,

        //Custom Field Parameters
        //'meta_key'       => 'key',
        //'meta_value'     => 'value',
        //'meta_value_num' => 10,
        //'meta_compare'   => '=',
        //'meta_query'     => array(
        //  array(
        //      'key' => 'color',
        //      'value' => 'blue',
        //      'type' => 'CHAR',
        //      'compare' => '='
        //  ),
        //  array(
        //      'key' => 'price',
        //      'value' => array( 1,200 ),
        //      'compare' => 'NOT LIKE'
        //  ),

        //Taxonomy Parameters
        //'tax_query' => array(
        //'relation'  => 'AND',
        //  array(
        //      'taxonomy'         => 'color',
        //      'field'            => 'slug',
        //      'terms'            => array( 'red', 'blue' ),
        //      'include_children' => true,
        //      'operator'         => 'IN'
        //  ),
        //  array(
        //      'taxonomy'         => 'actor',
        //      'field'            => 'id',
        //      'terms'            => array( 1, 2, 3 ),
        //      'include_children' => false,
        //      'operator'         => 'NOT IN'
        //  )
        //),

        //Permission Parameters -
        // 'perm' => 'readable',

        //Parameters relating to caching
        //'no_found_rows'          => false,
        //'cache_results'          => true,
        //'update_post_term_cache' => true,
        //'update_post_meta_cache' => true,

    );

    $newswire_pressroom_query = new WP_Query($args);

    return $newswire_pressroom_query;

}


add_action('wp_head', 'newswire_print_newswroom_custom_style', 100);
function newswire_print_newswroom_custom_style() {
    $options = newswire_options();
    echo '<style type="text/css">';
    if ( current_user_can('administrator' )) :
        echo '.navbar-fixed-top { top: 32px};';
    endif;
    echo $options['custom_css'];
    echo $options['pressroom_custom_css'];

    echo '</style>';
}

/**
 * wp version 3.7 and up
 * resttric from media access only for PRReporter
 */
add_filter('ajax_query_attachments_args', "newswire_user_restrict_media_library");
function newswire_user_restrict_media_library($query) {

    global $current_user;
    $user = wp_get_current_user();

    //if ( in_array('PRReporter', $user->roles) && count($user->roles) == 1 && current_user_can('prreporter_cap') ) {
    //
    if (current_user_can('prreporter_cap_only')) {
        $query['author'] = $user->ID;
    }

    return $query;
}


add_theme_support('post-thumbnails');
add_image_size('pin_as_image_thumb', 300, 300, true); // Hard Crop Mode
add_image_size('pin_image_size1', 300, 300, true); // Hard Crop Mode
add_image_size('pin_image_size2', 200, 200, true); // Hard Crop Mode
add_image_size('pin_image_size3', 150, 200, true); // Hard Crop Mode
add_image_size('pin_image_size4', 300, 200, true); // Hard Crop Mode
add_image_size('pin_image_size5', 300, 400, true); // Hard Crop Mode

/**
 * Add thumnail support for 300x300
 */
if (function_exists('add_theme_support')) {
    /**
     * Setup image size and post thumbnail
     */
    function newswire_image_setup() {
        add_theme_support('post-thumbnails');
        add_image_size('newswire_thumb', 640, 640, true); //300 pixels wide (and unlimited height)
    }
    add_action('after_setup_theme', 'newswire_image_setup');

    /**
     * Setup image custom sizes
     */
    function my_custom_sizes($sizes) {
        return array_merge($sizes, array(
            'newswire_thumb' => __('Newswire Thumb'),
        ));
    }
    add_filter('image_size_names_choose', 'my_custom_sizes');
}

// Internationlization
// load_theme_textdomain( 'newswire', NEWSWIRE_PLUGIN_ASSETS_DIR . '/languages' );
/**
 * Register post status
 *
 */
//add_action('init', 'newswire_register_post_status');
function newswire_register_post_status() {

    //$client = Newswire_Client::getInstance();
    //$result = $client->post('article/sentbacklist', null);
    //var_dump(wp_remote_retrieve_body($result));

    register_post_status('re-do', array(
        'label' => _x('Sentback', 'post'),
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'public' => true,
        '_builtin' => true, /* internal use only. */
        'label_count' => _n_noop('Sentback <span class="count">(%s)</span>', 'Sentback <span class="count">(%s)</span>'),
    ));

    register_post_status('sponsored', array(
        'label' => _x('Sponsored', 'post'),
        'public' => true,
        '_builtin' => true, /* internal use only. */
        'label_count' => _n_noop('Sponsored <span class="count">(%s)</span>', 'Sponsored <span class="count">(%s)</span>'),
    ));

    register_post_status('archived', array(
        'label' => _x('Archived', 'post'),
        'public' => true,
        '_builtin' => true, /* internal use only. */
        'label_count' => _n_noop('Archived <span class="count">(%s)</span>', 'Archived <span class="count">(%s)</span>'),
    ));

}


if ( !function_exists('newswire_maybe_insert_mainphoto')):
/**
 * Single Press Release content related hooks.
 *
 *
 * Head hook if press release page
 */
add_action('wp_head', 'newswire_maybe_insert_mainphoto', 2);
function newswire_maybe_insert_mainphoto() {

    global $post;

    wp_reset_postdata();

    //google badge
    echo '<script src="https://apis.google.com/js/platform.js" async defer></script>';

    if (is_object($post) && get_post_type($post->ID) == 'pr' && is_single()) {
        
        remove_action( 'wp_head', 'rel_canonical' );
        //hide company info
        if ($data = newswire_data()) {
            if ( empty($data['show_company_info']) ) {
                printf("<style>%s</style>", '#company_nap,#company_desc_wrapper{display:none}');
            }
        }
        //print canonical url meta tag
        newswire_canonical_url( $post, true);

        
        //insert photo
        /** insert photo to pr object content */
        if (!has_post_thumbnail($post->ID)) {
            add_filter('the_content', 'newswire_insert_photo_to_pr_content');
        } elseif (function_exists('newsninja_seo_breadcrumbs')) { //Theme related
            add_filter('the_content', 'newswire_insert_photo_to_pr_content');
        }

        function newswire_insert_photo_to_pr_content($content) {

            $html = '';
            /*
            img_caption: "Original Watermen",
            img_alt_tag: "Original Watermen",
            img_caption_link: "http://www.originalwatermen.com",
            img_alt_tag_link: "http://www.originalwatermen.com",
             */

            $meta = wp_parse_args(newswire_data() , array('img_alt_tag'=> '', 'img_caption' => '', 'img_caption_link' => ''));
            
        
            if ( !empty($meta['include_image'])  )
                $html = sprintf('<p><img src="%s" title="%s" alt="%s" border=0 width="auto"> <br> <a href="%s" class="aligncenter pressroom-photocaption" >%s</a> </p>',
                /*$meta['img_url']*/newswire_image_url( get_the_ID(),  $meta['img_url'] ), $meta['img_caption'], $meta['img_alt_tag'], $meta['img_caption_link'] ? $meta['img_caption_link'] : '#', $meta['img_caption']);
            
            return $html . $content;
        }

        //insert description to pr content
        add_filter('the_content', 'newswire_insert_description');
        function newswire_insert_description($content) {

            $meta = newswire_data();
            //var_dump($meta);

            $html = !empty($meta['description']) ? $meta['description'] : '';

            return $html . $content;
        }
    }
} //end function
endif;

/**
 * Add tinymce callback, make sure to call this from support post type exluding editing page
 *
 *  1. Take care of automatic paragraph and newline to break convertion
 *  2. add noneditable tinymce plugin
 *  3. remove quicktags from pin_as_quote and pin_as_social
 *
 * @todo: subject for optimization
 *
 */
add_action('admin_head', 'newswire_tinymce_mods_init');
function newswire_tinymce_mods_init() {

    global $post_type, $post;

    $newswire_options = newswire_options();

    $newswire_screen = get_current_screen();

    //if ( $newswire_screen->base == 'post' && in_array( $post_type, $newswire_options['supported_post_types'] ) ):
    if ($post_type == 'pin_as_embed' || $post_type == 'pin_as_social') {
        function newswire_remove_quicktags() {
            // return false shows php notice
            return array('buttons'=>  null);
        }
       add_filter('quicktags_settings', 'newswire_remove_quicktags');
    }
  
    if ( $newswire_screen->base == 'post' && in_array($post_type,  array('pr') ) ):
        
        if (get_post_meta($post->ID, 'rss_source_url', true)) {
            return;
        }

        //if ( is_disable_submission( $post->ID ) ) return;

        /**
         * enable noneditable plugin
         */
        add_filter('tiny_mce_before_init', 'newswire_tinymce_init_callback');
        function newswire_tinymce_init_callback($settings) {

            $settings['setup'] = "function(ed){
                         ed.onInit.add(window.newswire.tinymce_init);
                         ed.onLoadContent.add(window.newswire.tinymce_onloadcontent);
                    }";
           /* $settings['apply_source_formatting'] = false;
            $settings['wpautop'] = false;
            // Don't remove line breaks
            $settings['remove_linebreaks'] = false;
            // Convert newline characters to BR tags
            $settings['convert_newlines_to_brs'] = true;
            // Do not remove redundant BR tags
            $settings['remove_redundant_brs'] = false;

            $settings['remove_linebreaks'] = false;
            */
            $settings['plugins'] .= ',noneditable';

            return $settings;
        }
        

        /**
         * Add wyswyg plugin
         */
        add_filter('mce_external_plugins', 'newswire_tinymce_mode', 999);
        function newswire_tinymce_mode($plugins) {
            global $tinymce_version;

            if (version_compare($tinymce_version, '4.0.0', '>=')) {
                $plugpath = NEWSWIRE_PLUGIN_URL . '/assets/js/tinymce/plugins/noneditable/plugin.min.js';
            } else {
            $plugpath = NEWSWIRE_PLUGIN_URL . '/assets/js/tinymce/plugins/noneditable/editor_plugin.js';
        }

        //$plugpath = NEWSWIRE_PLUGIN_URL . '/assets/js/tinymce/plugins/noneditable/editor_plugin.js';
        $plugins['noneditable'] = $plugpath;

        return $plugins;

    }

    endif;
}



if ( !function_exists('newswire_add_fbroot')):
add_action('wp_footer', 'newswire_add_fbroot');
/**
* Add Fbroot markup
*/
function newswire_add_fbroot() {
    
    if ( !is_newswire_pressroom_page() ) return;

    ?><div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script><?php
}
function newswire_print_default_fb() {
    ?><div class="fb-page" data-href="https://www.facebook.com/newswires" data-width="300" data-height="600" data-hide-cover="false" data-show-facepile="true" data-show-posts="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/newswires"><a href="https://www.facebook.com/newswires">Newswire</a></blockquote></div></div>
    <?php
}
endif;