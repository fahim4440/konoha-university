<?php

    if(!is_user_logged_in()) {
        wp_redirect(esc_url(site_url('/')));
    }


    get_header();
    while(have_posts()) {
        the_post(); 

        page_banner();
    ?>

        <div class="container container--narrow page-section">

            <div class="create-note">
                <h2 class="headline headline--medium">Create New Note</h2>
                <input class="new-note-title" placeholder="Title"></input>
                <textarea class="new-note-body" placeholder="Your note here....."></textarea>
                <span class="submit-note">Create Note</span>
            </div>

            <ul class="link-list min-list" id="my-notes">
                <?php
                    $my_notes = new WP_Query(array(
                        "post_type" => 'note',
                        "posts_per_page" => -1,
                        "author" => get_current_user_id(),
                    ));

                    

                    while($my_notes -> have_posts()) {
                        $my_notes -> the_post(); ?>

                        <li data-id="<?php the_ID() ?>">
                            <input readonly value="<?php echo str_replace('Private: ', '', esc_attr(get_the_title())); ?>" class="note-title-field"></input>
                            <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                            <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                            <textarea readonly class="note-body-field"><?php echo esc_textarea(wp_strip_all_tags(get_the_content())); ?></textarea>
                            <span class="update-note btn btn--blue btn--small"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Save</span>
                        </li>
                        
                    <?php }
                ?>
            </ul>
        </div>
        
        <?php 
    }
    get_footer();
?>