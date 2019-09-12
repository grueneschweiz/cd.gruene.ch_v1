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
        }
    });

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

})(jQuery);
