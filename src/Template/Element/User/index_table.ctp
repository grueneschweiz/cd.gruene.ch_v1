<table cellpadding="0" cellspacing="0" class="table table-hover">
    <thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('first_name') ?></th>
        <th scope="col"><?= $this->Paginator->sort('last_name') ?></th>
        <th scope="col"><?= $this->Paginator->sort('email') ?></th>
        <th scope="col"><?= __('Managed by') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= h($user->first_name) ?></td>
            <td><?= h($user->last_name) ?></td>
            <td><?= h($user->email) ?></td>
            <td><?= $this->ReferencedBy->managedBy($user) ?></td>
            </td>
            <td class="actions norint">
                <?= $this->Html->link('<i class="fa fa-eye view-icon" aria-hidden="true"></i>',
                    ['controller' => 'Users', 'action' => 'view', $user->id],
                    ['escapeTitle' => false, 'alt' => __('View')]) ?>
                <?= $this->Html->link('<i class="fa fa-pencil edit-icon" aria-hidden="true"></i>',
                    ['controller' => 'Users', 'action' => 'edit', $user->id],
                    ['escapeTitle' => false, 'alt' => __('Edit')]) ?>
                <?= $this->Form->postLink('<i class="fa fa-trash-o delete-icon" aria-hidden="true"></i>',
                    ['controller' => 'Users', 'action' => 'delete', $user->id], [
                        'escapeTitle' => false,
                        'alt' => __('Delete'),
                        'confirm' => __('Are you sure you want to delete "{0} {1}"?', $user->first_name,
                            $user->last_name)
                    ]) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php if (!isset($paginator) || $paginator) {
    echo $this->element('paginator');
} ?>