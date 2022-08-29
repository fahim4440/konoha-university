import $ from 'jquery'

class MyNotes {
    constructor() {
        this.events();
    }

    events() {
        $("#my-notes").on("click", ".delete-note", this.delete_note);
        $("#my-notes").on("click", ".edit-note", this.edit_note.bind(this));
        $("#my-notes").on("click", ".update-note", this.update_note.bind(this));
        $(".submit-note").on("click", this.create_note.bind(this));
    }

    delete_note(e) {
        var this_note = $(e.target).parents("li");
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/wp/v2/note/' + this_note.data('id'),
            type: 'DELETE',
            success: (response) => {
                this_note.slideUp();
            },
            error: (response) => {
                alert(JSON.stringify(response.responseJSON.message));
            }
        });
    }

    edit_note(e) {
        var this_note = $(e.target).parents("li");

        if(this_note.data("state") == 'editable') {
            this.makeReadonly(this_note);
        } else {
            this.makeEditable(this_note);
        }
        
    }

    makeReadonly(this_note) {
        this_note.find(".note-title-field, .note-body-field").attr("readonly", "readonly").removeClass("note-active-field");
        this_note.find(".update-note").removeClass("update-note--visible");
        this_note.find(".edit-note").html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit');
        this_note.data("state", "readonly");
    }

    makeEditable(this_note) {
        this_note.find(".note-title-field, .note-body-field").removeAttr("readonly").addClass("note-active-field");
        this_note.find(".update-note").addClass("update-note--visible");
        this_note.find(".edit-note").html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');
        this_note.data("state", "editable");
    }

    update_note(e) {
        var this_note = $(e.target).parents("li");
        var update_note = {
            'title': this_note.find(".note-title-field").val(),
            'content': this_note.find(".note-body-field").val(),
        }
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/wp/v2/note/' + this_note.data('id'),
            type: 'POST',
            data: update_note,
            success: (response) => {
                this.makeReadonly(this_note);
            },
            error: (response) => {
                alert(JSON.stringify(response.responseJSON.message));
            }
        });
    }

    create_note(e) {
        var new_note = {
            'title': $(".new-note-title").val(),
            'content': $(".new-note-body").val(),
            'status': 'publish',
        }
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/wp/v2/note/',
            type: 'POST',
            data: new_note,
            success: (response) => {
                $(".new-note-title, .new-note-body").val('');
                $(`
                    <li data-id="${response.id}">
                        <input readonly value="${response.title.raw}" class="note-title-field"></input>
                        <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                        <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                        <textarea readonly class="note-body-field">${response.content.raw}</textarea>
                        <span class="update-note btn btn--blue btn--small"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Save</span>
                    </li>
                `).prependTo("#my-notes").hide().slideDown();
            },
            error: (response) => {
                alert(JSON.stringify(response.responseJSON.message));
            }
        });
    }
}

export default MyNotes;