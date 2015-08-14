<?php
/* --------------------------------------------------------
 Custom Table Column For listing data using wordpress way
----------------------------------------------------------- */



if ( !function_exists('newswire_show_custom_column_pressroom_blocktype_header')):
/*
* All PressRoom Blocks Add custom columns block type from pressroom listing table
*/ 
function newswire_pressroom_custom_column_header( $columns ) {
    $date = $columns['date'];
    unset($columns['date']);
    $columns[ 'pressroom_blocktype' ] = 'Block Type';
    $columns[ 'pressroom_indicator' ] = 'Visible on Pressroom Page?';
    $columns['author'] = __( 'Author','newswire' );
    $columns['date'] = $date;
        return $columns;
}
// send the filter hook
foreach(array('pressroom') as $cpt) {
    add_filter( "manage_edit-{$cpt}_columns", 'newswire_pressroom_custom_column_header', 1  );   
}

function newswire_show_custom_columns( $column_name, $post_id ) {    
    if ( $column_name == 'author') {
        echo get_the_author($post_id);
        return;
    }

    if ( 'pressroom_blocktype' == $column_name ) {
         $cpts = newswire_pressroom_postypes();
        echo $cpts[get_post_type( $post_id )];
        return;
    }

    if ( 'pressroom_indicator' == $column_name && get_post_type( $post_id ) == 'pr' ) {

        if ( has_term('pressroom', 'post_tag', $post_id ) ) {
            echo "Yes";
        } else {
            echo 'No';
        }
    } else {
        echo "Yes";
    }

   
}

foreach( array_keys(newswire_pressroom_postypes()) as $cpt )
    add_action( "manage_{$cpt}_posts_custom_column", 'newswire_show_custom_columns', 10, 2 );
endif;


/**
* Plugin 
* Create settings link
*/
add_filter( 'plugin_action_links_'.NEWSWIRE , 'newswire_add_settings_link' );
function newswire_add_settings_link($links) {
    $settings_link = '<a href="edit.php?post_type=pressroom&page=newsroom-settings">Settings</a>';
    $links = array_merge(array($settings_link), $links );
    return $links;
}



if ( !function_exists('newswire_excerpt_length')):
add_filter( 'excerpt_length', 'newswire_excerpt_length', 999 );
function newswire_excerpt_length( $length ) {
    global $post;
    $type = get_post_type( $post->ID );
    if ( 'pr' == $type )
        return 255;
}
endif;

/**
 * When newswire_option is update fetch categories from newswire
 */
add_action('update_option_' . NEWSWIRE_OPTIONS, 'newswire_fetch_categories_when_option_updated', 12, 2);
function newswire_fetch_categories_when_option_updated($old_value, $value) {

    if ( !empty($value['newsroom_page_template'] ) ) :
        $url = get_permalink($value['newsroom_page_template']);

        if (!empty($url)) {
            newswire_fetch_categories($force = 1);
        }
    endif;
}



/**
* Move all functions here that needs to be pluggable
*/
if (!function_exists('newswire_newsroom_nextpage')):
function newswire_newsroom_nextpage() {
    
    $options = newswire_options();
    
    $newsroom_page = $options['newsroom_page_template'];
    
    $paged = ($_GET['next']) ? $_GET['next'] : 1;

    $next_page = get_permalink( $newsroom_page ) .'?next='. intval($paged + 1 ) ;
    
    return sprintf('<a href="%s"></a>', $next_page );
}
endif;

