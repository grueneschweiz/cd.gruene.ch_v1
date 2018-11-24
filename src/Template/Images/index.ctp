<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Image'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Bars'), ['controller' => 'Bars', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Bar'), ['controller' => 'Bars', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="images index large-9 medium-8 columns content">
    <h3><?= __('Images') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th scope="col"><?= $this->Paginator->sort('id') ?></th>
            <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
            <th scope="col"><?= $this->Paginator->sort('opath') ?></th>
            <th scope="col"><?= $this->Paginator->sort('npath') ?></th>
            <th scope="col"><?= $this->Paginator->sort('width') ?></th>
            <th scope="col"><?= $this->Paginator->sort('height') ?></th>
            <th scope="col"><?= $this->Paginator->sort('flattext') ?></th>
            <th scope="col"><?= $this->Paginator->sort('tags') ?></th>
            <th scope="col"><?= $this->Paginator->sort('created') ?></th>
            <th scope="col" class="actions"><?= __('Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($images as $image): ?>
            <tr>
                <td><?= $this->Number->format($image->id) ?></td>
                <td><?= $image->has('user') ? $this->Html->link($image->user->id,
                        ['controller' => 'Users', 'action' => 'view', $image->user->id]) : '' ?></td>
                <td><?= h($image->opath) ?></td>
                <td><?= h($image->npath) ?></td>
                <td><?= $this->Number->format($image->width) ?></td>
                <td><?= $this->Number->format($image->height) ?></td>
                <td><?= h($image->flattext) ?></td>
                <td><?= h($image->tags) ?></td>
                <td><?= h($image->created) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $image->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $image->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $image->id],
                        ['confirm' => __('Are you sure you want to delete # {0}?', $image->id)]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
