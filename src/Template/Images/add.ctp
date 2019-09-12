<div id="builder-canvas">
    <div class="intro mt-3 mb-5"><h1><?= __('Create an image'); ?></h1>
        <p><?= __('Getting your images styled is now a breeze. Try it out!') ?></p>
    </div>

    <div id="image-size" class="row mb-3">
        <div class="col-12 col-md-8">
            <span class="helptext"
                  title="<?= __('This defines the size of the final image. Use \'custom size\' for non-predefined dimensions.') ?>"></span>
            <?= $this->Form->input('canvas-format', [
                'options' => $image_sizes,
                'label' => __('Select image size'),
                'class' => 'form-control'
            ]); ?>
        </div>
        <div class="col-6 col-md-2">
            <span class="helptext" title="<?= __('Max width is 5000px.') ?>"></span>
            <?= $this->Form->input('canvas-width-setter',
                [
                    'label' => __('Width (px)'),
                    'type' => 'number',
                    'min' => 100,
                    'max' => 5000,
                    'step' => 1,
                    'class' => 'form-control',
                ]); ?>
        </div>
        <div class="col-6 col-md-2">
            <span class="helptext" title="<?= __('Max height is 5000px.') ?>"></span>
            <?= $this->Form->input('canvas-height-setter',
                [
                    'label' => __('Height (px)'),
                    'type' => 'number',
                    'min' => 100,
                    'max' => 5000,
                    'step' => 1,
                    'class' => 'form-control',
                ]); ?>
        </div>
    </div>

    <span class="helptext"
          title="<?= __('The logo is mandatory and may only be omitted on graphics that are part of an already well branded product.') ?>"></span>
    <?= $this->Form->input('logo', [
        'options' => $logos,
        'empty' => true,
        'label' => __('Logo'),
        'class' => 'form-control mb-3',
    ]); ?>

    <div class="row mt-3 mb-3">
        <div class="col-12 col-form-label">
            <span class="helptext"
                  title="<?= __('You can upload any JPEG or PNG file. Make sure the resolution is high enough to fill the image.') ?>"></span>
            <span><?= __('Choose image'); ?></span>
        </div>
        <div class="input-group col-12">
            <div class="custom-file">
                <label class="custom-file-label"
                       for="image-upload"><?= __('Click to select image to upload'); ?></label>
                <input type="file" class="custom-file-input cropit-image-input" id="image-upload"
                       accept=".png, .jpg, .jpeg, image/png, image/jpeg">
            </div>
            <div class="input-group-append">
                <button id="remove-image" class="btn btn-outline-secondary"
                        type="button"><?= __('Remove image'); ?></button>
            </div>
        </div>
    </div>

    <div class="warning-image-format-error d-none alert alert-danger mb-3" role="alert">
        <strong><?= __('Warning!') ?></strong>
        <?= __('Image could not be loaded. Make sure to use either the JPEG or PNG format.') ?>
    </div>
    <div class="warning-image-size-error d-none alert alert-danger mb-3" role="alert">
        <strong><?= __('Warning!') ?></strong>
        <?= __('This image is to small. Please upload one with a higher resolution.') ?>
    </div>

    <div class="spacer mb-2">&nbsp;</div>

    <div class="col-12">
            <span class="helptext"
                  title="<?= __('The preview image may be smaller than the final image.') ?>"></span>
        <span><?= __('Position the bars by drag and drop'); ?></span>
    </div>

    <div id="image-cropper" class="image-editor">
        <div class="cropit-preview"></div>
        <div style="clear:both"></div>
    </div>

    <div class="warning-to-much-text alert alert-danger mt-4 mb-0" role="alert">
        <strong><?= __('Warning!') ?></strong>
        <?= __('At least one bar contains to much text.') ?>
    </div>

    <div class="row mt-4">
        <div class="custom-range-slider image-modifier-controls col mr-4">
            <table class="w-100">
                <tr>
                    <td class="align-middle icon-small">
                        <span class="btn-styler fa fa-picture-o decreaser"></span>
                    </td>
                    <td class="align-middle slider">
                        <input type="range" class="cropit-image-zoom-input custom w-100" min="0" max="1" step="0.01">
                    </td>
                    <td class="align-middle icon-big">
                        <span class="btn-styler fa fa-picture-o fa-2x pl-2 increaser"></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="custom-range-slider font-size-controls col mr-4">
            <table class="w-100">
                <tr>
                    <td class="align-middle icon-small">
                        <span class="btn-styler fa fa-font decreaser"></span>
                    </td>
                    <td class="align-middle slider">
                        <input type="range" class="font-size-slider w-100" min="0" max="1" step="0.01">
                    </td>
                    <td class="align-middle icon-big">
                        <span class="btn-styler fa fa-font fa-2x increaser"></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mt-3 mb-2">
        <span class="helptext"
              title="<?= __('Be as short as possible. The system will constraint the number of bars and the text size to suit the corporate design.') ?>"></span>
        <span><?= __('Bar text') ?></span>
    </div>
    <div id="bars-form"></div>
    <button id="add-subline" class="btn btn-outline-secondary"><?= __('Add subline') ?></button>
    <div class="warning-to-much-text alert alert-danger mt-3 mb-0" role="alert">
        <strong><?= __('Warning!') ?></strong>
        <?= __('At least one bar contains to much text.') ?>
    </div>
    <div class="spacer mt-3"></div>
    <div id="copyright-form" class="d-none">
        <span class="helptext"
              title="<?= __('You can add some copyright info to the edge of the image.') ?>"></span>
        <?= $this->Form->input('copyright', [
            'label' => __('Copyright info'),
            'id' => 'copyright',
            'class' => 'form-control mb-3',
        ]); ?>
    </div>
    <span class="helptext"
          title="<?= __('Generally the bars are on the left side. But you may put them on the right side, if the photo requires it.') ?>"></span>
    <?= $this->Form->input('layout', [
        'options' => $layouts,
        'label' => __('Layout'),
        'class' => 'form-control mb-3',
    ]); ?>
    <div id="color-scheme-form" class="d-none">
        <span class="helptext"
              title="<?= __('Default is the green color schema. Use the white color scheme if your image has a green background. Use green head- & sublines if your background is white.') ?>"></span>
        <?= $this->Form->input('color_scheme', [
            'options' => $color_schemes,
            'label' => __('Color scheme'),
            'id' => 'color_scheme',
            'class' => 'form-control mb-3',
        ]); ?>
    </div>
    <span class="helptext"
          title="<?= __('The border is part of the corporate design. You may however omit the border if you use the image in a context where a border looks ugly.') ?>"></span>
    <?= $this->Form->input('border-form', [
        'options' => $border_options,
        'label' => __('Border'),
        'class' => 'form-control mb-4',
    ]); ?>
    <button id="image-generate" class="btn btn-outline-primary mb-3"><?= __('Generate Image') ?></button>

</div>

<div id="generating-image" title="<?= __('Working hard') ?>">
    <?= __("I'm busy generating your image, please hang on a few ticks. I'll be right there.") ?>
    <span id="please-fill-out-legal"><?= __("Please answer meanwhile the legal questions below.") ?></span>
    <img src="/img/ajax-loader.gif" id="generating-image-loader">
    <img src="/img/ajax-loader.gif" id="sending-legal-loader">
    <div id="legal-check" class="mt-3">
        <div id="right_of_personality_wrapper">
            <h3><?= __('Right of personality') ?></h3>

            <div id="people-input">
                <p class="mt-3"><?= __('Are there any identifiably people in the image?') ?></p>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary active">
                        <input type="radio" name="people" id="people-1" autocomplete="off" value="1"
                               checked> <?= __('Yes') ?>
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" name="people" id="people-2" autocomplete="off" value="0"> <?= __('No') ?>
                    </label>
                </div>
            </div>

            <div id="right_of_personality-input">
                <p class="mt-3"><?= __('Are they either people of public interest or have they agreed to the publication of this image?') ?></p>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary">
                        <input type="radio" name="right_of_personality" id="right_of_personality-1" value="1"
                               autocomplete="off"> <?= __('Yes') ?>
                    </label>
                    <label class="btn btn-secondary active">
                        <input type="radio" name="right_of_personality" id="right_of_personality-unclear"
                               value="unclear"
                               autocomplete="off" checked> <?= __('Unclear') ?>
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" name="right_of_personality" id="right_of_personality-2" value="0"
                               autocomplete="off"> <?= __('No') ?>
                    </label>
                </div>
                <div id="right_of_personality-warning" class="alert alert-warning mt-3" role="alert">
                    <strong><?= __('Right of personality:') ?></strong>
                    <?= __("By law, people have to agree on the publication of photos, where they are identifiably.") ?>
                </div>
            </div>
        </div>

        <div id="copyright-wrapper">
            <h3 class="mt-4"><?= __('Copyright') ?></h3>

            <?= $this->Form->input('own_image', [
                'label' => __('Who is the originator of the raw image?'),
                'options' => [
                    0 => '',
                    'me' => __('Me'),
                    'stock' => __('Stock image'),
                    'agency' => __('Agency / Photographer'),
                    'friend' => __('Friend / Member of the party'),
                    'unknown' => __("I don't know / I'm not sure"),
                ],
                'class' => 'form-control',
            ]); ?>
            <div id="stock-image-questions" class="mt-3">
                <?= $this->Form->input('source', [
                    'label' => __('Where is the raw image from?'),
                    'placeholder' => __('Url to the raw image on the stock image platform.'),
                    'class' => 'form-control mb-3',
                ]); ?>
                <?= $this->Form->input('licence', [
                    'label' => __('Select the raw images licence'),
                    'options' => [
                        'other' => __('Other'),
                        'cc+' => __('Creative Commons with Attribution'),
                        'cc' => __('Creative Commons without Limitation')
                    ],
                    'class' => 'form-control mb-3',
                ]); ?>
            </div>
            <div id="photographer-questions" class="mt-3">
                <?= $this->Form->input('originator', [
                    'label' => __('Who is the originator of the raw image?'),
                    'placeholder' => __('Name of the photographer, agency or person who did this artwork'),
                    'class' => 'form-control',
                ]); ?>
            </div>
            <div id="originator-warning" class="alert alert-warning mt-3" role="alert">
                <strong><?= __('Copyright:') ?></strong>
                <?= __("Publishing this image may violate the intellectual property right.") ?>
            </div>
            <div id="usage-questions" class="mt-3">
                <p class="mt-3 mb-2"><?= __('I hereby confirm that:') ?></p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="i_can_use" id="i_can_use" name="i_can_use">
                    <label class="form-check-label" for="i_can_use">
                        <?= __('I have the right to use this raw image') ?>
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="others_can_use" id="others_can_use"
                           name="others_can_use">
                    <label class="form-check-label" for="others_can_use">
                        <?= __('Others can use this raw image without limitation') ?>
                    </label>
                </div>
            </div>
            <button class="btn btn-outline-primary mt-4 mb-3" id="submit-legal">
                <?= __('Confirm legal declarations') ?>
            </button>
        </div>
    </div>
    <div class="warning-image-generation-error d-none alert alert-danger" role="alert">
        <strong><?= __('Warning!') ?></strong>
        <span><?= __('The image could not be generated. Please try again.') ?></span>
    </div>
    <div id="download-button" class="mt-3 mb-2"></div>
</div>
