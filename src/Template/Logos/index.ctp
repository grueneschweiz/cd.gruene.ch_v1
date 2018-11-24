<?= $this->element('admin_nav'); ?>
<div class="logos index large-9 medium-8 columns content">
    <div class="clearfix mt-3 mb-3">
        <h3 class="float-left mr-3"><?= __('Logos') ?></h3>
        <a href="<?= $this->Url->build(['action' => 'add']) ?>"
           class="btn btn-outline-primary"><?= __('Add Logo') ?></a>
    </div>
    <?= $this->element('Logo/index_table'); ?>
</div>
