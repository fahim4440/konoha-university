<?php
    get_header(); 

    page_banner(array(
        'title' => 'All Programs',
        'subtitle' => 'You can look different programs of our university',
    ));
    
?>

    <ul class="link-list min-list">

        <div class="container container--narrow page-section">
            <?php
                while(have_posts()) {
                    the_post(); ?>
                
                    <li>
                        <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php if(has_excerpt()) {
                                the_excerpt();
                            } else {
                                echo wp_trim_words(get_the_content(), 18);
                            } 
                        ?>
                    </li>

                <?php }  
            ?>
        </div>
    
    </ul>
    
    <?php
    get_footer();
?>