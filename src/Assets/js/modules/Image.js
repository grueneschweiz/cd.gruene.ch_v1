/**
 * The image class
 *
 * @param {$.fn.cibuilder} $cibuilder
 * @param {jQuery} $bars the image bars
 */
function ImageModule($cibuilder, $bars) {

    /**
     * the image container object
     */
    var $container;

    /**
     * load the containers on invokation
     *
     * @param {$.fn.$cibuilder} $cibuilder
     * @param {jQuery} $bars the image bars
     */
    this._construct = function ($cibuilder, $bars) {
        this.$cibuilder = $cibuilder;
        this.$container = $bars;
    };

    /**
     * adds a bar form element to $bars
     *
     * @param {object} options
     * @param {int} num position number of the bar
     */
    this.addBar = function (options, num) {

        var text = options.text,
            type = options.type,
            barHtml = '<div class="barwrapper"><div class="bar bar-' + num + ' ' + type + '">' + text + '</div><div style="clear:both;"></div>';

        // if its the first element
        if (num === 1) {
            this.$container.prepend(barHtml);
        } else {
            this.$container.find('.bar-' + (num - 1)).parent('.barwrapper').after(barHtml);
        }
    };

    /**
     * reorders the bars after deleting one or before adding one
     *
     * @param {int} id
     * @param {string} mode accepts 'add|delete'
     */
    this.reorderBars = function (id, mode) {
        var $bars = this.$container.find('.bar'),
            i = 0;

        // first remove all class names
        this.removeBarIdClasses();

        // add new class names in proper order
        $bars.each(function () {
            i++;

            // skip the id we want to add
            if (mode === 'add' && i === id) {
                i++;
            }

            // add new class
            $(this).addClass('bar-' + i);
        });
    };

    /**
     * Remove all html classes with a bar id
     *
     * @returns {Boolean} false if there was nothing to do
     */
    this.removeBarIdClasses = function () {
        var class_names = this.getBarClassNamesString(),
            self = this;

        // abort if no objects where found
        if (class_names === false) {
            return false;
        }

        // loop over given class names
        $.each(class_names, function (index, class_name) {
            // get elements with class name and remove class
            self.$container.find('.' + class_name).removeClass(class_name);
        });
    };

    /**
     * Return array with the bars identifying class names like bar-1, bar-2 etc.
     *
     * @returns {Array}
     */
    this.getBarClassNamesString = function () {
        var class_names = [],
            tmp,
            $objs = this.$container.find('.bar');

        // loop over bars and add bar-x class to class_names
        $objs.each(function () {
            tmp = $(this).attr('class');
            class_names.push(tmp.match(/bar-\d+/)[0]);
        });

        return class_names;
    };

    /**
     * Remove the bar with the class bar-x where x=num
     *
     * @param {int} num
     */
    this.removeBar = function (num) {
        var $el = this.$container.find('.bar-' + num).parent('.barwrapper');
        $el.remove();
    };

    /**
     * return the number of bars in $fbars
     *
     * @returns int
     */
    this.countBars = function () {
        return this.$container.find('div.bar').length;
    };

    /**
     * Sets colors of bars
     *
     * @param {string} scheme accepts 'green', 'white', 'greengreen'
     */
    this.setScheme = function (scheme) {
        switch (scheme) {
            case 'green':
                this.$container
                    .find('.bar.headline.green, .bar.headline.white')
                    .removeClass('green white')
                    .addClass('green');
                this.$container
                    .find('.bar.subline.green, .bar.subline.white')
                    .removeClass('green white')
                    .addClass('white');
                break;

            case 'white':
                this.$container
                    .find('.bar.green, .bar.white')
                    .removeClass('green white')
                    .addClass('white');
                break;

            case 'greengreen':
                this.$container
                    .find('.bar.green, .bar.white')
                    .removeClass('green white')
                    .addClass('green');
                break;
        }
    };

    /**
     * sets the left or right parameter of the image-bars container
     */
    this.setBarsXposition = function () {
        var margin_factor = 1.25, // margin = margin_factor * border_width
            border_width = parseInt($('.border-container.border-left').width()),
            margin = margin_factor * border_width,
            angle = this.getRotationAngle(this.$container),
            bar_padding,
            left,
            right;

        // add correction for angle
        margin -= this.$container.height() * Math.sin(angle) * 0.5;

        // compute position values
        if (this.$container.hasClass('right')) {
            bar_padding = parseInt(this.$container.find('.headline:first').css('padding-right'));
            left = 'auto';
            right = (margin - bar_padding) + 'px';
        } else {
            bar_padding = parseInt(this.$container.find('.headline:first').css('padding-left'));
            left = (margin - bar_padding) + 'px';
            right = 'auto';
        }

        // set x pos
        this.$container
            .css('left', left)
            .css('right', right);
    };

    /**
     * set font size of the bars
     *
     * detects the max and the min value and uses the given value
     * as multiplicand in between the min and the max
     *
     * @param {float} scale
     */
    this.setFontSize = function (scale) {
        var max = this.detectMaxFontSize(),
            min = this.detectMinFontSize(),
            size = (max - min) * scale + min;

        if (min > max) {
            size = min;
            $('.warning-to-much-text').show();
            $('.font-size-controls').hide();
        } else {
            $('.warning-to-much-text').hide();
            $('.font-size-controls').show();
        }

        this.$container.css('font-size', size + 'px');
    };

    /**
     * Get the max font size of the bars
     *
     * @returns {Number}
     */
    this.detectMaxFontSize = function () {
        var max = 0.95,
            width_limit = this.getFontSizeWidthLimit(max),
            height_limit = this.getFontSizeHeightLimit();
        return width_limit < height_limit ? width_limit : height_limit;
    };

    /**
     * Get the minimal font size of the bars
     *
     * @returns {Number}
     */
    this.detectMinFontSize = function () {
        var min = 0.5,
            width_limit = this.getFontSizeWidthLimit(min),
            height_limit = this.getFontSizeHeightLimit(),
            geometrical_limit = width_limit < height_limit ? width_limit : height_limit,
            logo_limit = 0;

        if ($('#logo-subline').length) {
            // 2.5 is the zoom factor between the logos subline and the logo headline
            // 1.75 is the minimal factor between headlines and the logo, according to the ci-guide
            logo_limit = parseFloat($('#logo-subline').css('font-size')) * 2.25 * 1.75;
        }

        return geometrical_limit > logo_limit ? geometrical_limit : logo_limit;
    };

    /**
     * Detect font size limit by width with given factor
     *
     * @param {Number} factor
     * @returns {Number}
     */
    this.getFontSizeWidthLimit = function (factor) {
        var side = this.$container.hasClass('left') ? 'left' : 'right',
            angle = this.getRotationAngle(this.$container),
            angle_corr = (parseFloat(this.$container.height()) * Math.sin(-angle)) / 2,
            bar_width = parseFloat(this.$container.width()) + parseFloat(this.$container.css(side)) + angle_corr,
            image_width = parseFloat($('.cropit-preview-image-container').width()),
            font_size = parseFloat(this.$container.css('font-size')),
            width_limit = factor * image_width;

        return (width_limit / bar_width) * font_size;
    };

    /**
     * Detect font size limit by height
     *
     * @returns {Number}
     */
    this.getFontSizeHeightLimit = function () {
        var dragger_height = parseFloat($('#image-bars-dragger').height()),
            image_height = parseFloat($('.cropit-preview-image-container').height()),
            font_size = parseFloat(this.$container.css('font-size'));

        return font_size / (dragger_height / image_height);
    };

    /**
     * Set the height of the dragger container to fit its content
     */
    this.setDraggerHeight = function () {
        var height = this.$container.height(),
            angle = this.getRotationAngle(this.$container),
            $relevant_bar,
            visible_bar_width,
            visible_bar_height_correction,
            dragger_height;

        if (this.$container.hasClass('left')) {
            $relevant_bar = this.$container.find('.barwrapper:first-of-type .bar');
        } else {
            $relevant_bar = this.$container.find('.barwrapper:last-of-type .bar');
        }

        visible_bar_width = this.getVisibleBarWidth($relevant_bar);
        visible_bar_height_correction = visible_bar_width * Math.sin(-angle);
        dragger_height = height + visible_bar_height_correction + 2 * this.getImageBarsMargin();

        $('#image-bars-dragger').css('height', dragger_height);
    };

    /**
     * Returns the angle of an element in radians
     *
     * @param {jQuery} obj a jQuery object or a selector string
     * @returns {Number}
     */
    this.getRotationAngle = function (obj) {
        var matrix = $(obj).css('transform');

        var values = matrix.split('(')[1];
        values = values.split(')')[0];
        values = values.split(',');

        var a = values[0],
            b = values[1];

        return Math.atan2(b, a);
    };

    /**
     * Sets the bars containers top position to fit the dragger
     */
    this.setBarsTopPosition = function () {
        var margin = this.getImageBarsMargin(),
            bars_width = this.$container.width(),
            angle = this.getRotationAngle(this.$container),
            triangle_correction,
            overflow,
            height,
            rotation_corr,
            top;

        if (this.$container.hasClass('right')) {
            height = this.$container.height();
            rotation_corr = (height / 2) * Math.tan(-angle);
            overflow = (-parseFloat(this.$container.css('right'))) - rotation_corr;
            triangle_correction = ((bars_width / 2) - overflow) * Math.sin(-angle);
        } else {
            triangle_correction = (bars_width / 2) * Math.sin(-angle);
        }

        top = triangle_correction + margin;

        this.$container.css('top', top);
    };

    /**
     * Calculate and return the vertical margin between the border and the bars container
     *
     * @returns {number}
     */
    this.getImageBarsMargin = function () {
        var $border = $('.border-container.border-top'),
            border_height = $border.height(),
            border_margin_factor;

        if ('none' === $border.css('display')) {
            border_margin_factor = 1;
        } else {
            border_margin_factor = 2;
        }

        return border_margin_factor * border_height;
    };

    /**
     * Calculate and return the visible width of the given bar.
     * CAUTION: This function only works for the first and the last bar.
     *
     * @param {$} $bar
     *
     * @return {number}
     */
    this.getVisibleBarWidth = function ($bar) {
        var angle = this.getRotationAngle(this.$container),
            height = this.$container.height(),
            rotation_corr = (height / 2) * Math.tan(-angle),
            width = $bar.outerWidth(true),
            side = this.$container.hasClass('left') ? 'left' : 'right',
            offset = -parseFloat(this.$container.css(side));

        return width - offset - rotation_corr;
    };

    /**
     * Moves the dragger back on the image, if he dropped out
     */
    this.moveDraggerIntoBox = function () {
        var $dragger = $('#image-bars-dragger'),
            $image = $('.cropit-preview-image-container'),
            image_top_boundary = $image.offset().top,
            image_bottom_boundary = image_top_boundary + $image.height(),
            dragger_height = $dragger.outerHeight(),
            dragger_top_pos = $dragger.offset().top,
            dragger_css_top_pos = parseFloat($dragger.css('top'));

        if ((dragger_top_pos + dragger_height) > image_bottom_boundary) {
            $dragger.css('top', image_bottom_boundary - dragger_top_pos - dragger_height + dragger_css_top_pos);
        }

        if (dragger_top_pos < image_top_boundary) {
            $dragger.css('top', 0);
        }
    };

    /**
     * invoke the constructor
     */
    this._construct($cibuilder, $bars);
}

module.exports = ImageModule;