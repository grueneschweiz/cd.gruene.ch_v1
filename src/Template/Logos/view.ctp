<?= $this->element('admin_nav'); ?>
<div class="logos view large-9 medium-8 columns content">
    <div class="clearfix mt-3 mb-3">
        <h3 class="float-left mr-3"><?= h($logo->name) ?></h3>
        <a href="<?= $this->Url->build(['action' => 'edit', $logo->id]) ?>"
           class="btn btn-outline-primary"><?= __('Edit Logo') ?></a>
    </div>
    <table class="vertical-table table">
        <tr>
            <th scope="row"><?= __('Base logo') ?></th>
            <td><?= h($logo->top_path) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Subline') ?></th>
            <td><?= h($logo->subline) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($logo->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Added by') ?></th>
            <td><?= empty($logo->user) ? '#' . __('deleted') : h($logo->user->full_name_email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($logo->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Last Modified') ?></th>
            <td><?= h($logo->modified) ?></td>
        </tr>
    </table>
    <?php if (!empty($groups)): ?>
        <div class="related mt-5">
            <h4><?= __('Groups using this logo') ?></h4>
            <?= $this->element('Group/index_table', [
                'groups' => $groups,
                'paginator' => false,
                'hierarchical' => false,
            ]); ?>
        </div>
    <?php endif; ?>
</div>
