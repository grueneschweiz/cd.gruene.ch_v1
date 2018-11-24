<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $image->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $image->id)]
            )
            ?></li>
        <li><?= $this->Html->link(__('List Images'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Bars'), ['controller' => 'Bars', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Bar'), ['controller' => 'Bars', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="images form large-9 medium-8 columns content">
    <?= $this->Form->create($image) ?>
    <fieldset>
        <legend><?= __('Edit Image') ?></legend>
        <?php
        echo $this->Form->input('user_id', ['options' => $users]);
        echo $this->Form->input('opath');
        echo $this->Form->input('npath');
        echo $this->Form->input('width');
        echo $this->Form->input('height');
        echo $this->Form->input('flattext');
        echo $this->Form->input('tags');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
