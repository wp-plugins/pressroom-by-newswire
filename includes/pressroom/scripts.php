<?php

if ( !function_exists('newswire_load_js_and_css')):
/**
* enqueue required js and css from admin
*
* @todo: load css and js only where its needed. one way to avoid conflict and lessen the admin load
*/
add_action('admin_enqueue_scripts', 'newswire_load_js_and_css' );
function newswire_load_js_and_css() {

    global $hook_suffix;

     //localize script
    $_newswire_vars = array(
        'site_url' => get_site_url(),
        'name' => get_bloginfo(),
        'theme_directory' => get_template_directory_uri()
    );    
    wp_localize_script( 'jquery', 'siteinfo', $_newswire_vars );


    wp_enqueue_media(); 
    /* need this for some sortable box from settings later 
    wp_enqueue_script( 'jquery-ui-core'      );
    wp_enqueue_script( 'jquery-ui-tabs'      );
    wp_enqueue_script( 'jquery-ui-mouse'     );
    wp_enqueue_script( 'jquery-ui-draggable' );
    wp_enqueue_script( 'jquery-ui-droppable' );
    wp_enqueue_script( 'jquery-ui-sortable'  );
    
    */
    // global js    
    wp_register_script( 'newswire-admin', plugins_url( basename(NEWSWIRE_DIR) . '/assets/js/admin.js'), array('jquery', 'wp-color-picker'), '1.1', true );
    wp_enqueue_script( 'newswire-admin' );

    //Needed from categorys
    //wp_register_script('newswire-admin-masonry', plugins_url('assets/js/jquery.masonry.min.js', __FILE__ ), array('jquery'), null, true);
    //wp_enqueue_script( 'newswire-admin-masonry' );

    //pluploader
    //wp_enqueue_script('plupload-all');
    wp_enqueue_script('plupload-handlers');

    //post specific
    wp_register_script( 'newswire_post', plugins_url( basename(NEWSWIRE_DIR)  . '/assets/js/post.js' ), array('jquery', 'jquery-ui-tabs', 'jquery-ui-tooltip', 'jquery-ui-datepicker'), '1.1', true);
    wp_enqueue_script( 'newswire_post' );

    //global css
    wp_register_style( 'newswire_css_admin', NEWSWIRE_PLUGIN_URL . 'assets/css/admin.css');
    wp_enqueue_style( 'newswire_css_admin');

    //enqueue color picker/swash
    wp_enqueue_style('wp-color-picker'); 

}
endif;