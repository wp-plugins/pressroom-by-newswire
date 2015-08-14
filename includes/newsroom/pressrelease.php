<?php


if ( !function_exists('newswire_modify_add_new_button_link')):
add_action('all_admin_notices', 'newswire_modify_add_new_button_link');
function newswire_modify_add_new_button_link() {
    global $typenow, $post_new_file;

    if ( $typenow == 'pressroom' && $pagenow = 'edit.php') {
        $post_new_file = 'post-new.php?post_type=pr';
    }
}
endif;


if ( ! function_exists('newswire_clone_pr')):
/**
* Clone pr content to post/blog type
*/
function newswire_clone_pr($post, $update = false) {
    global $wpdb;
    /*
     * new post data array
     */
    $args = array(
        'comment_status' => $post->comment_status,
        'ping_status' => $post->ping_status,
       // 'post_author' => $new_post_author,
        'post_content' => $post->post_content,
        'post_excerpt' => $post->post_excerpt,
        'post_name' => $post->post_name,
        'post_parent' => $post->post_parent,
        'post_password' => $post->post_password,
        'post_status' => $post->post_status,
        'post_title' => $post->post_title,
        'post_type' => 'post',
        'to_ping' => $post->to_ping,
        'menu_order' => $post->menu_order,
    );

    /*
     * insert the post by wp_insert_post() function
     */
    if ($update) {
        $args['ID'] = get_post_meta($post->ID, 'newswire_cloned', true);
        if ($args['ID']) {
            wp_update_post($args);
        }

        return null;

    } else {

        $new_post_id = wp_insert_post($args);
    }
    //$post_id = $post->ID;
    /*
     * get all current post terms ad set them to the new post draft
     */
    $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
    foreach ($taxonomies as $taxonomy) {
        $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
        wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
    }

    /*
     * duplicate all post meta
     */
    $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
    if (count($post_meta_infos) != 0) {
        $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
        foreach ($post_meta_infos as $meta_info) {
            $meta_key = $meta_info->meta_key;
            $meta_value = addslashes($meta_info->meta_value);
            $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
        }
        $sql_query .= implode(" UNION ALL ", $sql_query_sel);
        $wpdb->query($sql_query);
    }

    return $new_post_id;
}
endif;

