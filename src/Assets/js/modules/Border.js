/**
 * The Border class
 *
 * @param {$.fn.cibuilder} $cibuilder
 * @param {jQuery} $wrapper object
 */
function BorderModule($cibuilder, $wrapper) {
    "use strict";
    /**
     * load the containers on invokation
     *
     * @param {$.fn.cibuilder} $cibuilder
     * @param {jQuery} wrapper object
     */
    this._construct = function ($cibuilder, $wrapper) {
        this.$cibuilder = $cibuilder;

        // set scale factor
        this.scale_factor = 0.02;

        // set wrapper object
        this.$wrapper = $wrapper;

        // inject containers
        this.injectContainers();

        // rerender wrapper
        this.$wrapper = $wrapper;

        // select containers
        this.$containers = {
            top: this.$wrapper.children('.border-top'),
            right: this.$wrapper.children('.border-right'),
            bottom: this.$wrapper.children('.border-bottom'),
            left: this.$wrapper.children('.border-left'),
            nw: this.$wrapper.children('.border-corner-nw'),
            ne: this.$wrapper.children('.border-corner-ne'),
            se: this.$wrapper.children('.border-corner-se'),
            sw: this.$wrapper.children('.border-corner-sw')
        };
    };

    /**
     * inserts the border elements into the wrapper
     */
    this.injectContainers = function () {
        var containers = '<div class="border-container border-top"></div>' +
            '<div class="border-container border-right"></div>' +
            '<div class="border-container border-bottom"></div>' +
            '<div class="border-container border-left"></div>' +
            '<div class="border-container border-corner border-corner-nw"></div>' +
            '<div class="border-container border-corner border-corner-ne"></div>' +
            '<div class="border-container border-corner border-corner-se"></div>' +
            '<div class="border-container border-corner border-corner-sw"></div>';
        this.$wrapper.html(containers);
    };

    /**
     * sets the image containers properties and displays/hides it
     *
     * @param string type accepts 'auto', 'none'
     */
    this.set = function (type) {
        if ('auto' === type) {
            this.getImgDims();
            this.setContainersProperties();
            this.$wrapper.show();
        } else {
            this.$wrapper.hide();
        }
    };

    /**
     * populates the img_dims variable with { width, height } of the image preview container
     */
    this.getImgDims = function () {
        var $img = $('.cropit-preview-image-container');
        this.img_dims = {
            width: parseInt($img.outerWidth()),
            height: parseInt($img.outerHeight())
        };
    };

    /**
     * places and colors all the border containers
     */
    this.setContainersProperties = function () {
        var border_width = this.getBorderWidth(),
            corner_width = border_width * 2;

        // top
        this.$containers.top
            .width(this.img_dims.width)
            .height(border_width)
            .css('left', 0)
            .css('top', 0);

        // right
        this.$containers.right
            .width(border_width)
            .height(this.img_dims.height)
            .css('left', this.img_dims.width - border_width)
            .css('top', 0);

        // bottom
        this.$containers.bottom
            .width(this.img_dims.width)
            .height(border_width)
            .css('left', 0)
            .css('top', this.img_dims.height - border_width);

        // left
        this.$containers.left
            .width(border_width)
            .height(this.img_dims.height)
            .css('left', 0)
            .css('top', 0);

        // nord west corner
        this.$containers.nw
            .width(corner_width)
            .height(corner_width)
            .css('left', 0)
            .css('top', 0)
            .css('border-radius', corner_width + 'px 0 0 0')
            .css('border-left-width', border_width + 'px')
            .css('border-top-width', border_width + 'px');

        // nord east corner
        this.$containers.ne
            .width(corner_width)
            .height(corner_width)
            .css('left', this.img_dims.width - this.$containers.ne.outerWidth())
            .css('top', 0)
            .css('border-radius', '0 ' + corner_width + 'px 0 0')
            .css('border-width', border_width + 'px ' + border_width + 'px 0 0');

        // south east corner
        this.$containers.se
            .width(corner_width)
            .height(corner_width)
            .css('left', this.img_dims.width - this.$containers.se.outerWidth())
            .css('top', this.img_dims.height - this.$containers.se.outerHeight())
            .css('border-radius', '0 0 ' + corner_width + 'px 0')
            .css('border-right-width', border_width + 'px')
            .css('border-bottom-width', border_width + 'px');

        // south east corner
        this.$containers.sw
            .width(corner_width)
            .height(corner_width)
            .css('left', 0)
            .css('top', this.img_dims.height - this.$containers.se.outerHeight())
            .css('border-radius', '0 0 0 ' + corner_width + 'px')
            .css('border-left-width', border_width + 'px')
            .css('border-bottom-width', border_width + 'px');
    };

    /**
     * returns the calculated border width
     *
     * @return float
     */
    this.getBorderWidth = function () {
        var width = this.img_dims.width,
            height = this.img_dims.height;
        return Math.round(((width + height) / 2) * this.scale_factor);
    };

    /**
     * invoke the constructor
     */
    this._construct($cibuilder, $wrapper);
}

module.exports = BorderModule;