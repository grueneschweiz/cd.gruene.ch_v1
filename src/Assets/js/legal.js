/**
 * jQuery wrapper
 */
(function ($) {
    "use strict";

    $.fn.legalChecker = function () {
        var self = this;

        this._construct = function () {
            $('input[name="people"]').on('change', this.people);
            $('input[name="right_of_personality"]').on('change', this.right_of_personality);
            $('select[name="own_image"]').on('change', this.own_image);

            $('#submit-legal').on('click', this.submit_legal);

            $('#stock-image-questions').hide();
            $('#photographer-questions').hide();
            $('#originator-warning').hide();
            $('#usage-questions').hide();
            $('#others_can_use').prop('disabled', true);
        };

        this.people = function ($el) {
            var $el = $($el.target);
            $('input[name="people"]').removeAttr('checked').parent().removeClass('active');
            $el.attr('checked', 'checked').parent().addClass('active');

            if (1 === parseInt($el.val())) {
                $('#right_of_personality-input').show();
                self.others_can_use();
            } else {
                $('#right_of_personality-input').hide();
                self.others_can_use();
            }
        };

        this.right_of_personality = function ($el) {
            var $el = $($el.target);
            $('input[name="right_of_personality"]').removeAttr('checked').parent().removeClass('active');
            $el.attr('checked', 'checked').parent().addClass('active');

            if (1 === parseInt($el.val())) {
                $('#right_of_personality-warning').hide();
                self.others_can_use();
            } else {
                $('#right_of_personality-warning').show();
                self.others_can_use();
            }
        };

        this.others_can_use = function () {
            var others_can_use = true;

            if (1 === parseInt($('input[name="people"]:checked').val())
                && 1 !== parseInt($('input[name="right_of_personality"]:checked').val())) {
                others_can_use = false;
            }

            if (-1 !== $.inArray($('select[name="own_image"]').val(), ['stock', 'unknown', '0'])) {
                others_can_use = false;
            }

            $('#others_can_use').prop('disabled', !others_can_use).removeAttr('checked');
        };

        this.own_image = function ($el) {
            switch ($($el.target).val()) {
                case 'me':
                    $('input[name="originator"]').val(user.first_name + ' ' + user.last_name);
                    $('#stock-image-questions').hide();
                    $('#photographer-questions').show();
                    $('#originator-warning').hide();
                    $('#usage-questions').show();
                    self.others_can_use();
                    break;
                case 'stock':
                    $('input[name="originator"]').val('');
                    $('#photographer-questions').show();
                    $('#stock-image-questions').show();
                    $('#originator-warning').hide();
                    $('#usage-questions').show();
                    self.others_can_use();
                    break;
                case 'agency':
                    $('input[name="originator"]').val('');
                    $('#stock-image-questions').hide();
                    $('#photographer-questions').show();
                    $('#originator-warning').hide();
                    $('#usage-questions').show();
                    self.others_can_use();
                    break;
                case 'friend':
                    $('input[name="originator"]').val('');
                    $('#stock-image-questions').hide();
                    $('#photographer-questions').show();
                    $('#originator-warning').hide();
                    $('#usage-questions').show();
                    self.others_can_use();
                    break;
                case 'unknown':
                    $('input[name="originator"]').val('');
                    $('#stock-image-questions').hide();
                    $('#photographer-questions').hide();
                    $('#originator-warning').show();
                    $('#usage-questions').show();
                    self.others_can_use();
                    break;
                case '0':
                    $('input[name="originator"]').val('');
                    $('#stock-image-questions').hide();
                    $('#photographer-questions').hide();
                    $('#originator-warning').hide();
                    $('#usage-questions').hide();
                    self.others_can_use();
                    break;
            }
        };

        this.submit_legal = function () {
            if (!self.validate()) {
                return;
            }

            var data = {
                hash: $('input[name="hash"]').val(),
                people: $('input[name="people"]:checked').val(),
                right_of_personality: $('input[name="right_of_personality"]:checked').val(),
                own_image: $('select[name="own_image"]').val(),
                source: $('input[name="source"]').val(),
                licence: $('select[name="licence"]').val(),
                originator: $('input[name="originator"]').val(),
                i_can_use: $('input[name="i_can_use"]').is(':checked'),
                others_can_use: $('input[name="others_can_use"]').is(':checked')
            };

            $.ajax({
                url: '/images/ajaxAddLegal',
                type: 'POST',
                data: data,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', x_csrf_token);
                    $(self).hide();
                    $('#sending-legal-loader').show();
                }
            }).done(function (xhr, status) {
                if ('success' === status && xhr === 'true') {
                    $('#download-button').show();
                } else {
                    $('#download-button').hide();
                    $('.warning-image-generation-error').removeClass('d-none');
                }
            }).always(function () {
                $('#sending-legal-loader').hide();
            }).fail(function (data, status, error) {
                console.log(data, status, error);
                $('.warning-image-generation-error').removeClass('d-none');
            });
        };

        this.validate = function () {
            var valid = true;
            $('.legal-invalid-input').remove();

            if ('0' === $('select[name="own_image"]').val()) {
                this.markInvalid($('#own-image'), trans.own_image_empty);
                valid = false;
            }

            if (!$('#i_can_use:checked').length) {
                this.markInvalid($('#i_can_use'), trans.can_i_use);
                valid = false;
            }

            if (-1 !== $.inArray($('select[name="own_image"]').val(), ['me', 'stock', 'agency', 'friend'])
                && '' === $('#originator').val().trim()
                && 'cc' !== $('#licence').val()) {
                this.markInvalid($('#originator'), trans.originator_empty);
                valid = false;
            }

            if ('stock' === $('select[name="own_image"]').val()
                && '' === $('#source').val().trim()) {
                this.markInvalid($('#source'), trans.source_empty);
                valid = false;
            }

            return valid;
        };

        this.markInvalid = function ($el, message) {
            var error = '<div class="legal-invalid-input alert alert-danger mt-1" role="alert">' +
                '<strong>' + trans.invalid + '</strong> ' + message + '</div>';
            $el.focus().parent().append(error);
        };

        this._construct();
        return this;
    };

})(jQuery);