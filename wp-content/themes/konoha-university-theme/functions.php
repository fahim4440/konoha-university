<?php

    require get_theme_file_path( 'inc/search-route.php' );
    require get_theme_file_path( 'inc/like-route.php' );

    function university_files() {
        wp_enqueue_script( "main-university-js", get_theme_file_uri("/build/index.js"), array('jquery'), '1.0', true );
        wp_enqueue_style( "custom-google-fonts", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");
        wp_enqueue_style( "font-awesome", "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
        wp_enqueue_style( "university_main_style", get_theme_file_uri("/build/style-index.css") );
        wp_enqueue_style( "university_extra_style", get_theme_file_uri("/build/index.css") );
        wp_localize_script( "main-university-js", "universityData", array(
            'root_url' => get_site_url(),
            'nonce' => wp_create_nonce('wp_rest'),
        ) );
    }

    function university_features() {
        add_theme_support("title-tag");
        add_theme_support("post-thumbnails");
        add_image_size("professorLandscape", 400, 260, true);
        add_image_size("professorPortrait", 480, 650, true );
        add_image_size("pageBanner", 1500, 350, true);
    }

    function adjusting_queries($query) {

        if(!is_admin() AND is_post_type_archive('program') AND $query -> is_main_query()) {
            $query -> set("orderby", "title");
            $query -> set("order", "ASC");
            $query -> set("posts_per_page", "-1");
        }

        $today = date("Ymd");
        if(!is_admin() AND is_post_type_archive('event') AND $query -> is_main_query()) {
            $query -> set("meta_key", "event_date");
            $query -> set("orderby", "meta_value_num");
            $query -> set("order", "ASC");
            $query -> set("meta_query", array(
                array(
                    "key" => 'event_date',
                    "compare" => '>=',
                    "value" => $today,
                    "type" => 'numeric'
                ),
            ));
        }
    }

    function customizing_rest_api() {
        register_rest_field( "post", "author_name", array(
            'get_callback' => function() {
                return get_the_author();
            }
        ));
    }

    function page_banner($args = NULL) {
        
        if(!$args['title']) {
            $args['title'] = get_the_title();
        }

        if(!$args['subtitle']) {
            $args['subtitle'] = get_field('page_banner_subtitle');
        }

        if(!$args['photo']) {
            $photo = get_field('page_banner_image')['sizes']['pageBanner'];
            if($photo AND !is_archive() AND !is_home()) {
                $args['photo'] = $photo;
            } else {
                $args['photo'] = get_theme_file_uri('images/konoha_university_banner.jpg');
            }
        }

        ?>
        <div class="page-banner">
            
            <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo'] ?>)"></div>
            
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
            
                <div class="page-banner__intro">
                    <p><?php echo $args['subtitle']; ?></p>
                </div>
            
            </div>
    
        </div>
    <?php }

    function redirecting_to_front_end() {
        $current_user = wp_get_current_user();

        if(count($current_user -> roles) == 1 AND $current_user -> roles[0] == 'subscriber') {
            wp_redirect(site_url('/'));
            exit;
        }
    }

    function no_admi_bar() {
        $current_user = wp_get_current_user();

        if(count($current_user -> roles) == 1 AND $current_user -> roles[0] == 'subscriber') {
            show_admin_bar( false );
        }
    }

    function header_url() {
        return esc_url(site_url('/'));
    }

    function login_CSS() {
        wp_enqueue_style( "custom-google-fonts", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");
        wp_enqueue_style( "font-awesome", "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
        wp_enqueue_style( "university_main_style", get_theme_file_uri("/build/style-index.css") );
        wp_enqueue_style( "university_extra_style", get_theme_file_uri("/build/index.css") );
    }

    function header_title() {
        return get_bloginfo("name");
    }

    function make_note_private($data) {
        if($data['post_type'] == 'note') {
            $data['post_content'] = sanitize_textarea_field($data['post_content']);
            $data['post_title'] = sanitize_textarea_field($data['post_title']);
        }
        if($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
            $data['post_status'] =  'private';
        }
        return $data;
    }

    function ignore_certain_files($exclude_filters) {
        $exclude_filters[] = 'themes/konoha-university-theme/node_modules';
        return $exclude_filters;
    }

    add_action( "wp_enqueue_scripts", "university_files");
    add_action( "after_setup_theme", "university_features");
    add_action( "pre_get_posts", "adjusting_queries");
    add_action( "rest_api_init", "customizing_rest_api" );
    add_action( "admin_init", "redirecting_to_front_end" );
    add_action( "wp_loaded", "no_admi_bar" );
    add_action( "login_enqueue_scripts", "login_CSS" );
    add_filter( "login_headerurl", "header_url" );
    add_filter( "login_headertitle", "header_title" );
    add_filter( "wp_insert_post_data", "make_note_private" );
    add_filter( "ai1wm_exclude_content_from_export", "ignore_certain_files");
?>