<?php
    get_header();
    while(have_posts()) {
        the_post();

        page_banner();

    ?>

        <div class="container container--narrow page-section"> 
            
            <div class="generic-content">

                <div class="row-group">

                    <div class="one-third">
                        <?php the_post_thumbnail('professorPortrait'); ?>
                    </div>

                    <div class="two-third">
                        <?php
                            $liked_count = new WP_Query(array(
                               'post_type' => 'like',
                               'meta_query' => array(
                                    array(
                                        'key' => 'liked_professor_id',
                                        'compare' => '=',
                                        'value' => get_the_ID(),
                                    ),
                                ),
                            ));

                            $exists_like = 'no';

                            if(is_user_logged_in()) {
                                $exists_query = new WP_Query(array(
                                    'post_type' => 'like',
                                    'author' => get_current_user_id(),
                                    'meta_query' => array(
                                         array(
                                             'key' => 'liked_professor_id',
                                             'compare' => '=',
                                             'value' => get_the_ID(),
                                         ),
                                     ),
                                ));
    
                                if($exists_query -> found_posts) {
                                    $exists_like = 'yes';
                                }
                            }
                        ?>
                        <span class="like-box" data-like="<?php echo $exists_query -> posts[0] -> ID; ?>" data-professor="<?php the_ID(); ?>" data-exists="<?php echo $exists_like ?>">
                            <i class="fa fa-heart-o" aria-hidden="true"></i>
                            <i class="fa fa-heart" aria-hidden="true"></i>
                            <span class="like-count"><?php echo $liked_count -> found_posts; ?></span>
                        </span>
                        <?php the_content(); ?>
                    </div>
                </div>
                <?php the_content(); ?>
            </div>

            <?php
                $relatedPrograms = get_field("related_programs");
                if($relatedPrograms) { ?>
                    <hr class="section-break">
                    <h2 class="headline headline--medium headline--post-title">Teaches Program(s)</h2>
                    <ul class="link-list min-list">
                    <?php foreach($relatedPrograms as $program) { ?>
                        <li><a href="<?php echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>
                    <?php } ?>
                    </ul>
                    <?php
                }
            ?>
        </div>
    
    <?php }
    get_footer();
?>