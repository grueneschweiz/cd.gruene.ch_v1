<div class="registration-box clean-content-width">
    <div>
        <h1 class="mb-3"><?= __('Register') ?></h1>
        <?= $this->Flash->render() ?>
        <p><?= __('Thank you for your registration. We will check it and send you the login credentials as soon as possible. Please be aware, that this may take a few days.') ?></p>
    </div>
</div>

<div class="spacer mt-5 mb-2">&nbsp;</div>

<?= $this->element('simple_footer'); ?>