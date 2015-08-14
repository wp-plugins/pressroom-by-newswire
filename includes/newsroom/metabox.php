<?php

if ( !function_exists('newswire_add_post_meta_box')):
/*
 * Add PR meta box 
 *
 */
add_action('add_meta_boxes', 'newswire_add_post_meta_box');
function newswire_add_post_meta_box() {

    global $post;

    $type = get_post_type($post);

    //if ( !get_post_meta($post->ID, NEWSWIRE_ARTICLE_SUBMITTED, true ) )  return ;

    //if ( get_post_meta( $post->ID, 'rss_source_url', true)  ) return;

/*  if ($type == 'pr') {

        if (current_user_can('administrator')) {
            add_meta_box($id = 'nwire-post-meta-side', $title = 'Publication Options', $callback = 'newswire_metabox_prcheckboxes', $screen, $context = 'side', $priority = 'core', $callback_args = null);
        }

    }
*/  
    
    
//add tooltip meta box
    if ( in_array($type, array('pin_as_text','pr', 'pin_as_embed', 'pin_as_image', 'pin_as_quote', 'pin_as_social', 'pin_as_link', 'pin_as_contact')) )
        add_meta_box($id = 'pressroom-tooltips', $title = 'Tool Tips', $callback = 'newswire_pressroom_show_tooltip_metabox', $type, $context = 'side', $priority = 'core', $callback_args = null);
   
    
    //skip if disabled
    //if ( is_disable_submission( $post->ID ) ) return;

    $options = newswire_options();

    $options['supported_post_types'] = array('pr');

    if (sizeof($options['supported_post_types'])) {
        foreach ($options['supported_post_types'] as $screen) {
            //
            //add publishing box
            add_meta_box($id = 'nwire-post-meta', $title = 'PressRoom by Newswire', $callback = 'newswire_pr_meta_box_callback', $screen, $context = 'advanced', $priority = 'high', $callback_args = null);
            // /add_meta_box( $id = 'nwire-post-meta-side', $title = 'PressRoom by Newswire', $callback = 'newswire_metabox_prcheckboxes', $screen, $context ='side' , $priority = 'core', $callback_args = null );
        }

    } else {

        add_meta_box($id = 'nwire-post-meta', $title = 'PressRoom by Newswire', $callback = 'newswire_pr_meta_box_callback', $screen = 'pr', $context = 'advanced', $priority = 'high', $callback_args = null);
    }
}
endif;


if ( !function_exists('newswire_pr_meta_box_callback')):
/**
 *
 * add meta box to post
 *
 **/
function newswire_pr_meta_box_callback() {

    global $post, $newswire_config;

    $post_meta = wp_parse_args( get_post_meta($post->ID, NEWSWIRE_POST_META_CUSTOM_FIELDS, $single = true), 
            array('article_status' => '', 'category' => '')
        );

    //skip from rss

    ?>
    <div id="newswire-post-meta-box">
    <?php
        wp_nonce_field(NEWSWIRE_POST_META_NONCE, NEWSWIRE_POST_META_NONCE);
    ?>
    <div id="newswire-article-meta-tab">
        <div class="newswire-article-status ">
            <?php
                echo newswire_generate_meta_box_fields(newswire_post_meta_box_elements('article_status'), $post_meta);
            ?>
        </div>
    <?php

    if ($GLOBALS['typenow'] != 'pr'): ?>
        <div style="float: right">
            <a type="button" class="button button-primary" id="newswire_disable_submission">Disable Submission</a>&nbsp;<span class="howto"><img src="<?php echo NEWSWIRE_PLUGIN_URL ?>/assets/images/help.png" title="Prevent this post to be submitted to newswire" ></span>
        </div>
        <?php
    endif;
    ?>
    <div class="newswire-section">
    <?php
        
       // echo newswire_generate_meta_box_fields(newswire_post_meta_box_elements('category'), $post_meta);

        echo newswire_generate_meta_box_fields(newswire_post_meta_box_elements('bonus_category'), $post_meta);
    ?>
    </div>

        <ul class="newswire-tabs">

            <li class="newswire-tab "><a href="#nwire-author-info"><?php _e('Author Info');?></a></li>
            <li class="newswire-tab hide-if-no-js"><a href="#nwire-company-info"><?php _e('Company Info');?></a></li>      
            <li class="newswire-tab hide-if-no-js"><a href="#newswire-add-image"><?php _e('Add Image');?></a></li>
        </ul>

        <div id="nwire-author-info" class="newswire-tabs-panel">

            <p class="howto"><?php echo $newswire_config['tooltip']['author_info_tab']?></p>
            <?php
                echo newswire_generate_meta_box_fields(newswire_post_meta_box_elements('author_info'), $post_meta);
            ?>
        </div>

        <div id="nwire-company-info" class="newswire-tab-panel"  style="display: none;">

            <p class="howto"><?php echo $newswire_config['tooltip']['company_info_tab']?></p>
            <?php
                echo newswire_generate_meta_box_fields(newswire_post_meta_box_elements('company_info'), $post_meta);
            ?>
        </div>

        <!-- add image //-->
        <div id="newswire-add-image">

            <div>
                <p class="howto" style="float:none"> <button class="newswire_update_image">Select Image</button> Upload a 16:9 aspect ratio .jpg image that is a minimum of 640px in width. Image upload required.</p>

            </div>
            <div>
                <div style="display: block; width: 30%; float: left">
                    <input type="hidden" name="newswire_data[img_url]" value="<?php echo isset($post_meta['img_url']) ? $post_meta['img_url'] : ''?>">
                    <div class="newswire-image-preview">
                    <?php

                        if ( isset($post_meta['img_url']) && $post_meta['img_url'] != '') {
                            echo '<img src="' . $post_meta['img_url'] . '" >';
                        }

                    ?></div>
                </div>

                <div>
                    <?php echo newswire_generate_meta_box_fields(newswire_post_meta_box_elements('add_image'), $post_meta);?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
endif;




if ( !function_exists('newswire_pressroom_show_tooltip_metabox')):
/**
* Tooltip Metabox
*/
function newswire_pressroom_show_tooltip_metabox() {
    global $post;
    
    $type = get_post_type($post);

    $tips =  newswire_config( $type, 'pressroom_metabox_tooltip');

    //echo $type;
    echo '<div class="tooltip">';   
    if ( $tips )
        echo $tips;
    //echo 'Tooltip Section Tooltip Section Tooltip Section Tooltip Section Tooltip Section Tooltip Section Tooltip Section Tooltip Section Tooltip Section Tooltip Section Tooltip Section ';
    echo '</div>';
}   
endif;