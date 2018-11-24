<?= $this->Form->create($logo) ?>
    <fieldset>
        <?php
        echo '<div class="mb-4">';
        echo $this->Form->input('name', [
            'class' => 'form-control',
            'placeholder' => __('Ex. gruene.ch / verts.ch'),
        ]);
        echo '</div>';
        echo '<div class="mb-4">';
        echo $this->Form->input('top_path',
            [
                'options' => $top_paths,
                'class' => 'chosen form-control form-control-chosen',
                'label' => __('Base logo:'),
            ]);
        echo '</div>';
        echo '<div class="mb-4">';
        echo $this->Form->input('subline', [
            'class' => 'form-control',
            'label' => __('Subline Text'),
            'placeholder' => __('Ex. gruene.ch / verts.ch'),
        ]);
        echo '</div>';
        ?>
    </fieldset>
<?= $this->Form->button(__('Save Logo'), ['class' => 'btn btn-outline-primary mb-4']) ?>
<?= $this->Form->end() ?>