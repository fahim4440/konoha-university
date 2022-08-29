<?php
    add_action('rest_api_init','university_like_routes');

    function university_like_routes() {
        register_rest_route('university/v1','manageLike',array(
            'methods' => 'POST',
            'callback' => 'create_like',
        ));

        register_rest_route('university/v1','manageLike',array(
            'methods' => 'DELETE',
            'callback' => 'delete_like',
        ));
    }

    function create_like($data) {
        $professor_id = sanitize_text_field($data['professor_id']);
        if(is_user_logged_in()) {
            $exists_query = new WP_Query(array(
                'post_type' => 'like',
                'author' => get_current_user_id(),
                'meta_query' => array(
                     array(
                         'key' => 'liked_professor_id',
                         'compare' => '=',
                         'value' => $professor_id,
                     ),
                 ),
            ));
            if($exists_query -> found_posts == 0 AND get_post_type($professor_id) == 'professor') {
                return wp_insert_post(array(
                    'post_type' => 'like',
                    'post_status' => 'publish',
                    'meta_input' => array(
                        'liked_professor_id' => $professor_id,
                    ),
                ));
            } else {
                die("You already liked the professor");
            }
        } else {
            die("You need to signin");
        }
    }

    function delete_like($data) {
        $like_id = sanitize_text_field($data['like']);
        if(get_current_user_id() == get_post_field('post_author', $like_id) AND get_post_type($like_id) == 'like') {
            wp_delete_post($like_id, true);
            return 'Congrats';
        } else {
            die('You do not have permission to do that');
        }
    }
?>