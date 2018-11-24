<?= $this->Form->create($group) ?>
    <fieldset>
        <?php
        echo '<div class="mb-4">';
        echo $this->Form->input('name', [
            'class' => 'form-control',
        ]);
        echo '</div>';
        echo '<div class="mb-4">';
        echo $this->Form->input('parent_id',
            [
                'options' => $parentGroups,
                'empty' => true,
                'required' => false,
                'label' => __('This group is a subgroup of:'),
                'class' => 'chosen form-control form-control-chosen',
            ]);
        echo '</div>';
        echo '<div class="mb-4">';
        echo $this->Form->input('logos._ids',
            [
                'options' => $logos,
                'class' => 'chosen form-control form-control-chosen',
                'label' => __('Members of this group can use the folowing logos:'),
            ]);
        echo '</div>';
        echo '<div class="mb-4">';
        echo $this->Form->input('users._ids',
            [
                'options' => $users,
                'class' => 'chosen form-control form-control-chosen',
                'label' => __('The members of this group are:'),
            ]);
        echo '</div>';
        ?>
    </fieldset>
<?= $this->Form->button(__('Save Group'), ['class' => 'btn btn-outline-primary mb-4']) ?>
<?= $this->Form->end() ?>