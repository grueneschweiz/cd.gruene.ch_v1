<table cellpadding="0" cellspacing="0" class="table table-hover">
    <thead>
    <tr>
        <th scope="col"><?= __('Name') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($groups as $id => $name): ?>
        <?php if ($name instanceof \App\Model\Entity\Group) {
            $id = $name->id;
            $name = $name->name;
        } ?>
        <tr>
            <td><?= $name ?></td>
            <td class="actions norint">
                <?= $this->Html->link('<i class="fa fa-eye view-icon" aria-hidden="true"></i>',
                    ['controller' => 'Groups', 'action' => 'view', $id],
                    ['escapeTitle' => false, 'alt' => __('View')]) ?>
                <?= $this->Html->link('<i class="fa fa-pencil edit-icon" aria-hidden="true"></i>',
                    ['controller' => 'Groups', 'action' => 'edit', $id],
                    ['escapeTitle' => false, 'alt' => __('Edit')]) ?>
                <?= $this->Form->postLink('<i class="fa fa-trash-o delete-icon" aria-hidden="true"></i>',
                    ['controller' => 'Groups', 'action' => 'delete', $id], [
                        'escapeTitle' => false,
                        'alt' => __('Delete'),
                        'confirm' => __('Are you sure you want to delete "{0}"?', $name)
                    ]) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php if (!isset($paginator) || $paginator) {
    echo $this->element('paginator');
} ?>