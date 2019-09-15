var Masonry = require('masonry-layout');
var imagesLoaded = require('imagesloaded');

/**
 * jQuery wrapper
 */
(function ($) {
    "use strict";

    $(document).ready(function () {
        if ($('.gallery').length) {
            initToggleMeta();
            initMasonry();
            initDeleteButtons();
            initSearch();
        }
    });

    var initSearch = function () {
        $('#gallery-search').keyup(function(e) {
            if (e.keyCode == 13) {
                search();
            }
        });

        $('#gallery-search-submit').click(function() {
            search();
        });
    }

    var search = function() {
        var terms = $('#gallery-search').val();

        if (0 === terms.trim().length) {
            window.location = '/images/index';
            return;
        }

        window.location = '/images/search/'+encodeURIComponent(terms);
    }

    var initToggleMeta = function () {
        $('.gallery-card').each(function (idx, card) {
            var $card = $(card);
            var $caption = $card.find('.gallery-image-caption');

            $caption.hide();
            $caption.removeClass('d-none');

            $card.find('.gallery-image').click(function () {
                $caption.slideToggle();
            });
        });
    }

    var initMasonry = function () {
        var masonry = new Masonry('.gallery', {
            itemSelector: '.gallery-card',
            gutter: 0,
            fitWidth: true,
            columnWidth: 300
        });

        var imgLoad = new imagesLoaded(document.querySelector('.gallery'));

        imgLoad.on('progress', function () {
            masonry.layout();
        });
    }

    var initDeleteButtons = function () {
        var $buttons = $('.image-delete-button');

        $buttons.click(function () {
            var $button = $(this);

            if (!confirm(trans.delete_image)) {
                return;
            }

            $button.hide();
            $button.after('<div class="spinner-border spinner-border-sm delete-loader" role="status">\n' +
                '  <span class="sr-only">Loading...</span>\n' +
                '</div>');

            $.ajax({
                url: '/images/ajax-delete/' + $button.data('imageId'),
                type: 'DELETE',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', x_csrf_token);
                }
            }).done(function (data, status) {
                var resp = $.parseJSON(data);
                if (status === 'success' && resp.success) {
                    var $card = $button.closest('.gallery-card');
                    $card.remove();
                } else {
                    $button.parent().find('.delete-loader').remove();
                    $button.show();
                    alert(resp.message);
                }
            });

        });
    }

})(jQuery);
