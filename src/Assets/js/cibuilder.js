/**
 * jQuery wrapper
 */
(function ($) {
    "use strict";

    var Logo = require('./modules/Logo');
    var Rules = require('./modules/Rules');
    var Border = require('./modules/Border');
    var Form = require('./modules/Form');
    var Image = require('./modules/Image');
    var Copyright = require('./modules/Copyright');

    /**
     * Instantiate jQuery plugin
     *
     * @returns {$.fn.cibuilder}
     */
    $.fn.cibuilder = function (options) {

        /**
         * keep me accessible
         */
        var self = this;

        /**
         * load modules and register event listeners on construction
         */
        this._construct = function (options) {
            this.image = new Image(this, $(this));
            this.border = new Border(this, $(options.border));
            this.form = new Form(this, $(options.form));
            this.logo = new Logo(this, $(options.logo));
            this.rules = new Rules(this);
            this.copyright = new Copyright(this, $(options.copyright_outer), $(options.copyright_inner));

            this.registerEventListeners();
        };

        /**
         * manage all event listeners
         *
         * these event are fired:
         * - canvasSizeChanged
         * - logoChanged
         * - imageChanged
         * - fontSizeChanged
         * - barAdded
         * - barRemoved
         * - textChanged
         * - barDragStop
         * - layoutChanged
         * - colorSchemeChanged
         * - borderChanged
         * - rulesApplied
         */
        this.registerEventListeners = function () {
            this.on('barAdded', function () {
                self.image.setDraggerHeight();
                self.image.setBarsXposition();
                self.setFontSize($('.font-size-slider').val());
                self.applyRules();
            });

            this.on('barRemoved', function () {
                self.image.setBarsXposition();
                self.image.setDraggerHeight();
                self.setFontSize($('.font-size-slider').val());
                self.applyRules();
            });

            this.on('canvasSizeChanged', function () {
                self.setLogo();
                // call this three times due to some recursive calculations
                self.setFontSize($('.font-size-slider').val());
                self.setFontSize($('.font-size-slider').val());
                self.setFontSize($('.font-size-slider').val());
                self.setBorder($('#border-form').val());
                self.setCopyright();
            });

            this.on('fontSizeChanged', function () {
                self.image.setDraggerHeight();
                self.image.setBarsTopPosition();
                self.image.moveDraggerIntoBox();
            });

            this.on('layoutChanged', function () {
                self.image.setBarsXposition();
                self.image.setDraggerHeight(); // must be called before font size is changed
                self.image.setBarsTopPosition(); // must be called before font size is changed
                self.setLogo();
                self.setFontSize($('.font-size-slider').val());
                self.setCopyright();
            });

            this.on('barDragStop', function () {
                self.setLogo();
            });

            this.on('copyrightChanged', function() {
                self.setCopyright();
            });

            this.on('colorSchemeChanged', function () {
                self.setLogo();
            });

            this.on('borderChanged', function () {
                self.image.setBarsXposition();
                self.setCopyright();
            });

            this.on('textChanged', function () {
                self.setFontSize($('.font-size-slider').val());
            });
        };

        /**
         * Add the bar to the form and the image
         *
         * @param {object} options in the folowing format:
         * { text : 'string to display', type : 'headline|subline white|green|magenta', [num : int] }
         * @returns {$.fn.$cibuilder}
         */
        this.add = function (options) {

            // Set the id of the bar we want to add
            this.setCurrentBarId(options);

            // Make sure we dont overwrite any bars
            this.image.reorderBars(this.current_bar_id, 'add');
            this.form.reorderBars(this.current_bar_id, 'add');

            // Add the bar
            this.image.addBar(options, this.current_bar_id);
            this.form.addBar(options, this.current_bar_id);

            this.trigger('barAdded');

            return this;
        };

        /**
         * Remove bar with given class id from canvas and form
         *
         * @param {object} options in the folowing format: { num : int }
         * @returns {$.fn.$cibuilder}
         */
        this.remove = function (options) {
            // Set the id of the bar we want to remove
            this.setCurrentBarId(options);

            // remove the bar
            this.image.removeBar(this.current_bar_id);
            this.form.removeBar(this.current_bar_id);

            // make the order neat again
            this.image.reorderBars(this.current_bar_id, 'remove');
            this.form.reorderBars(this.current_bar_id, 'remove');

            this.trigger('barRemoved');

            return this;
        };

        /**
         * Set current bar id
         *
         * If the options contain a num object set it to the num, else
         * set it to the total of bars on the canvas + 1
         *
         * @param {object} options in the folowing format:
         * { text : 'string to display', type : 'headline|subline white|green|magenta', [num : int] }
         * @returns {$.fn.$cibuilder}
         */
        this.setCurrentBarId = function (options) {
            // if a num was given
            if (typeof options.num !== 'undefined') {
                // set it as the current
                this.current_bar_id = options.num;
            } else {
                // else set the last + 1 as the current
                this.current_bar_id = this.image.countBars() + 1;
            }

            return this;
        };

        /**
         * Set color scheme
         *
         * @param {string} scheme options 'green', 'white', 'greengreen'
         */
        this.setScheme = function (scheme) {
            this.image.setScheme(scheme);

            this.trigger('colorSchemeChanged');
        };

        /**
         * Set border
         *
         * @param type string accepts 'auto', 'none'
         * @returns {$.fn.$cibuilder}
         */
        this.setBorder = function (type) {
            // set border
            this.border.set(type);

            this.trigger('borderChanged');

            return this;
        };

        /**
         * apply rules
         *
         * @returns {$.fn.$cibuilder}
         */
        this.applyRules = function () {
            // apply rules
            this.rules.apply();

            this.trigger('rulesApplied');

            return this;
        };

        /**
         * Set logo
         *
         * @param data object must contain top_path and subline
         * @returns {$.fn.$cibuilder}
         */
        this.setLogo = function (data) {
            if (data) {
                this.logo.data = data;
            }

            if (this.logo.data) {
                this.logo.set();

                this.trigger('logoChanged');
            }

            return this;
        };

        /**
         * Set the font size of the bars
         *
         * @param {float} size
         * @returns {$.fn.$cibuilder}
         */
        this.setFontSize = function (size) {
            this.image.setFontSize(size);

            this.trigger('fontSizeChanged');

            return this;
        };

        /**
         * Set copyright
         *
         * @returns {$.fn.$cibuilder}
         */
        this.setCopyright = function () {
            var $img = $('.cropit-preview-image-container');

            this.copyright.set(
                $('#copyright').val(),
                $('#layout').val(),
                $('#border-form').val(),
                $img.outerHeight(),
                $img.outerWidth()
            );

            return this;
        };

        /**
         * invoke the constructor
         */
        this._construct(options);

        return this;
    };

})(jQuery);
