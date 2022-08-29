import $ from 'jquery'

class Like {
    constructor() {
        this.events();
    }

    events() {
        $(".like-box").on("click", this.click_button_dispatcher.bind(this));
    }

    click_button_dispatcher(e) {
        var current_like_box = $(e.target).closest(".like-box");

        if(current_like_box.attr('data-exists') == 'yes') {
            this.delete_like(current_like_box);
        } else {
            this.create_like(current_like_box);
        }
    }

    create_like(current_like_box) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            type: 'POST',
            data: {
                'professor_id' : current_like_box.data('professor'),
            },
            success: (response) => {
                current_like_box.attr('data-exists', 'yes');
                var count = parseInt(current_like_box.find('.like-count').html(), 10);
                count++;
                current_like_box.find('.like-count').html(count);
                current_like_box.attr('data-like', response);
            },
            error: (response) => {
                console.log(response);
            }
        });
    }

    delete_like(current_like_box) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            data: {
                'like': current_like_box.attr('data-like'),
            },
            type: 'DELETE',
            success: (response) => {
                current_like_box.attr('data-exists', 'no');
                var count = parseInt(current_like_box.find('.like-count').html(), 10);
                count--;
                current_like_box.find('.like-count').html(count);
                current_like_box.attr('data-like', '');
            },
            error: (response) => {
                console.log(response);
            }
        });
    }
}

export default Like