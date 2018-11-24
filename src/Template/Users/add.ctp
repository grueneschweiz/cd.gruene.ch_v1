<?= $this->element('admin_nav'); ?>
<div class="users form large-9 medium-8 columns content col-12 col-md-6">
    <h3 class="mb-3"><?= __('Add User') ?></h3>
    <?= $this->element('User/input', ['add' => true]); ?>
</div>
