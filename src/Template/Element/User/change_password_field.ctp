<?php

switch ($mode) {
    case 'add':
        $placeholder = __('Enter password');
        $empty_hint = __('If you leave the password field blank, a randomly generated password will be used.');
        $pattern = '.{0}|';
        break;
    case 'forceChange':
        $placeholder = __('Enter new password');
        $empty_hint = '';
        $pattern = '';
        break;
    default:
        $placeholder = __('Leave blank to keep password unchanged');
        $empty_hint = __('If you leave the password field blank, it will remain as it is.');
        $pattern = '.{0}|';
        break;
}

?>

<div class="mb-4" id="password-wrapper">
    <?= $this->Form->label('password'); ?>
    <div class="input-group">
        <?= $this->Form->password('password', [
            'class' => 'form-control',
            'value' => '',
            'placeholder' => $placeholder,
            'pattern' => $pattern . '^(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$',
            'title' => __('The password must be at least 8 characters long and contain one lowercase character, one uppercase character and one digit.'),
            'required' => $mode === 'forceChange',
        ]); ?>
        <div class="input-group-append">
            <button class="btn btn-outline-primary password-show-switch"><i class="fa fa-eye"></i></button>
        </div>
    </div>
    <small class="form-text text-muted">
        <?= __('The password must be at least 8 characters long and contain at least:') ?>
        <ul>
            <li><?= __x('password rule', 'one lowercase character') ?></li>
            <li><?= __x('password rule', 'one uppercase character') ?></li>
            <li><?= __x('password rule', 'one digit') ?></li>
        </ul>
        <strong><?= $empty_hint ?></strong>
    </small>
</div>

<script>
    $('.password-show-switch').click(function (e) {
        e.preventDefault();
        var $icon = $(this).find('i').first(),
            $field = $(this).parent().parent().find('input[name="password"]');
        if ($icon.hasClass('fa-eye-slash')) {
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            $field.attr('type', 'password').focus();
        } else {
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            $field.attr('type', 'text').focus();
        }
    });
</script>