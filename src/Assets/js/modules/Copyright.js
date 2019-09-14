/**
 * The Copyright class
 *
 * @param {$.fn.cibuilder} $cibuilder
 * @param {jQuery} $outer wrapper object
 * @param {jQuery} $inner wrapper object
 */
function CopyrightModule($cibuilder, $outer, $inner) {
    "use strict";

    /**
     * load the containers on invokation
     *
     * @param {$.fn.cibuilder} $cibuilder
     * @param {jQuery} outer wrapper object
     * @param {jQuery} inner wrapper object
     */
    this._construct = function ($cibuilder, $outer, $inner) {
        this.$cibuilder = $cibuilder;
        this.$outer = $outer;
        this.$inner = $inner;
        this.sizeFactor = 0.01;
        this.borderSideMarginFactor = 0.2; // times border width
        this.borderBottomMarginFactor = 3; // times border width
        this.noBorderMargin = 1; // em
        this.text = '';
    };

    /**
     * set the copyright
     *
     * @param string layout
     * @param string border
     */
    this.set = function (text, layout, border, height, width) {
        var was_empty = this.text === '' && text !== '';

        if (this.text !== text) {
            this.text = text;
            this.setText();
        }

        if (this.imgHeight !== height || this.imgWidth !== width) {
            this.imgHeight = height;
            this.imgWidth = width;
            this.setFontSize();
            this.setPosition();
        }

        if (this.layout !== layout || this.border !== border) {
            this.layout = layout;
            this.border = border;
            this.setPosition();
        }

        if (was_empty) {
            this.setPosition();
        }
    };

    this.setPosition = function () {
        if ('auto' === this.border) {
            this.$outer.removeClass('no-border').addClass('has-border');
            var border = this.$cibuilder.border.getBorderWidth();
            var side_margin = border * this.borderSideMarginFactor;
            var bottom_margin = border * this.borderBottomMarginFactor;
        } else {
            this.$outer.addClass('no-border').removeClass('has-border');
            var font_size = parseFloat(this.$inner.css('font-size'));
            var side_margin = this.noBorderMargin * font_size;
            var bottom_margin = this.noBorderMargin * font_size + this.$inner.outerHeight(true);
        }

        this.$outer.css('bottom', bottom_margin + 'px');

        if ('left' === this.layout) {
            this.$outer.removeClass('is-left').addClass('is-right');
            this.$outer.css('left', (this.imgWidth - this.$inner.outerHeight(true) - side_margin) + 'px');
        } else {
            this.$outer.addClass('is-left').removeClass('is-right');
            this.$outer.css('left', side_margin + 'px');
        }
    }

    this.setFontSize = function () {
        var fontSize = Math.sqrt(this.imgHeight * this.imgWidth) * this.sizeFactor;
        this.$inner.css('font-size', fontSize + 'px');
    }

    this.setText = function () {
        if (this.text.length) {
            this.$inner.text(trans.copy + ' ' + this.text);
        } else {
            this.$inner.text('');
        }
    }

    /**
     * invoke the constructor
     */
    this._construct($cibuilder, $outer, $inner);
}

module.exports = CopyrightModule;
