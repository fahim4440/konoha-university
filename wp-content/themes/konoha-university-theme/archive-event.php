<?php
    get_header(); 

    page_banner(array(
        'title' => 'Upcoming Events',
        'subtitle' => 'See What is going on in our university',
    ));
    
?>

    <div class="container container--narrow page-section">
        <?php
            while(have_posts()) {
                the_post();
                get_template_part( 'template-parts/content-event');
            }
            echo paginate_links();
        ?>
        <hr class="section-breal">
        <p>Are you looking for Past Events? Checkout our <a href="<?php echo site_url("/past-events") ?>">Past Events Archive</a>.</p>
    </div>
    
    <?php
    get_footer();
?>