/**
 * The Form class
 *
 * @param {$.fn.cibuilder} $cibuilder
 * @param {jQuery} $form
 */
function FormModule($cibuilder, $form) {
    "use strict";

    /**
     * load the containers on invokation
     *
     * @param {$.fn.cibuilder} $cibuilder
     * @param {jQuery} $form
     */
    this._construct = function ($cibuilder, $form) {
        this.$cibuilder = $cibuilder;
        this.image = $cibuilder.image;
        this.selectBars($form);
    };

    /**
     * adds a bar to $bars
     *
     * @param {object} options
     * @param {int} num position number of the bar
     */
    this.addBar = function (options, num) {
        this.addBarHtml(options, num);
        this.addButtonEvents();
        this.addBarEvents();
    };

    /**
     * Remove the bar row with the class bar-x where x=num
     *
     * @param {int} num
     */
    this.removeBar = function (num) {
        var $el = this.$bars.children('.bar-' + num);
        $el.remove();
    };

    /**
     * select the bars table
     */
    this.selectBars = function (formSelector) {
        this.$bars = $(formSelector);
    };

    /**
     * reorders the bars after deleting one or before adding one
     *
     * @param {int} id
     * @param {string} mode accepts 'add|delete'
     */
    this.reorderBars = function (id, mode) {
        var i, tmp, $el, parent = this;

        // remove all id classes - lets start clean
        this.removeBarIdClasses();

        // get all selectors of elements that need a new id class
        var bar_element_selectors = this.getClassIdSelectors();

        // loop over all selectors
        $.each(bar_element_selectors, function (index, selector) {
            i = 0;

            // select element and add new id class
            $(selector).each(function () {
                i++;
                $el = $(this);

                // skip the id we want to add
                if (mode === 'add' && i === id) {
                    i++;
                }

                // get class to add
                tmp = selector.match(/\.\w+$/)[0];
                tmp = tmp.substr(1);
                tmp = tmp + '-' + i;

                // add new class
                $el.addClass(tmp);

                // if its an input element
                if ($el.is('input')) {
                    // also change the name
                    $el.attr('name', tmp);

                    // and bind events again
                    parent.$current_bar = $('div.row.' + tmp);
                    parent.addBarEvents();
                }
            });
        });
    };

    /**
     * Remove all html classes with a bar id
     *
     * @returns {Boolean} false if there was nothing to do
     */
    this.removeBarIdClasses = function () {
        var class_names = this.getBarClassNamesWidthId(),
            self = this;

        // abort if no objects where found
        if (class_names === false) {
            return false;
        }

        // loop over given class names
        $.each(class_names, function (index, class_name) {
            // get elements with class name and remove class
            self.$bars.find('.' + class_name).removeClass(class_name);
        });
    };

    /**
     * Return an array with all form elements containing a nummerical class selector
     *
     * @returns {Array}
     */
    this.getClassIdSelectors = function () {
        return [
            'div.row.bar',
            'div.input.bar',
            'div.barbuttons.bar',
            'input.bar',
            '.barbutton.add',
            '.barbutton.remove',
            '.barbutton.subline',
            '.barbutton.headline',
            '.barbutton.accent_color',
            '.barbutton.main_color'
        ];
    };

    /**
     * Return the bars class names (without ids)
     *
     * @returns {Array}
     */
    this.getBarClassNames = function () {
        return [
            'bar',
            'add',
            'remove',
            'subline',
            'headline',
            'accent_color',
            'main_color'
        ];
    };

    /**
     * Return the bars identifing class names like bar-1, add-2 etc.
     *
     * @returns {Array}
     */
    this.getBarClassNamesWidthId = function () {
        var class_names = [],
            ids = this.getBarClassIds(),
            classes = this.getBarClassNames();

        // abort if no objects where found
        if (ids === false) {
            return false;
        }

        // loop over all ids and classes and add them to the class_names array
        $.each(ids, function (i1, id) {
            $.each(classes, function (i2, class_name) {
                class_names.push(class_name + '-' + id);
            });
        });

        return class_names;
    };

    /**
     * returns an array with all bar class name ids
     *
     * @returns {Array|Boolean} false if there are none else array with the ids
     */
    this.getBarClassIds = function () {
        var tmp,
            $objs = this.$bars.find('div.bar'),
            ids = [];

        // abort if no objects were found
        if ($objs.length === 0) {
            return false;
        }

        // loop throu rows and add the identifing class names number
        $objs.each(function () {
            tmp = $(this).attr('class');
            ids.push(tmp.match(/\d+/)[0]);
        });

        return ids;
    };

    /**
     * generates the specific html for a form bar
     *
     * @param {int} num
     * @param {string} type
     * @param {string} content
     * @returns string the html
     */
    this.getBarDiv = function (num, type, content) {
        return '<div class="bar bar-' + num + ' ' + type + '">' + content + '</div>';
    };

    /**
     * generates html for the buttons
     *
     * the barbuttons are defined in global scope (add.ctp)
     *
     * @param {int} num
     * @returns {string} the html
     */
    this.getBarButtons = function (num) {
        var html = '',
            barbuttons = [
                ['add', 'fa fa-plus-circle', 'left'],
                ['remove', 'fa fa-minus-circle', 'right'],
            ];
        $.each(barbuttons, function (index, button) {
            html += '<span class="barbutton ' + button[0] + ' ' + button[0] + '-' + num + ' ' + button[1] + ' btn btn-outline-primary float-' + button[2] + '"></span>';
        });
        return html;
    };

    /**
     * generates the specific html for a form bar input field
     *
     * @param {int} num
     * @param {string} value
     * @returns {string} the form bar input html
     */
    this.getBarInputHtml = function (num, value) {
        return '<input type="text" name="bar-' + num + '" class="bar bar-' + num + ' input field form-control" value="' + value + '">';
    };

    /**
     * adds a bar form html to $bars
     *
     * @param {object} options
     * @param {int} num position number of the bar
     */
    this.addBarHtml = function (options, num) {
        var $current_bar,
            value = options.text,
            color = options.type.match(/magenta/) ? 'magenta' : 'green',
            barHtml = '<div class="row mb-1 bar bar-' + num + ' current_bar ' + color + '">' +
                this.getBarDiv(num, 'input col', this.getBarInputHtml(num, value)) +
                this.getBarDiv(num, 'barbuttons', this.getBarButtons(num)) +
                '</div>';

        // if its the first element
        if (num === 1) {
            this.$bars.prepend(barHtml);
        } else {
            this.$bars.find('div.row.bar-' + (num - 1)).after(barHtml);
        }

        // set current bar
        $current_bar = $('div.current_bar');
        $current_bar.removeClass('current_bar');
        this.$current_bar = $current_bar;

    };

    /**
     * Adds the key up and the paste events to the input fields
     */
    this.addBarEvents = function () {
        var $bar = this.$current_bar,
            num = this.getStringDigits($bar.attr('class').match(/bar-\d+/)[0]),
            self = this;

        // prevent double binding
        $bar.off('keyup paste');

        $bar.on('keyup paste', function () {
            self.image.$container.find('.bar-' + num).text($bar.find('input.bar-' + num).val());
            self.$cibuilder.trigger('textChanged');
        });
    };

    /**
     * return the number of bars in $bars
     *
     * @returns {int}
     */
    this.countBars = function () {
        return this.$bars.find('div.bar.row').length;
    };

    /**
     * Detects if there will be more magenta headlines than green ones if we insert another magenta one
     *
     * @param {int} num
     * @returns {Boolean}
     */
    this.isAccentColorDominating = function (num) {
        var accent = this.image.$container.find('.headline.magenta').length,
            main = this.image.$container.find('.headline.green, .headline.white').length;

        if (this.image.$container.find('.bar-' + num).hasClass('magenta')) {
            if (accent >= main) {
                return true;
            }
        }

        return false;
    };

    /**
     * add the click events to the bar form buttons
     */
    this.addButtonEvents = function () {
        var $bar = this.$current_bar,
            self = this;

        // add button
        $bar.find('.barbutton.add').click(function () {
            var num = self.getStringDigits($(this).attr('class').match(/add-\d+/)[0]),
                resp = true;

            // check if user wants more magenta than green
            if (self.isAccentColorDominating(num)) {
                // if so, ask him, if he is sure
                resp = confirm(trans.accent_headline_warning);
            }

            // add bar if magenta doesn't dominate or if user wants it to dominate
            if (resp) {
                self.$cibuilder.add({
                    text: trans.new_line,
                    type: self.parseType(self.image.$container.find('.bar-' + num).attr('class')),
                    num: num + 1
                });
            }
        });

        // remove button
        $bar.find('.barbutton.remove').click(function () {
            var num = self.getStringDigits($(this).attr('class').match(/remove-\d+/)[0]);
            self.$cibuilder.remove({num: num});
        });
    };

    /**
     * cuts the 'bar' and 'bar-x' html-class out of a html-class string
     *
     * @param {string} string
     * @returns {string}
     */
    this.parseType = function (string) {
        string = string.replace('row', '');
        string = string.replace('bar', '');
        string = string.replace(/bar-\d+/, '');
        string = string.replace(/\s+/, ' ');
        string = string.trim();
        return string;
    };

    /**
     * Return the digits in a string
     *
     * @param {string} string
     * @returns {int}
     */
    this.getStringDigits = function (string) {
        return parseInt(string.match(/\d+/)[0]);
    };

    /**
     * invoke constructor
     */
    this._construct($cibuilder, $form);
}

module.exports = FormModule;