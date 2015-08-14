<?php

if ( !function_exists('newswire_add_role_caps')):
/**
 * Creawte custom Roles and capabilities
 */
add_action('admin_init', 'newswire_add_role_caps', 999);
function newswire_add_role_caps() {
    

    //set_plugin_widgets_default();
    // maybe_create_singlepr_template();
    // maybe_autocreate_roles();
    
    // Add the roles you'd like to administer the custom post types
    $roles = array('editor', 'administrator');
    // Loop through each role and assign capabilities
    foreach ($roles as $the_role) {

        $role = get_role($the_role);
        if (!$role) {
            continue;
        }

        //press releases caps
        $role->add_cap('read');
        $role->add_cap('edit_newswire_pr');
        $role->add_cap('read_newswire_pr');
        $role->add_cap('delete_newswire_pr');

        $role->add_cap('edit_newswire_prs');
        $role->add_cap('edit_others_newswire_prs');
        $role->add_cap('publish_newswire_prs');
        $role->add_cap('read_private_newswire_prs');
        $role->add_cap('delete_newswire_prs');
        $role->add_cap('delete_private_newswire_prs');
        $role->add_cap('delete_published_newswire_prs');
        $role->add_cap('delete_others_newswire_prs');
        $role->add_cap('edit_private_newswire_prs');
        $role->add_cap('edit_published_newswire_prs');

        //create_posts

        //pressroom blocks caps
        $role->add_cap('edit_pressroom_block');
        $role->add_cap('read_pressroom_block');
        $role->add_cap('delete_pressroom_block');

        $role->add_cap('edit_pressroom_blocks');
        $role->add_cap('edit_others_pressroom_blocks');
        $role->add_cap('publish_pressroom_blocks');
        $role->add_cap('read_private_pressroom_blocks');
        $role->add_cap('delete_pressroom_blocks');
        $role->add_cap('delete_private_pressroom_blocks');
        $role->add_cap('delete_published_pressroom_blocks');
        $role->add_cap('delete_others_pressroom_blocks');
        $role->add_cap('edit_private_pressroom_blocks');
        $role->add_cap('edit_published_pressroom_blocks');
        $role->add_cap('ordering_blocks');
        $role->add_cap('publish_newswire_prs_to_pressroom');

    }
    

    /*
    * 
    * set specific caps to author or PRREporter
    */
    $roles = array('author', 'PRReporter');
    // Loop through each role and assign capabilities
    foreach ($roles as $the_role) {

        $role = get_role($the_role);

        if (!$role) {
         
            continue;
        }
    }

    /*
    *
    */
    add_filter('map_meta_cap', 'newswire_check_user_cap', 9999, 4);
    function newswire_check_user_cap($caps, $cap, $user_id, $args = array()) {

        return $caps;
    }

    //disallow pr menu from PRREporter and author

}
endif;