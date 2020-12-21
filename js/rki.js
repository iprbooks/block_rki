var type = 'book';
$(document).ready(function () {
    // init
    send_request(type);

    $('.rki-form-control').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("rki-filter-apply").click();
        }
    });

    $('.rkibook-nav-link').click(function (e) {
        $(".rkibook-nav-link").removeClass("active");
        $(this).addClass("active");
        $(".rkibook-tab-pane").removeClass("active");
        $('#rkibook-' + $(this).data("type")).addClass("active");
    })

});

// tab
$(".rki-tab").click(function () {
    type = $(this).data("type");
    send_request(type);
});

// filter
$("#rki-filter-apply").click(function () {
    send_request(type);
});

// clear filter
$("#rki-filter-clear").click(function () {
    $(".rki-filter").val("");
    send_request(type);
});


function send_request(type, page = 0) {
    var filter = $(".rki-filter")
        .map(function () {
            return this.id + "=" + $(this).val();
        })
        .get()
        .join('&');

    $.ajax({
        url: M.cfg.wwwroot + "/blocks/rki/ajax.php?action=getlist&type=" + type + "&page=" + page + "&" + encodeURI(filter)
    }).done(function (data) {

        // hide read button
        $("#rki-item-detail-read").hide();

        // set data
        $("#rki-items-list").scrollTop(0);
        $("#rki-items-list").html(data.html);

        // set details click listener
        $(".rki-item").click(function () {
            set_details($(this).data("id"));
        });

        // pagination
        $(".rki-page").click(function () {
            send_request(type, $(this).data('page'));
        });

        // init detail view
        set_details($(".rki-item").data("id"));
    });
}

function set_details(id) {
    this.clear_details();
    $("#rki-item-detail-image").html($("#rki-item-image-" + id).html());
    $("#rki-item-detail-title").html($("#rki-item-title-" + id).html());
    $("#rki-item-detail-pubhouse").html($("#rki-item-pubhouse-" + id).html());
    $("#rki-item-detail-authors").html($("#rki-item-authors-" + id).html());
    $("#rki-item-detail-pubyear").html($("#rki-item-pubyear-" + id).html());
    $("#rki-item-detail-description").html($("#rki-item-description-" + id).html());
    $("#rki-item-detail-keywords").html($("#rki-item-keywords-" + id).html());
    $("#rki-item-detail-pubtype").html($("#rki-item-pubtype-" + id).html());

    var rb = $("#rki-item-detail-read");
    rb.attr("href", $("#rki-item-url-" + id).attr("href"));
    if ($("#rki-item-url-" + id).attr("href")) {
        rb.show();
    }
}

function clear_details() {
    $("#rki-item-detail-image").html('');
    $("#rki-item-detail-title").html('');
    $("#rki-item-detail-pubhouse").html('');
    $("#rki-item-detail-authors").html('');
    $("#rki-item-detail-pubyear").html('');
    $("#rki-item-detail-description").html('');
    $("#rki-item-detail-keywords").html('');
    $("#rki-item-detail-pubtype").html('');
}

