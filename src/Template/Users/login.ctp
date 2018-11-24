<div class="login-box clean-content-width">
    <div>
        <h1 class="mb-3"><?= __('Log in') ?></h1>
        <?= $this->Flash->render() ?>
        <?= $this->Form->create() ?>
        <div id="new-password-sent" class="alert alert-success d-none" role="alert">
            <?= __("We've sent you an email with a link to set a new password. Don't forget to check also the junk mail folder.") ?>
        </div>
        <div id="new-password-error" class="alert alert-danger d-none" role="alert">
            <?= __("Uuups, something went wrong. Please try again later.") ?>
        </div>
        <div class="mb-4">
            <?= $this->Form->input('email', [
                'class' => 'form-control mb-3',
                'autocomplete' => 'email',
                'label' => __('Email'),
                'autofocus' => true,
            ]) ?>
            <small id="email-not-found" class="form-text text-danger d-none">
                <?= __("Email address not found. Might your login be registered to an other email address?") ?>
            </small>
        </div>
        <div class="mb-4" id="password-wrapper">
            <?= $this->Form->input('password', [
                'class' => 'form-control',
                'autocomplete' => 'current-password',
                'label' => __('Password')
            ]) ?>
            <small class="form-text text-muted">
                <?= __('Forgot password?') ?>
                <a href="#" class="forgot-password-link" tabindex="10"><?= __x('Password', 'Reset it!') ?></a>
            </small>
        </div>
        <div class="form-group mb-4" id="rememberme-wrapper">
            <div class="form-check">
                <?= $this->Form->checkbox('rememberme', [
                    'class' => 'form-check-input',
                    'id' => 'rememberme',
                ]) ?>
                <?= $this->Form->label('rememberme', __('Stay logged in'), [
                    'class' => 'form-check-label',
                    'for' => 'rememberme',
                ]) ?>
            </div>
        </div>
        <?= $this->Form->button(__('Log in'), [
            'class' => 'btn btn-primary',
            'id' => 'login-submit'
        ]) ?>
        <?= $this->Html->link(
            __('Register'),
            ['controller' => 'Users', 'action' => 'register'],
            ['class' => 'btn btn-outline-primary', 'id' => 'register']
        ); ?>
        <?= $this->Form->end() ?>
        <button class="btn btn-primary d-none"
                href="" id="forgot-password-submit"><?= __('Get password reset link') ?></button>
    </div>
</div>

<?= $this->element('simple_footer'); ?>

<?= $this->element('User/login_box'); ?>
<script>
    (function ($) {
        $('.forgot-password-link').click(function () {
            $('.alert').addClass('d-none');
            $('#password-wrapper, #login-submit, #register, #rememberme-wrapper').hide();
            $('#forgot-password-submit').removeClass('d-none');
        });

        $('#forgot-password-submit').click(function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '<?= $this->Url->build(['controller' => 'Users', 'action' => 'forgot-password']) ?>',
                data: {email: $('#email').val()},
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', '<?= $this->request->params['_csrfToken'] ?>');
                    $('#email-not-found').addClass('d-none');
                    $('#new-password-sent').addClass('d-none');
                    $('#forgot-password-submit').addClass('disabled');
                }
            })
                .done(function (resp) {
                    if (resp === 'true') {
                        $('#password-wrapper, #login-submit, #register, #rememberme-wrapper').show();
                        $('#forgot-password-submit').addClass('d-none');
                        $('#new-password-sent').removeClass('d-none');
                        $('#password').val('');
                    } else {
                        $('#email-not-found').removeClass('d-none');
                    }
                })
                .fail(function () {
                    $('#new-password-error').addClass('d-none');
                })
                .always(function () {
                    $('#forgot-password-submit').removeClass('disabled');
                });
        });
    })($);
</script>