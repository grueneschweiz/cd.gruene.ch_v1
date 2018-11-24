<?= $this->element('admin_nav'); ?>
<div class="users index large-9 medium-8 columns content">
    <div class="clearfix mt-3 mb-3">
        <h3 class="float-left mr-3"><?= __('Users') ?></h3>
        <a href="<?= $this->Url->build(['action' => 'add']) ?>"
           class="btn btn-outline-primary"><?= __('Add User') ?></a>
    </div>
    <?= $this->element('User/index_table'); ?>
</div>