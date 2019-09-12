/**
 * The Copyright class
 *
 * @param {$.fn.cibuilder} $cibuilder
 * @param {jQuery} $wrapper object
 */
function CopyrightModule($cibuilder, $wrapper) {
    "use strict";

    /**
     * load the containers on invokation
     *
     * @param {$.fn.cibuilder} $cibuilder
     * @param {jQuery} wrapper object
     */
    this._construct = function ($cibuilder, $wrapper) {
        this.$cibuilder = $cibuilder;
        this.$wrapper = $wrapper;
        this.sizeFactor = 0.0225;
        this.borderMarginFactor = 2.25; // times border width
        this.noBorderMargin = 1; // em
    };

    /**
     * set the copyright
     *
     * @param string layout
     * @param string border
     */
    this.set = function (text, layout, border, height, width) {
        if (this.text !== text) {
            this.text = text;
            this.setText();
        }

        if (this.imgHeight !== height || this.imgWidth !== width) {
            this.imgHeight = height;
            this.imgWidth = width;
            this.setFontSize();
        }

        // yes, we do have to call this in any case
        this.layout = layout;
        this.border = border;
        this.setPosition();
    };

    this.setPosition = function () {
        if ('left' === this.layout) {
            this.$wrapper.removeClass('is-left').addClass('is-right');
            this.$wrapper.css('left', (this.imgWidth - this.$wrapper.outerHeight(true)) + 'px');
        } else {
            this.$wrapper.addClass('is-left').removeClass('is-right');
            this.$wrapper.css('left', 0);
        }

        if ('auto' === this.border) {
            this.$wrapper.removeClass('no-border').addClass('has-border');
            var border = this.$cibuilder.border.getBorderWidth();
            this.$wrapper.css('margin', (border * this.borderMarginFactor) + 'px');
        } else {
            this.$wrapper.addClass('no-border').removeClass('has-border');
            this.$wrapper.css('margin', this.noBorderMargin + 'em');
        }
    }

    this.setFontSize = function () {
        var fontSize = Math.sqrt(this.imgHeight * this.imgWidth) * this.sizeFactor;
        this.$wrapper.css('font-size', fontSize + 'px');
    }

    this.setText = function () {
        if (this.text.length) {
            this.$wrapper.text(trans.copy + ' ' + this.text);
        } else {
            this.$wrapper.text('');
        }
    }

    /**
     * invoke the constructor
     */
    this._construct($cibuilder, $wrapper);
}

module.exports = CopyrightModule;
