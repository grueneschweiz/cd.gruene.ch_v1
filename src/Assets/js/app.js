require('../css/main.scss');


require('jquery');
require('jquery-ui/dialog');
require('jquery-ui/tooltip');
require('chosen-js');
require('cropit');

require('./cibuilder.js');
require('./legal.js');

window.$ = $;

$(document).ready(function () {
    $('.chosen').chosen();
});

var $cibuilder;
var newHash;

$(document).ready(function () {
    'use strict';

    // --------------------------------------
    // stop here, if this is not the builder
    // --------------------------------------
    if (0 === $('#builder-canvas').length) {
        return;
    }

    $('#legal-check').legalChecker();

    $('#canvas-width-setter, #canvas-height-setter').trigger('change');
    var startup_width = $('#canvas-width-setter').val();
    var startup_height = $('#canvas-height-setter').val();
    var initialImage = true;

    // downloading dialog
    $('#generating-image').dialog({
        autoOpen: false,
        modal: true,
        width: $(window).width() < 600 ? $(window).width() * 0.9 : 600
    });

    // cropit
    var $imageCropper = $('#image-cropper');
    $imageCropper.cropit({
        allowDragNDrop: false, // due to some error
        imageState: {src: '/img/bg5000.png'},
        imageBackground: true,
        imageBackgroundBorderWidth: 75,
        width: startup_width,
        height: startup_height,
        smallImage: 'allow',
        $fileInput: $('input.cropit-image-input'),
        $zoomSlider: $('input.cropit-image-zoom-input'),
        onImageError: function (err) {
            if (0 === err.code) {
                $('.warning-image-format-error').removeClass('d-none');
            }
            if (1 === err.code) {
                $('.warning-image-size-error').removeClass('d-none');
            }
        },
        onImageLoaded: function () {
            $('.warning-image-format-error').addClass('d-none');
            $('.warning-image-size-error').addClass('d-none');
        },
        onFileChange: function () {
            $('.image-modifier-controls').show();
            $('#color-scheme-form').removeClass('d-none');
            $('#color_scheme').val('green').trigger('change');
            initialImage = false;
            $cibuilder.trigger('imageChanged');
        }
    });

    // remove image
    $('#remove-image').click(function () {
        if (confirm(trans.reload)) {
            location.reload();
        }
    });

    // handle rotation
    $('.rotate-cw-btn').click(function () {
        $imageCropper.cropit('rotateCW');
    });
    $('.rotate-ccw-btn').click(function () {
        $imageCropper.cropit('rotateCCW');
    });

    // hide bars and logo on image move
    $('.cropit-preview-image').on('mousedown', function () {
        $("#image-bars-dragger, #logo-wrapper").hide();
    }).on('mouseup mouseleave', function () {
        $("#image-bars-dragger, #logo-wrapper").fadeIn();
    });

    // add borders container to image
    var iborders = '<div id="border-wrapper"></div>';
    $imageCropper.append(iborders);

    // add logo container to image
    var ilogo = '<div id="logo-wrapper"></div>';
    $imageCropper.append(ilogo);

    // add bars container to image
    var ibars = '<div id="image-bars-dragger" class="' + $('#layout').val() + '"><div id="image-bars" class="' + $('#layout').val() + '"></div></div>';
    $imageCropper.append(ibars);

    // instantiate bars object
    $cibuilder = $('#image-bars').cibuilder({
        form: '#bars-form',
        border: '#border-wrapper',
        logo: '#logo-wrapper'
    });

    // pre populate it
    $cibuilder.add({
        text: trans.headline_1,
        type: 'headline white left'
    });
    $cibuilder.add({
        text: trans.headline_2,
        type: 'headline magenta left'
    });
    $cibuilder.add({
        text: trans.subline_1,
        type: 'subline white left'
    });

    // add subline
    $('#add-subline').click(function () {
        $cibuilder.add({
            text: trans.subline_1,
            type: 'subline white ' + $('#layout').val()
        });
        $(this).hide();
    });

    // font size
    $('.font-size-slider').on('input change', function () {
        $cibuilder.setFontSize($(this).val());
    });

    // clickable slider icons
    $('.custom-range-slider').each(function () {
        $(this).find('.increaser').click(function () {
            var $slider = $(this).parent().parent().find('input[type="range"]'),
                step = (parseFloat($slider.attr('max')) - parseFloat($slider.attr('min'))) / 10;
            $slider.val(parseFloat($slider.val()) + step);
            $slider.trigger('change');
        });

        $(this).find('.decreaser').click(function () {
            var $slider = $(this).parent().parent().find('input[type="range"]'),
                step = (parseFloat($slider.attr('max')) - parseFloat($slider.attr('min'))) / 10;
            $slider.val(parseFloat($slider.val()) - step);
            $slider.trigger('change');
        });
    });

    // format
    $('#canvas-format').change(function () {
        var $width = $('#canvas-width-setter'),
            $height = $('#canvas-height-setter'),
            image = $imageCropper.cropit('imageSize'),
            wfactor,
            hfactor,
            factor,
            dims;

        if ('custom' === $(this).val()) {
            $width.add($height).removeAttr('disabled');
        } else {
            $width.add($height).attr('disabled', 'disabled');

            dims = $(this).val().split('x');
            $width.val(dims[0]);
            $height.val(dims[1]);
        }

        $imageCropper.cropit('previewSize', {width: $width.val(), height: $height.val()});

        // set zoom of initial image
        if (initialImage) {
            wfactor = $width.val() / image.width;
            hfactor = $height.val() / image.height;
            factor = wfactor > hfactor ? wfactor : hfactor;
            $imageCropper.cropit('zoom', factor);
        }

        $("#image-bars-dragger").width($width.val());

        $cibuilder.trigger('canvasSizeChanged');
    });
    $('#canvas-width-setter, #canvas-height-setter').on('change keyup', function () {
        var min = parseInt($(this).attr('min')),
            max = parseInt($(this).attr('max'));
        if ($(this).val() < min) {
            return;
        } else if ($(this).val() > max) {
            return;
        }
        $('#canvas-format').trigger('change');
    });

    // layout
    $('#layout').change(function () {
        // set classes
        $('#image-bars').add('#image-bars * .bar').add('#image-bars-dragger')
            .removeClass('left right')
            .addClass($(this).val());

        $cibuilder.trigger('layoutChanged');
    });

    // color scheme
    $('#color_scheme').change(function () {
        $cibuilder.setScheme($(this).val());
    });

    // border
    $cibuilder.setBorder($('#border-form').val());
    $('#border-form').change(function () {
        $cibuilder.setBorder($(this).val());
    });

    // dragger
    $("#image-bars-dragger").draggable({
        axis: 'y',
        containment: '.cropit-preview-image-container',
        stop: function () {
            $cibuilder.trigger('barDragStop');
        }
    }).width(startup_width);

    // logo
    $('#logo').change(function () {
        var jsondata,
            data = {id: $(this).val()};

        jsondata = JSON.stringify(data);

        $.ajax({
            url: '/images/ajaxGetLogo',
            type: 'POST',
            data: {id: jsondata},
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', x_csrf_token);
            }
        }).done(function (data, status) {
            if (status === 'success') {
                $cibuilder.setLogo($.parseJSON(data));
            } else {
                alert(trans.logo_loding_error);
            }
        });
    });

    // make sure to get a good start
    $('#canvas-format').trigger('change');
    $cibuilder.trigger('fontSizeChanged');

    // move dragger to bottom to start
    $('#image-bars-dragger').css('top', $imageCropper.cropit('previewSize').height - $('#image-bars-dragger').height());

    // tooltips
    $('.helptext').tooltip();

    // generate image
    $("#image-generate").click(function () {
        var jsondata,
            bardata = [],
            data = {},
            $rotator = $('#logo-rotator'),
            $logo_top = $('#logo-top'),
            $subline = $('#logo-subline'),
            image_pos = $('#image-bars').offset(),
            cropper_pos = $('#image-cropper').offset(),
            logo_pos = $('#logo-wrapper').offset(),
            y_pos = image_pos.top - cropper_pos.top,
            x_pos = image_pos.left - cropper_pos.left,
            logo_y_pos = logo_pos.top - cropper_pos.top,
            logo_x_pos = logo_pos.left - cropper_pos.left;

        $('#image-bars div.bar').each(function (index, value) {
            var $obj = $(value);

            bardata[index] = {
                text: $obj.text(),
                type: $obj.attr('class'),
                fontsize: parseFloat($obj.css('font-size'))
            };
        });

        // make sure no bars are left empty
        for (var i = 0; i < bardata.length; i++) {
            if ("" === bardata[i].text.replace(/\s/g, '')) {
                alert(trans.empty_bars);
                return;
            }
        }

        $rotator.css('transform', 'none');

        data = {
            image: {
                zoom: $imageCropper.cropit('zoom'),
                size: $imageCropper.cropit('imageSize'),
                pos: $imageCropper.cropit('offset'),
                src: $imageCropper.cropit('imageSrc'),
                name: $('.cropit-image-input').val().split('\\').pop().split('/').pop() // http://stackoverflow.com/questions/423376/how-to-get-the-file-name-from-a-full-path-using-javascript
            },
            preview: {
                size: $imageCropper.cropit('previewSize')
            },
            bars: {
                data: bardata,
                y_pos: y_pos,
                x_pos: x_pos
            },
            border: {
                type: $('#border-form').val(),
                width: Math.round($('.border-container.border-left').width())
            },
            logo: {
                src: $logo_top.attr('data'),
                width: $logo_top.width(),
                height: $logo_top.height(),
                y_pos: logo_y_pos,
                x_pos: logo_x_pos,
                margin: parseFloat($rotator.css('margin-top')),
                fontsize: parseFloat($subline.css('font-size')),
                subline: $subline.text(),
                left: parseFloat($subline.css('margin-left'))
            },
            hash: $('input[name="hash"]').val()
        };

        $rotator.css('transform', 'rotate(-5deg)');

        jsondata = JSON.stringify(data);

        $.ajax({
            url: '/images/ajaxAdd',
            type: 'POST',
            data: {addImage: jsondata},
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', x_csrf_token);
                $('.warning-image-generation-error').addClass('d-none');
                if (initialImage) {
                    $('#legal-check').hide();
                    $('#please-fill-out-legal').hide();
                    $('#download-button').show();
                } else {
                    $('#legal-check').show();
                    $('#please-fill-out-legal').show();
                    $('#download-button').hide();
                }
                $('#download-button a#download-img').remove();
                $('#generating-image-loader').show();
                $('#sending-legal-loader').hide();
                $('#generating-image').dialog('open');
            }
        }).done(function (data, status) {
            $('#generating-image-loader').hide();

            if (status === 'success') {
                var content = $.parseJSON(data);
                if (content.filename === undefined) {
                    $('.warning-image-generation-error').removeClass('d-none');
                    if (content) {
                        $('.warning-image-generation-error span').text(content);
                    }
                    return;
                }
                newHash = content.newHash;
                $('#download-button').html('<a href="/protected/finalimages/' + content.filename + '" class="btn btn-outline-primary" id="download-img" download>' + trans.download_image + '</a>');
                $('#download-img').click(function () {
                    $('#generating-image').dialog('close');
                    $('input[name="hash"]').val(newHash);
                });
            } else {
                alert(trans.image_generation_error);
            }
        }).fail(function (data, status, error) {
            console.log(data, status, error);
            $('.warning-image-generation-error').removeClass('d-none');
        });
    });
});
