<?php
include_once "functions.php";
include_once "includes/deprecate.php";

#classes
require_once "includes/classes/newswire-validator.php";
require_once "includes/classes/newswire-client.php";
require_once "includes/cron/newswire-freesites.php";

#global
include_once "includes/roles.php";
include_once "includes/media.php";

include_once "includes/pressroom/types.php";
include_once "includes/html/select-dropdowns.php";
include_once "includes/settings/tabs.php";

//newsroom
include_once "includes/newsroom/types.php";
include_once "includes/newsroom/pressrelease.php";
include_once "includes/newsroom/metabox.php";
include_once "includes/newsroom/filters.php";
include_once "includes/newsroom/actions/republish.php";


//pressroom
//include "includes/pressroom/actions.php";
include_once "includes/pressroom/actions/save-post.php";
include_once "includes/pressroom/scripts.php";
include_once "includes/pressroom/admin-menu.php";
include_once "includes/pressroom/admin-notices.php";
include_once "includes/pressroom/admin-filters.php";

include_once "includes/pressroom/metabox.php";
include_once "includes/pressroom/metaboxes/contact.php";
include_once "includes/pressroom/metaboxes/link.php";
include_once "includes/pressroom/metaboxes/quote.php";
include_once "includes/pressroom/metaboxes/image.php";

include_once "includes/pressroom/shortcodes/page-masonry.php"; 
include_once "includes/pressroom/widgets/blocks.php";
include_once "includes/pressroom/filters.php";
include_once "includes/activate.php";




/**
* Load all admin here
*/
if ( !function_exists('newswire_admin')):
add_action('plugins_loaded', 'newswire_admin');
function newswire_admin() {
   //var_dump('test');
    
    require_once "includes/classes/newswire-pressroom.php";
    $GLOBALS['nwpr'] = new Newswire_Pressroom;

    //newswire_pressroom_adminaction();
    //newswire_pressroom_notices();
    //newswire_pressroom_menu();
    //newswire_pressroom_metaboxes();
    
}
endif;


if ( !function_exists('newswire_init')) :
/**
* Frontend
*/
add_action('init', 'newswire_init');
function newswire_init() {

   
}
endif;

