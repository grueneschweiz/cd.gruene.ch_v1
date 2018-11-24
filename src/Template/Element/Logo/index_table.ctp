<table cellpadding="0" cellspacing="0" class="table table-hover">
    <thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('name') ?></th>
        <th scope="col"><?= $this->Paginator->sort('subline', __('Subline Text')) ?></th>
        <th scope="col"><?= __('Base Logo') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($logos as $logo): ?>
        <tr>
            <td><?= h($logo->name) ?></td>
            <td><?= h($logo->subline) ?></td>
            <td><img class="index-logo-preview" src="<?= h($logo->top_path) ?>"></td>
            <td class="actions norint">
                <?= $this->Html->link('<i class="fa fa-eye view-icon" aria-hidden="true"></i>',
                    ['controller' => 'Logos', 'action' => 'view', $logo->id],
                    ['escapeTitle' => false, 'alt' => __('View')]) ?>
                <?= $this->Html->link('<i class="fa fa-pencil edit-icon" aria-hidden="true"></i>',
                    ['controller' => 'Logos', 'action' => 'edit', $logo->id],
                    ['escapeTitle' => false, 'alt' => __('Edit')]) ?>
                <?= $this->Form->postLink('<i class="fa fa-trash-o delete-icon" aria-hidden="true"></i>',
                    ['controller' => 'Logos', 'action' => 'delete', $logo->id], [
                        'escapeTitle' => false,
                        'alt' => __('Delete'),
                        'confirm' => __('Are you sure you want to delete "{0}"?', $logo->name)
                    ]) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php if (!isset($paginator) || $paginator) {
    echo $this->element('paginator');
} ?>