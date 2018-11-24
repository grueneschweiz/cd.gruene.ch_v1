<?php if ($admin) {
    echo $this->element('admin_nav');
} else {
    echo '<div class="mt-4"></div>';
}
?>
<div class="users form large-9 medium-8 columns content col-12 col-md-6">
    <div class="clearfix mt-3 mb-3">
        <h3 class="float-left mr-3"><?= __('Edit User') ?></h3>
        <a href="<?= $this->Url->build(['action' => 'logout-everywhere', $user->id]) ?>"
           class="btn btn-outline-primary"><?= __('Logout on all devices') ?></a>
    </div>
    <?= $this->element('User/input'); ?>
</div>
