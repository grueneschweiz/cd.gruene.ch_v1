<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Image'), ['action' => 'edit', $image->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Image'), ['action' => 'delete', $image->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $image->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Images'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Image'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Bars'), ['controller' => 'Bars', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Bar'), ['controller' => 'Bars', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="images view large-9 medium-8 columns content">
    <h3><?= h($image->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $image->has('user') ? $this->Html->link($image->user->id,
                    ['controller' => 'Users', 'action' => 'view', $image->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Opath') ?></th>
            <td><?= h($image->opath) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Npath') ?></th>
            <td><?= h($image->npath) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Flattext') ?></th>
            <td><?= h($image->flattext) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Tags') ?></th>
            <td><?= h($image->tags) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($image->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Width') ?></th>
            <td><?= $this->Number->format($image->width) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Height') ?></th>
            <td><?= $this->Number->format($image->height) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($image->created) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Bars') ?></h4>
        <?php if (!empty($image->bars)): ?>
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Image Id') ?></th>
                    <th scope="col"><?= __('Text') ?></th>
                    <th scope="col"><?= __('Position') ?></th>
                    <th scope="col"><?= __('Fontsize') ?></th>
                    <th scope="col"><?= __('Type') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($image->bars as $bars): ?>
                    <tr>
                        <td><?= h($bars->id) ?></td>
                        <td><?= h($bars->image_id) ?></td>
                        <td><?= h($bars->text) ?></td>
                        <td><?= h($bars->position) ?></td>
                        <td><?= h($bars->fontsize) ?></td>
                        <td><?= h($bars->type) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'),
                                ['controller' => 'Bars', 'action' => 'view', $bars->id]) ?>
                            <?= $this->Html->link(__('Edit'),
                                ['controller' => 'Bars', 'action' => 'edit', $bars->id]) ?>
                            <?= $this->Form->postLink(__('Delete'),
                                ['controller' => 'Bars', 'action' => 'delete', $bars->id],
                                ['confirm' => __('Are you sure you want to delete # {0}?', $bars->id)]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>