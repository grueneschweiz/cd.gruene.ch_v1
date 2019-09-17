<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

?>
<!DOCTYPE html>
<html>
<?= $this->element('head') ?>
<body>
<div id="wrapper">
    <header>
        <div class="titlebar-wrapper">
            <h1 class="bg-green"><?= __('salut') ?></h1>
            <div style="clear: both"></div>
            <h1 class="bg-magenta"><?= $this->request->session()->read('Auth.User.first_name') ?? "Let's Log in" ?></h1>
            <div style="clear: both"></div>
        </div>
        <?php if ($this->request->session()->read('Auth.User.id')): ?>
            <nav class="mt-4">
                <?= $this->element('top_nav'); ?>
            </nav>
        <?php endif; ?>
    </header>

    <div class="main clearfix">
        <?= $this->Flash->render() ?>
        <div class="container float-left ml-md-3">
            <?= $this->fetch('content') ?>
        </div>
    </div>

    <footer class="footer mt-5">
        <div class="container ml-md-3">
            <div class="row pt-3 pb-3">
                <div class="col-sm mt-3 mb-3">
                    <h3><?= __('Imprint') ?></h3>
                    <p>Grüne Schweiz | Les Verts suisses
                        <br>Waisenhauspaltz 21
                        <br>3011 Bern
                    </p>
                    <p>
                        <a href="mailto:gruene@gruene.ch">gruene@gruene.ch</a>
                        <br><a href="http://www.gruene.ch">www.gruene.ch</a>
                    </p>
                </div>
                <!--<div class="col-sm mt-3 mb-3">
                    <h3><?= __('Questions or suggestions?') ?></h3>
                    <p><?= __('We are very keen about your feedback. Feel free to ask or suggest anything.') ?></p>
					<?php
                $subject = rawurlencode(__('Feedback on cd.gruene.ch'));
                $body = rawurlencode(__("Hi Cyrill\n\nI just used cd.gruene.ch and id like to say ..."));
                ?>
                    <p><a class="btn btn-secondary"
                          href="mailto:cyrill.bolliger@gruene.ch?subject=<?= $subject ?>&body=<?= $body ?>"><?= __('Ask or suggest') ?></a>
                    </p>
                </div>-->
                <div class="col-sm mt-3 mb-3">
                    <h3><?= __('Under Development') ?></h3>
                    <p><?= __("This tool is still under development and is continuously extended and improved. If you experience any bugs or if you wish to suggest any new features, please let us know.") ?></p>
                    <p><a class="btn btn-secondary"
                          href="mailto:cyrill.bolliger@gruene.ch?subject=<?= $subject ?>&body=<?= $body ?>"><?= __('Contact us') ?></a>
                    </p>
                </div>
                <div class="col-sm mt-3 mb-3">
                    <h3><?= __('Use responsibly') ?></h3>
                    <p><?= __("Every author bears the liability for the images he creates. Make sure you do only use images you are authorized to use and don't forget to give appropriate credits to the originator of the image. If people are visible, they have to agree to the publishing.") ?></p>
                    <p><?= __('Feel free to contact us, if you have any questions about a certain image.') ?></p>
                </div>
                <div class="col-sm mt-3 mb-3">
                    <h3><?= __('Privacy') ?></h3>
                    <p><?= __('Raw images as well as the final images are stored on the server. The final images will be visible for anyone who has a login to this tool. Raw images may be shared, depending on your choices in the legal questionnaire.') ?></p>
                    <p><?= __('Raw images that are intended to be shared, may be sent to googles vision api for image recognition.') ?></p>
                </div>
            </div>
            <div class="row pt-3 pb-3">

            </div>
        </div>
    </footer>
</div>
<script>
    var trans = {
        new_line: '<?= htmlspecialchars(__('New line'), ENT_QUOTES) ?>',
        accent_headline_warning: '<?= htmlspecialchars(__('Two magenta headlines are only allowed, if the message requires it and would make less sense otherwise. Click Cancel to insert a second magenta bar. (You have to Click cancel, beacuse people never read the message but just clicked OK.)'), ENT_QUOTES) ?>',
        logo_loding_error: '<?= htmlspecialchars(__('The logo could not be loaded. Please try again.'), ENT_QUOTES) ?>',
        logo_collision: '<?= htmlspecialchars(__('The bars should never touch the logo. Do you want to move the logo in a other corner?'), ENT_QUOTES) ?>',
        logo_keeps_colliding: '<?= htmlspecialchars(__('The bars should never touch the logo. Please reduce the size of the bars.'), ENT_QUOTES) ?>',
        image_generation_error: '<?= htmlspecialchars(__('Error: The image could not be generated. Please try again.'), ENT_QUOTES) ?>',
        download_image: '<?= htmlspecialchars(__('Download image'), ENT_QUOTES) ?>',
        headline_1: '<?= htmlspecialchars(__('Headline 1'), ENT_QUOTES) ?>',
        headline_2: '<?= htmlspecialchars(__('Headline 2'), ENT_QUOTES) ?>',
        subline_1: '<?= htmlspecialchars(__('Subline 1'), ENT_QUOTES) ?>',
        reload: '<?= htmlspecialchars(__('This will clear all input. Do you want to proceed?'), ENT_QUOTES) ?>',
        invalid: '<?= htmlspecialchars(__('Input not valid:'), ENT_QUOTES) ?>',
        own_image_empty: '<?= htmlspecialchars(__('Please select the originator of the image.'), ENT_QUOTES) ?>',
        originator_empty: '<?= htmlspecialchars(__('Please enter the name of the originator.'), ENT_QUOTES) ?>',
        source_empty: '<?= htmlspecialchars(__('Please enter url of the platform where you got the image from.'), ENT_QUOTES) ?>',
        can_i_use: '<?= htmlspecialchars(__('Please confirm that you have the right to use this image.'), ENT_QUOTES) ?>',
        empty_bars: '<?= htmlspecialchars(__('Input not valid: Please make sure you do not leave any bars empty.'), ENT_QUOTES) ?>',
        delete_image: '<?= htmlspecialchars(__('Are you sure you want to delete this image?'), ENT_QUOTES) ?>',
        copy: '<?= htmlspecialchars(__('© Photo:'), ENT_QUOTES) ?>',
        gradient: '<?= htmlspecialchars(__('Gradient'), ENT_QUOTES) ?>',
        transparent: '<?= htmlspecialchars(__('Transparent'), ENT_QUOTES) ?>'
    };

    var x_csrf_token = '<?= $this->request->params['_csrfToken'] ?>';
    var user = $.parseJSON('<?= json_encode($this->request->session()->read('Auth.User'))?>');
</script>
<?= $this->element('browser_update') ?>
</body>
</html>
