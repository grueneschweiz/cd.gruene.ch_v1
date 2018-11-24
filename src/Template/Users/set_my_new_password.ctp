<div class="login-box clean-content-width">
    <div>
        <h1 class="mb-3"><?= __('Set new password') ?></h1>
        <?= $this->Flash->render() ?>
        <?= $this->Form->create() ?>
        <?= $this->element('User/change_password_field', ['mode' => 'forceChange']); ?>
        <?= $this->Form->button(__('Save new password'), [
            'class' => 'btn btn-primary',
            'id' => 'login-submit'
        ]) ?>
        <?= $this->Form->end() ?>
    </div>
</div>

<?= $this->element('simple_footer'); ?>
<?= $this->element('User/login_box'); ?>
