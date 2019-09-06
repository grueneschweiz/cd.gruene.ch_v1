/**
 * The Rules class
 *
 * This class manages all appearance of the little bar buttons, in
 * respect of the corporate design rules.
 *
 * @param {$.fn.cibuilder} $cibuilder
 */
function RulesModule($cibuilder) {
    /**
     * the image object
     */
    var image;

    /**
     * the form object
     */
    var form;

    /**
     * number of subline bars
     */
    var subline_num;

    /**
     * number of headline bars in the accent color
     */
    var accent_headline_num;

    /**
     * number of headline bars in the main color
     */
    var main_headline_num;

    /**
     * populate properties on invokation
     *
     * @param {$.fn.cibuilder} $cibuilder
     */
    this._construct = function ($cibuilder) {
        this.$cibuilder = $cibuilder;
        this.image = $cibuilder.image;
        this.form = $cibuilder.form;
    };

    /**
     * Set the visibility of the bar buttons in respect of the rules
     */
    this.apply = function () {
        var self = this,
            type;

        this.setSublineNum();
        this.setAccentHeadlineNum();
        this.setMainHeadlineNum();

        this.form.$bars.find('.barbuttons').each(function () {
            type = self.getBarType($(this));

            self.addButton($(this).find('.add'), type);
            self.removeButton($(this).find('.remove'), type);
        });

        this.addSublineButton();
    };

    /**
     * Controls the visibility of the add subline button
     */
    this.addSublineButton = function () {
        if (this.subline_num < 1) {
            $('#add-subline').show();
        } else {
            $('#add-subline').hide();
        }
    };

    /**
     * Return a string identifing the type of the bar
     *
     * @param {jQuery} $buttons
     * @returns {String} 'subline|accent_headline|main_headline'
     */
    this.getBarType = function ($buttons) {
        var num = $buttons.attr('class').match(/bar-\d+/)[0].match(/\d+/)[0],
            $img = this.image.$container.find('.bar-' + num);

        if ($img.hasClass('subline')) {
            return 'subline';
        }

        if ($img.hasClass('headline') && $img.hasClass('magenta')) {
            return 'accent_headline';
        }

        return 'main_headline';
    };

    /**
     * sets the number of subline bars
     */
    this.setSublineNum = function () {
        this.subline_num = this.image.$container.find('.subline').length;
    };

    /**
     * sets the number of headline bars in the accent color
     */
    this.setAccentHeadlineNum = function () {
        this.accent_headline_num = this.image.$container.find('.headline.magenta').length;
    };

    /**
     * sets the number of headline bars in the main color
     */
    this.setMainHeadlineNum = function () {
        this.main_headline_num = this.image.$container.find('.headline.green, .headline.white').length;
    };

    /**
     * Controlls the visibility of the given add button, regarding the CD rules
     *
     * @param {jQuery} $button
     * @param {String} type
     */
    this.addButton = function ($button, type) {
        var show = false;

        switch (type) {
            case 'subline':
                if (this.subline_num < 2) {
                    show = true;
                    this.addSublineButton();
                }
                break;

            case 'accent_headline':
                if (this.accent_headline_num < 2 && (this.main_headline_num + this.accent_headline_num) < 3) {
                    show = true;
                }
                break;

            case 'main_headline':
                if ((this.main_headline_num + this.accent_headline_num) < 3) {
                    show = true;
                }
                break;
        }

        this.show($button, show);
    };

    /**
     * Controlls the visibility of the given remove button, regarding the CD rules
     *
     * @param {jQuery} $button
     * @param {String} type
     */
    this.removeButton = function ($button, type) {
        var show = false;

        switch (type) {
            case 'subline':
                show = true;
                break;

            case 'accent_headline':
                if (this.accent_headline_num > 1) {
                    show = true;
                }
                break;

            case 'main_headline':
                if (this.main_headline_num > 1) {
                    show = true;
                }
                break;
        }

        this.show($button, show);
    };

    /**
     * Show or hide a given object
     *
     * @param {jQuery} $button
     * @param {bool} bool
     */
    this.show = function ($button, bool) {
        if (bool) {
            $button.show();
        } else {
            $button.hide();
        }
    };

    /**
     * invoke constructor
     */
    this._construct($cibuilder);
}

module.exports = RulesModule;
