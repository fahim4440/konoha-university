<?php
    get_header();
    while(have_posts()) {
        the_post();

        page_banner();
        
    ?>

        <div class="container container--narrow page-section">

            <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Programs </a> 
                    <span class="metabox__main"><?php the_title(); ?></span>
                </p>
            </div>  
            
            <div class="generic-content">
                <?php the_field('main_body_content') ?>
            </div>

            <hr class="section-break">

            <?php
                $relatedProfessors = new WP_Query(array(
                    "posts_per_page" => -1,
                    "post_type" => 'professor',
                    "orderby" => 'title',
                    "order" => 'ASC',
                    "meta_query" => array(
                        array(
                            "key" => 'related_programs',
                            "compare" => 'LIKE',
                            "value" => '"' . get_the_ID() . '"'
                        ),
                    ),
                ));

                if($relatedProfessors -> have_posts()) { ?>
                    <h2 class="headline headline--medium headline--post-title">Professor(s) teaches <?php the_title(); ?></h2>
                    <ul class="professor-cards">
                    <?php
                    while($relatedProfessors -> have_posts()) {
                        $relatedProfessors -> the_post(); ?>
                        <li class="professor-card__list-item">
                            <a class="professor-card" href="<?php the_permalink(); ?>">
                                <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape') ?>">
                                <span class="professor-card__name"><?php the_title(); ?></span>
                            </a>
                        </li>
                    <?php }
                    ?>
                    </ul>
                    <?php
                }
                wp_reset_postdata();
            ?>

            <hr class="section-break">

            <?php
                $today = date("Ymd");
                $relatedEventPosts = new WP_Query(array(
                    "posts_per_page" => 2,
                    "post_type" => 'event',
                    "meta_key" => 'event_date',
                    "orderby" => 'meta_value_num',
                    "order" => 'ASC',
                    "meta_query" => array(
                        array(
                            "key" => 'event_date',
                            "compare" => '>=',
                            "value" => $today,
                            "type" => 'numeric'
                        ),
                        array(
                            "key" => 'related_programs',
                            "compare" => 'LIKE',
                            "value" => '"' . get_the_ID() . '"'
                        ),
                    ),
                ));

                if($relatedEventPosts -> have_posts()) { ?>
                    <h2 class="headline headline--medium headline--post-title">Upcoming <?php the_title(); ?>  Event(s)</h2>
                    <?php
                    while($relatedEventPosts -> have_posts()) {
                        $relatedEventPosts -> the_post();
                        get_template_part( 'template-parts/content-event');
                    }
                }
            ?>

        </div>
    
    <?php }
    get_footer();
?>