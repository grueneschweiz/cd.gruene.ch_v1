/**
 * The Logo class
 *
 * @param {$.fn.cibuilder} $cibuilder
 * @param {jQuery} $wrapper
 */
function LogoModule($cibuilder, $wrapper) {
    "use strict";
    /**
     * the logo data
     *
     * @type stdObject data must contain dirname, filename,
     * extension and subline property
     */
    var data;

    /**
     * the logo rotator object
     *
     * @type jQuery
     */
    var $rotator;

    /**
     * lets us control the logos size
     *
     * @type float
     */
    var scale_factor;

    /**
     * lets us control the logos margin
     *
     * @type float
     */
    var margin_factor;

    /**
     * controls the width factor of the logo compared with the verts logo
     *
     * @type float
     */
    this.width_factor = {
        gruene: 0.765,
        verda: 0.748,
        verdi: 0.79
    };

    /**
     * set to true to prevent logo collision alert
     *
     * @type {boolean}
     */
    this.collision_alert_lock = false;

    /**
     * set to true to prevent logo collision confirmation alert
     *
     * @type {boolean}
     */
    this.collision_confirm_lock = false;

    /**
     * load the containers on invocation
     *
     * @param {$.fn.cibuilder} $cibuilder
     * @param {jQuery} $wrapper
     */
    this._construct = function ($cibuilder, $wrapper) {
        this.$cibuilder = $cibuilder;

        // set scale factor
        this.scale_factor = 0.33;

        // set margin factor
        this.margin_factor = 0.12;

        // set wrapper object
        this.$wrapper = $wrapper;
    };

    /**
     * Set new logo
     */
    this.set = function () {
        var self = this,
            img,
            sub,
            color_scheme = $('#color_scheme').val(),
            color = color_scheme === 'white' ? 'white' : 'green',
            path = this.data.dirname + this.data.filename + '-' + color + '.' + this.data.extension;

        // if a logo was given
        if (this.data !== 'missing_id') {
            img = '<object id="logo-top" data="' + path + '" type="image/svg+xml"></object>';
            sub = this.data.subline ? '<div id="logo-subline"><span>' + this.data.subline + '</span></div>' : '';
        } else {
            img = '';
            sub = '';
        }

        // if a new logo was loaded
        if ($('#logo-top').attr('data') !== path) {
            this.data.scaled = false;
        }

        // add logo if needed
        if ($('#logo-top').attr('data') !== path || $('#logo-subline span').text() !== this.data.subline) {
            this.$wrapper.html('<div id="logo-rotator">' + img + sub + '</div>');

            // set rotator
            this.$rotator = $('#logo-rotator');
        } else {
            $('#logo-top').trigger('load');
        }

        // when logo is fully loaded
        $('#logo-top').on('load', function () {
            // set size
            self.setSize();
            // set position
            self.setPosition();
        });
    };

    /**
     * Move logo to the best position
     */
    this.setPosition = function () {
        var left,
            layout = $('#layout').val(),
            $img = $('.cropit-preview-image-container'),
            img_width = parseFloat($img.outerWidth()),
            $bars = this.$cibuilder.image.$container;

        // move logo to the opposite of the bars horizontal position
        if (layout === 'left') {
            left = img_width - this.$wrapper.width();
            this.$wrapper.css('left', left);
        } else {
            this.$wrapper.css('left', 0);
        }

        // make sure the logo is always in the image. lazy version ;)
        this.toggleVerticalPosition();
        this.toggleVerticalPosition();

        // move the logo to the opposite of the bars vertical position
        if (this.touches($bars)) {
            // move logo to opposite position
            this.toggleVerticalPosition();

            // if its still touching
            if (this.touches($bars)) {
                if (!this.collision_alert_lock) {
                    alert(trans.logo_keeps_colliding);
                    this.collision_alert_lock = true;
                }
                this.toggleVerticalPosition();
            }
        }
    };

    /**
     * If logo sticks to top, it will be moved down and vice versa.
     */
    this.toggleVerticalPosition = function () {
        var top,
            $img = $('.cropit-preview-image-container'),
            img_height = parseFloat($img.outerHeight());

        // if logo sticks to the top
        if (parseInt(this.$wrapper.css('top')) === 0) {
            // put it down
            this.$rotator.css('transform', 'none');
            top = img_height - $('#logo-wrapper').height();
            $('#logo-wrapper').css('top', top + 'px');
            this.$rotator.css('transform', 'rotate(-5deg)');
        } else {
            // else move it up
            this.$wrapper.css('top', 0);
        }
    };

    /**
     * checks if the given object touches the logo
     *
     * @param {jQuery} obj
     * @returns {Boolean}
     */
    this.touches = function (obj) {
        var $div1 = this.$wrapper,
            $div2 = $(obj),
            x1 = $div1.offset().left,
            y1 = $div1.offset().top,
            h1 = $div1.outerHeight(true),
            w1 = $div1.outerWidth(true),
            b1 = y1 + h1,
            r1 = x1 + w1,
            x2 = $div2.offset().left,
            y2 = $div2.offset().top,
            h2 = $div2.outerHeight(true),
            w2 = $div2.outerWidth(true),
            b2 = y2 + h2,
            r2 = x2 + w2;

        return !(b1 < y2 || y1 > b2 || r1 < x2 || x1 > r2);
    };

    /**
     * sets the size of the logo
     *
     * @uses this.scale_factor
     * @uses this.margin_factor
     *
     * @returns {$.fn.$cibuilder}
     */
    this.setSize = function () {
        this.$rotator.css('transform', 'none');

        var small_side,
            target_width,
            factor,
            margin,
            border_width,
            width_factor,
            $svg = $('#logo-top'),
            svg_width = parseFloat($svg.outerWidth()),
            $font = $('#logo-subline'),
            font_size = parseFloat($font.css('font-size'));

        // set size if logo hasn't loaded yet
        if (0 === svg_width) {
            svg_width = 500;
        }

        // scale logo according to the width of the logo in the verts version
        if (!this.data.scaled) {
            width_factor = this.width_factor[this.data.filename] || 1;
            svg_width = svg_width * width_factor;
            this.data.scaled = true;
        }

        target_width = this.calculateTargetWidth();
        factor = target_width / svg_width;

        $svg.width(target_width);
        $font.css('font-size', (font_size * factor) + 'px');

        border_width = parseInt($('.border-container.border-top').height());
        margin = target_width * this.margin_factor + border_width;

        this.$rotator
            .css('transform', 'rotate(-5deg)')
            .css('margin', margin + 'px');

        return this;
    };

    /**
     * calulates the size of the logo
     *
     * for portrait images, the width of the image is the measure,
     * for landscape images the surface is authorative. this was
     * decided by the responsible person, since it increases the logo
     * on landscape images, but doesn't break the rules for landscape.
     *
     * @returns {number}
     */
    this.calculateTargetWidth = function () {
        var $img = $('.cropit-preview-image-container'),
            img_width = parseFloat($img.outerWidth()),
            img_height = parseFloat($img.outerHeight()),
            measure;

        if (img_width < img_height) {
            // for portrait images the smaller side is authorative
            measure = img_width;
        } else {
            // for landscape images the surface is autorative
            measure = Math.sqrt(img_height * img_width);
        }

        return this.scale_factor * measure;
    }

    /**
     * invoke the constructor
     */
    this._construct($cibuilder, $wrapper);
}

module.exports = LogoModule;
