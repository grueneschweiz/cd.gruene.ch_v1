<?= $this->element('admin_nav'); ?>
<div class="groups view large-9 medium-8 columns content">
    <div class="clearfix mt-3 mb-3">
        <h3 class="float-left mr-3"><?= h($group->name) ?></h3>
        <a href="<?= $this->Url->build(['action' => 'edit', $group->id]) ?>"
           class="btn btn-outline-primary"><?= __('Edit Group') ?></a>
    </div>
    <table class="vertical-table table">
        <tr>
            <th scope="row"><?= __('Parent Group') ?></th>
            <td><?= $group->has('parent') ? $this->Html->link($group->parent->name,
                    ['controller' => 'Groups', 'action' => 'view', $group->parent->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($group->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Added by') ?></th>
            <td><?= $this->ReferencedBy->addedBy($group) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($group->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Last Modified') ?></th>
            <td><?= h($group->modified) ?></td>
        </tr>
    </table>
    <?php if (!empty($group->logos)): ?>
        <div class="related mt-5">
            <h4><?= __('Logos of this group') ?></h4>
            <?= $this->element('Logo/index_table', [
                'logos' => $group->logos,
                'paginator' => false
            ]); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($group->users)): ?>
        <div class="related mt-5">
            <h4><?= __('Users of this group') ?></h4>
            <?= $this->element('User/index_table', [
                'users' => $group->users,
                'paginator' => false
            ]); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($group->direct_children)): ?>
        <div class="related mt-5">
            <h4><?= __('Direct child groups') ?></h4>
            <?= $this->element('Group/index_table', [
                'groups' => $group->direct_children,
                'paginator' => false
            ]); ?>
        </div>
    <?php endif; ?>
</div>
