<?php



/*
* Settings being saved
*
*/

/**
* Pin as image - save attachment
*/
if ( !function_exists('newswire_save_pin_as_image')):
/**
 * Admin hook callback
 *
 * attach uploaded images from pin_as_image post type
 */
add_action('save_post_pin_as_image', 'newswire_save_pin_as_image');
function newswire_save_pin_as_image($post_ID, $post = null, $update = null) {
    //from pin_as_image
    // unhook this function so it doesn't loop infinitely

    remove_action('save_post_pin_as_image',  'newswire_save_pin_as_image');

    if (isset($_POST['pin_attachment']) && is_array($_POST['pin_attachment'])) {
        foreach ($_POST['pin_attachment'] as $attachment_id) {
            $attachment = array('ID' => $attachment_id, 'post_parent' => $post_ID);
            //  var_dump($attachment)   ;
            wp_update_post($attachment);
        }
    }
    //exit;
}
endif;

/*
* Content Metadata being save
*
*/

if ( ! function_exists('newswire_save_post_meta_values')):
// Do we need to save it 
///Need to save custom fields before sending to newswire
//add_action('post_updated', 'newswire_save_post_meta_values');    
add_action('transition_post_status', 'newswire_save_post_meta_values', 10, 3);
/**
 * Save custom fields for pressroom blocks
 * @todo - check with pr
 */
function newswire_save_post_meta_values($new_status, $old_status, $post) {

    //remove_action('post_updated' , 'newswire_save_post_meta_values');
    remove_action('transition_post_status' , 'newswire_save_post_meta_values');
    
    $post_id = $post->ID;
    
    if ( !in_array( get_post_type( $post ), newswire_pressroom_blocks() ) ) return;

    //skip doing cron
    if (defined('DOING_CRON') && DOING_CRON) {
        return 0;
    }

    if ( empty($_POST) ) return;

    //update post meta syndication
    if (isset($_POST)) {
        if ( !empty($_POST['newswire_copytoblog']) )  {
            update_post_meta($post_id, 'newswire_copytoblog', 'enable');
        } else {
            update_post_meta($post_id, 'newswire_copytoblog', '');
        }
    }

    if ( !empty($_POST['newswire_submission_bonus']) && isset($_POST['newswire_submission_bonus'])) {
        //
        update_post_meta($post_id, 'newswire_submission_bonus', '1');
    } else {

        update_post_meta($post_id, 'newswire_submission_bonus', 'disable');
    }

    /*
    if ( isset($_POST['newswire_submission']))
    if ( $_POST['newswire_submission'] )
    update_post_meta( $post_id, 'newswire_submission' , '1' );
    else
    update_post_meta( $post_id, 'newswire_submission' , 'disable' );
     */
    if ( !empty($_POST['disable_sumbmission']) && isset($_POST['disable_sumbmission'])) {

        update_post_meta($post_id, 'newswire_submission', 'disable');
    } else {

        update_post_meta($post_id, 'newswire_submission', '1');
    }

    // Check the user's permissions.
    if (!isset($_POST['newswire_data'])) {

        if ( get_post_type($post) != 'pr' ) {
            newswire_modify_post_update_message_redirect();
        }

        return 0;
    }

    //if (!current_user_can('edit_page', $post_id)) {
     //   $post_status = get_post_status($post);
    //}

    /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */

    // Check if our nonce is set.
    /* Verify the nonce before proceeding. */
    if (!isset($_POST[NEWSWIRE_POST_META_NONCE]) || !wp_verify_nonce($_POST[NEWSWIRE_POST_META_NONCE], NEWSWIRE_POST_META_NONCE) ) {
        return $post_id;
    }

    $newswire_options = get_option(NEWSWIRE_OPTIONS);

    /* OK, its safe for us to save the data now. */
    //if ( in_array($post_status, array('pending', 'auto-draft', 'draft', 'future', 'publish') ) )
    //{

    // Sanitize user input.
    $mydata = $_POST['newswire_data'];

    //from pin_as_link
    if (isset($_POST['delete_index'])) {

        foreach ($_POST['delete_index'] as $index) {
            unset($mydata['text'][$index]);
            unset($mydata['link'][$index]);
        }
    }

    //set postback url
    //$mydata['postback_url'] = site_url('?action=submit_postback');
    //var_dump($mydata);
    if (empty($mydata['show_company_info'])) {
        $mydata['show_company_info'] = "0";
    }
    
    if (empty($mydata['include_image'])) {
        $mydata['include_image'] = "0";
    }

    if (empty($mydata['link_name'])) {
        $mydata['link_name'] = "0";
    }

    update_post_meta($post_id, NEWSWIRE_POST_META_CUSTOM_FIELDS, $mydata);
    if ( get_post_type($post) != 'pr' ) {

        newswire_modify_post_update_message_redirect();
    }
    return 0;
}
endif;

/**
* disable newswire submission, ajax handler from
* edit pr page
*/
add_action('wp_ajax_newswire_disable_submission', 'disable_newswire_submission');
add_action('wp_ajax_nopriv_newswire_disable_submission', 'disable_newswire_submission');
function disable_newswire_submission() {
    $post = $_REQUEST['id'];
    if ( $post ) {        
        update_post_meta($post, 'newswire_submission', 'disable' );
    }
    
    exit;
}



/*
 * What does this hook do
 * validate fields must done here before save_post becuase it nees to be validated before sending to newswire
 * Sends article to newswire.net for review and publishing when a post is save from xxx status to publish, pending
 * we only validate when we are sending to newswire - so they can save it as draft etc
 * at this point. post cutsom fieldss are alrready saved to database
 *
 * Actions taken -
 *  1. validate fields
 *  2. Save custom post fields
 *  3. Send to newswire
 *
 * @fixed bugs
 *   July 10, 2014 - show_company_info field typing is jumpbing from int to string which results to invalid signing of data request from API.
 *                 - Created a callback handler to convert to string to make it consistent before computing the signature. dont care whats the type from wp point
 *
function wp_transition_post_status($new_status, $old_status, $post) {
do_action('transition_post_status', $new_status, $old_status, $post);
do_action("{$old_status}_to_{$new_status}", $post);
do_action("{$new_status}_{$post->post_type}", $post->ID, $post);
}
 */
if ( !function_exists('newswire_submit_article')):
//try to submit article onetime  anywhere from wp to newswire
add_action('newswire_manual_submission', 'newswire_submit_article', 12);
add_action('draft_to_pending', 'newswire_submit_article', 12);
add_action('pending_to_publish', 'newswire_submit_article', 12);
add_action('draft_to_publish', 'newswire_submit_article', 12);
add_action('sentback_to_publish', 'newswire_submit_article', 12);
//add_action('sentback_to_draft', 'newswire_submit_article', 12);
add_action('sentback_to_pending', 'newswire_submit_article', 12);
add_action('re-do_to_publish', 'newswire_submit_article', 12);
//add_action('re-do_to_draft', 'newswire_submit_article', 12);
add_action('re-do_to_pending', 'newswire_submit_article', 12);
add_action('new_to_pending', 'newswire_submit_article', 12);
add_action('new_to_publish', 'newswire_submit_article', 12);
add_action('future_to_publish', 'newswire_submit_article', 12);
add_action('future_to_pending', 'newswire_submit_article', 12);
add_action('auto-draft_to_pending', 'newswire_submit_article', 12);
add_action('auto-draft_to_publish', 'newswire_submit_article', 12);
add_action('publish_to_publish', 'newswire_submit_article', 12);
//add_action('wp_transition_post_status', 'newswire_submit_article');
//add_action('wp_insert_post', 'newswire_submit_post_callback');
//add_action('save_post', 'newswire_remote_post');
function newswire_submit_article($post) {

    global $wpdb;

    //get post id
    $post_ID = $post->ID;

    //  clean_post_cache( $post_ID );
    //  $post = get_post( $post_ID );

    if (!current_user_can('publish_newswire_prs_to_pressroom') && current_user_can('PRReporter')) {
        //if they can'tp publish pr to pressroom send them to newsroom only
        wp_set_post_tags($post_ID, 'newsroom');

    } else {
        //remove tag newsroom here
        //wp_set_post_tags($post_ID, 'pressroom' );
        //wp_remove_object_terms( $post_id, 'sweet', 'newsroom' );
    }

    //are we just running cron?
    if (newswire_var('cron')) {
        return 0;
    }

    //get post type
    $post_type = get_post_type($post_ID);

    //get newsqwire settings
    $newswire_settings = newswire_options();

    //skip if post_type is not supported
    //if ( !in_array( $post_type, (array)$newswire_settings['supported_post_types'] ) ) {
    if (!in_array($post_type, array('pr'))) {

        return 0;
    }

    $filter = current_filter();

    list($old_status, $new_status) = explode('_to_', $filter);

    //if doing cron skip
    if (defined('DOING_CRON') && DOING_CRON) {
        return 0;
    }

    //die('test2');

    //skip autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return 0;
    }

    // if no post_id
    if (!$post_ID) {
        return 0;
    }

    // Check the user's permissions.
    if ('page' == $_POST['post_type']) {

        if (!current_user_can('edit_page', $post_ID)) {
            return 0;
        }

    } else {

        if (!current_user_can('edit_post', $post_ID)) {
            return 0;
        }

    }

    $wp_post_status = get_post_status($post_ID);

    $post_meta = newswire_get_post_meta($post_ID);

    //newswire_update_post_status($post_ID, $old_status );
    $mydata = $post_meta;

    //skip?
    //if (is_disable_submission($post_ID)) {
    //  return;
    //}

    if ($old_status == 'publish') {
        //return;
    }
    //validation
    //From here on - submission to newswire

    /**
     * validate fields before submitting to newswire
     */
    $validator = new Newswire_Validator();

    if (!$validator->isValid($post)) {

        newswire_update_post_status($post_ID, $old_status);

        $validator->write_error_notice();

        newswire_override_post_save_message_redirect('pending');

        return $post;
    }


    //freesites routine start
    $sync = get_post_meta($post_ID, 'freesites_submission', true );
    
    if ( intval($sync) < 2 && $newswire_settings['api_validated'] ):

        if ( newswire_submission_type($post_ID) == 'freesites'  ) {

            //$stamp = time() + 3; //time() + ( 72 * 3600);
            
            $stamp = time() + ( 72 * 3600); //final stamp
            
            //cron it
            wp_schedule_single_event( $stamp, 'cron_freesite_sync_single', array( $post_ID ) );
        
            update_post_meta($post_ID, 'freesites_submission', 1, 0 );
            
            return;

        } else  {

            //unschedule it?
            //wp_unschedule_event( intval($stamp), 'cron_freesite_sync_single', array($post_ID) );
            wp_clear_scheduled_hook( 'cron_freesite_sync_single', array( $post_ID ) );

            update_post_meta($post_ID, 'freesites_submission', 0 , 1);
        }
        //exit;
    endif;
    //free site routin end


    
    if ( newswire_submission_type($post_ID) == 'freesites' ) {
        
        return $post;

    }

    // $newswire_settings['force_submission'];
    // $newswire_settings['article_submission_mode'] == 'autosubmit';
    // skip manual allow user to publish content locally
    // if manual submission dont send to neswire
    // test this
    if (in_array($filter, array('draft_to_publish', 'pending_to_publish', 'wp_transition_post_status')) && 'manual' == $newswire_settings['article_submission_mode'] && !$newswire_settings['force_submission']) {
        newswire_update_post_status($post_ID, $new_status);
        return;
    }

    if ($new_status == 'pending') {
        newswire_override_post_save_message_redirect('pending');
    }

    
}
endif;


if ( ! function_exists('newswire_clone_pr_to_post')):
/**
 * Clone PressRelease to Blog/Post
 * 
 */
add_action('save_post', 'newswire_clone_pr_to_post');
function newswire_clone_pr_to_post($post_id) {
    //skip non pr post
    $post_type = get_post_type($post_id);

    if (!in_array($post_type, array('pr'))) {
        return 0;
    }

    remove_action('save_post', 'newswire_clone_pr_to_post' );
    
    //remove opst
    //skip if not coming from backend
    //ignore from api
    if ( empty($_POST['newswire_data']) ) {
        return;
    }

    //skip doing cron

    if (defined('DOING_CRON') && DOING_CRON) {
        return 0;
    }


    
    $_POST['newswire_copytoblog'] = !empty($_POST['newswire_copytoblog']) ? $_POST['newswire_copytoblog'] : '';

    //check if cloned already

    $cloned = get_post_meta($post_id, 'newswire_cloned', true );

    $post = get_post($post_id);

    if ( get_post( $cloned ) && $cloned && $_POST['newswire_copytoblog'] ) {

        newswire_clone_pr($post, true);
        //update_post_meta($post_ID, 'newswire_cloned', $new_id );
    } else if ($post->post_status == 'publish' && $_POST['newswire_copytoblog'] && isset($_POST['newswire_copytoblog'])) {
    

        $new_id = newswire_clone_pr($post);

        update_post_meta($post_id, 'newswire_cloned', $new_id);

    }
    
    newswire_fetch_categories(1);
    //wp_set_post_tags($post->ID, array('pressroom', 'newsroom', 'news'), true);
    
    
    //remove this from light version
    newswire_update_pr_tags($post->ID);
    
    if ( !empty($_POST['include_on_pressroom_page']) ) {

        wp_set_post_tags($post->ID, 'pressroom', true);

    } else {

        wp_remove_object_terms($post->ID, 'pressroom', 'post_tag');
    }
    

}
endif;


function newswire_update_pr_tags($post_id) {
    $post = get_post($post_id);

    $cats = get_option('newswire_categories_flat');
    //set post tags based on 
    $meta = newswire_data($post->ID);
    $tags = array('newsroom');

    if ( is_array($cats) )
        foreach($cats as $catid=>$catname) {
            //if ( $catid == $meta['category_id'] ) $tags[] = $catname;
            if ( !empty($meta['category_id2']))
                if ( $catid == $meta['category_id2'] ) $tags[] = $catname;
                
        }
    if ( $tags )
        wp_set_post_tags( $post_id, $tags, false);
}

/**
*
*/
add_filter('post_updated_messages', 'newswire_post_updated_messages');
function newswire_post_updated_messages($messages) {
    global $post;
    $options = newswire_options();
    $url = get_permalink($options['pressroom_page_template']);
    //submitted to newswire
    $messages['post']['100'] = sprintf(__('Press release submitted to newswire. <a target="_blank" href="%s">Preview post</a>'), esc_url(add_query_arg('preview', 'true', get_permalink($post->ID))));
    $messages['post']['200'] = sprintf(__('Press release was reverted as draft.'));
    $messages['post']['991'] = sprintf(__('Post updated. <a href="%s" target="_blank">View post.</a>'), $url );

    return $messages;
}

/**
* handle redirecting to edit post after save/update
*
*/
function newswire_block_redirect_update($location) {
    $location = add_query_arg('message', 991, $location);
    return $location;
}

function newswire_update_post_redirect_error($location) {
    $location = add_query_arg('message', 200, $location);
    return $location;
}

function newswire_update_post_redirect_pending($location) {
    $location = add_query_arg('message', 100, $location);
    return $location;
}
function newswire_override_post_save_message_redirect($status = '') {

    $error = newswire_admin_get_error();

    if (!empty($error)) {

        add_filter('redirect_post_location', 'newswire_update_post_redirect_error');
    } elseif ($status == 'pending' && function_exists('newswire_config')) {
        add_filter('redirect_post_location', 'newswire_update_post_redirect_pending');

    }
}

function newswire_modify_post_update_message_redirect(){
    add_filter('redirect_post_location', 'newswire_block_redirect_update');
}

/**
 * Send article to newswire
 *
 * @param $post $wp_post object
 * @Param $newswire_article_id integer actual article id from newswire
 *
 */
function newswire_remote_submit_article($post = null, $newswire_article_id = 0, $create = 0) {

    $data = newswire_api_prepare_http_data($method = 'submit', $post);

    $client = Newswire_Client::getInstance();

    //set timeout?
    if ($create) {
        $response = $client->submit_article($data);
    } else {
        $data['body']['article_id'] = $newswire_article_id;
        # code...
        // The only reason why
        //die('updating article');
        $response = $client->post('/article/submit/', $data);

        if (!is_wp_error($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if ($data['article_id'] && $data['success']) {
                newswire_update_post_status($post->ID, 'pending');
            }
        }

    }

    return $response;
}

/**
 ** Prepare data to be submitted to newswire
 *  use callbacks indicated from config file
 */
function newswire_api_prepare_http_data($method, $post) {

    global $newswire_config;

    $request = array();

    setup_postdata($post);

    $post_meta = newswire_get_post_meta($post->ID);

    //if creating new post
    switch ($method) {

        case 'submit':

            $body = array();

            $callbacks = newswire_config('article_fields_handler');

            foreach (array_keys($newswire_config['settings']['article_fields']) as $field) {

                if (function_exists($callbacks[$field])) {
                    $body[$field] = call_user_func($callbacks[$field], $post, $post_meta[$field]);
                }
            /* watch out when writing callback filter */
                elseif (isset($post_meta[$field])) {
                    $body[$field] = $post_meta[$field];
                } else {
                    unset($body[$field]);
                }

            }

            break;
    } //end switch

    $request['body'] = array('article' => $body, 'postback_url' => site_url('?action=submit_postback&post_id=' . $post->ID));
    //debug?
    //var_dump($request);
    //exit;
    return $request;
}

function newswire_get_the_title_fieldmap($post, $val = null) {
//  setup_postdata( $post );
    return apply_filters('newswire_submit_article_title', $post->post_title);

}

function newswire_get_wp_excerpt_fieldmap($post, $val = null) {
//  setup_postdata( $post );
    return apply_filters('newswire_submit_article_description', $post->post_excerpt);

}

function newswire_get_the_content_fieldmap($post, $val = null) {
    //setup_postdata( $post );
    return apply_filters('newswire_submit_article_body', $post->post_content);

}
/*function newswire_show_company_info_fieldmap($post, $val = null){

return strval($val);
}*/

function newswire_get_current_user_email($post, $val) {

    global $display_name, $user_email;

    get_currentuserinfo();

    if ($val != '') {
        return $val;
    }

    return $user_email;
}

function newswire_get_current_user_displayname($post, $val) {
    global $display_name, $user_email;

    get_currentuserinfo();

    if ($val != '') {
        return $val;
    }

    return $display_name;

}
