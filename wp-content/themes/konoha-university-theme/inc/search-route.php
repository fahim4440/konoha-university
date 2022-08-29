<?php
    function university_register_search() {
        register_rest_route('university/v1', 'search', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'university_search_results',
        ));
    }

    function university_search_results($data) {
        $main_query = new WP_Query(array(
            'post_type' => array('post', 'page', 'professor', 'event', 'program'),
            's' => sanitize_text_field($data['term']),
        ));

        $results = array(
            'general_info' => array(),
            'professors' => array(),
            'events' => array(),
            'programs' => array(),
        );

        while($main_query -> have_posts()) {
            $main_query -> the_post();
            if(get_post_type() == 'post' OR get_post_type() == 'page') {
                array_push($results['general_info'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'post_type' => get_post_type(),
                    'author_name' => get_author_name(),
                ));
            } if(get_post_type() == 'professor') {
                array_push($results['professors'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url(0, 'professorLandscape'),
                ));
            } if(get_post_type() == 'event') {
                $eventDate = new DateTime(get_field('event_date'));
                $description = NULL;
                if(has_excerpt()) {
                    $description = get_the_excerpt();
                } else {
                    $description = wp_trim_words(get_the_content(), 18);
                }
                array_push($results['events'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'day' => $eventDate -> format('d'),
                    'month' => $eventDate -> format('M'),
                    'description' => $description,
                ));
            } if(get_post_type() == 'program') {
                array_push($results['programs'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'id' => get_the_ID(),
                ));
            }
        }

        

        if($results['programs']) {
            $program_meta_query = array('relation' => 'OR');
            foreach ($results['programs'] as $item) {
                array_push($program_meta_query, array(
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . $item['id'] . '"'
                ));
            }
            $program_relationship_query = new WP_Query(array(
                'post_type' => array('professor', 'event'),
                'meta_query' => $program_meta_query,
            ));
    
            while($program_relationship_query -> have_posts()) {
                $program_relationship_query -> the_post();
                if(get_post_type() == 'professor') {
                    array_push($results['professors'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'image' => get_the_post_thumbnail_url(0, 'professorLandscape'),
                    ));
                }

                if(get_post_type() == 'event') {
                    $eventDate = new DateTime(get_field('event_date'));
                    $description = NULL;
                    if(has_excerpt()) {
                        $description = get_the_excerpt();
                    } else {
                        $description = wp_trim_words(get_the_content(), 18);
                    }
                    array_push($results['events'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'day' => $eventDate -> format('d'),
                        'month' => $eventDate -> format('M'),
                        'description' => $description,
                    ));
                }
            }
        }

        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
        return $results;
    }
    add_action('rest_api_init', 'university_register_search');
?>