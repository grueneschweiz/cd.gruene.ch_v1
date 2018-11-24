<div class="registration-box clean-content-width">
    <div>
        <h1 class="mb-3"><?= __('Register') ?></h1>
        <?= $this->Flash->render() ?>
        <?= $this->Form->create() ?>
        <div class="mb-4">
            <?= $this->Form->input('first_name', [
                'class' => 'form-control mb-3',
                'autocomplete' => 'first_name',
                'label' => __('First name'),
                'required' => true,
            ]) ?>
        </div>
        <div class="mb-4">
            <?= $this->Form->input('last_name', [
                'class' => 'form-control mb-3',
                'autocomplete' => 'last_name',
                'label' => __('Last name'),
                'required' => true,
            ]) ?>
        </div>
        <div class="mb-4">
            <?= $this->Form->input('city', [
                'class' => 'form-control mb-3',
                'autocomplete' => 'city',
                'label' => __('City'),
                'required' => true,
            ]) ?>
        </div>
        <div class="mb-4">
            <?= $this->Form->input('email', [
                'class' => 'form-control mb-3',
                'autocomplete' => 'email',
                'type' => 'email',
                'label' => __('Email'),
                'required' => true,
            ]) ?>
        </div>
        <div class="mb-4">
            <?= $this->Form->input('message', [
                'class' => 'form-control mb-3',
                'autocomplete' => 'email',
                'type' => 'textarea',
                'label' => __('Comment')
            ]) ?>
        </div>
        <?= $this->Flash->render() ?>
        <?= $this->Form->button(__('Apply for login'), [
            'class' => 'btn btn-primary',
        ]) ?>
        <?= $this->Form->end() ?>
    </div>
</div>

<div class="spacer mt-5 mb-2">&nbsp;</div>

<?= $this->element('simple_footer'); ?>