<?= $this->element('admin_nav'); ?>
<div class="users view large-9 medium-8 columns content">
    <div class="clearfix mt-3 mb-3">
        <h3 class="float-left mr-3"><?= h($user->full_name_email) ?></h3>
        <a href="<?= $this->Url->build(['action' => 'edit', $user->id]) ?>"
           class="btn btn-outline-primary"><?= __('Edit User') ?></a>
    </div>
    <table class="vertical-table table">
        <tr>
            <th scope="row"><?= __('First Name') ?></th>
            <td><?= h($user->first_name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Last Name') ?></th>
            <td><?= h($user->last_name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Email') ?></th>
            <td><?= h($user->email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Language') ?></th>
            <td><?= h($user->lang) ?></td>
        </tr>
        <?php if ($super_admin): ?>
            <tr>
                <th scope="row"><?= __('Super Admin') ?></th>
                <td><?= $user->super_admin ? __('Yes') : __('No') ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <th scope="row"><?= __('Managed by') ?></th>
            <td><?= $this->ReferencedBy->managedBy($user) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Added by') ?></th>
            <td><?= $this->ReferencedBy->addedBy($user) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Image count') ?></th>
            <td><?= h($image_count) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Login count') ?></th>
            <td><?= h($user->login_count) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Last login') ?></th>
            <td><?= h($user->last_login) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($user->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Last Modified') ?></th>
            <td><?= h($user->modified) ?></td>
        </tr>
    </table>
    <?php if (!empty($user->groups)): ?>
        <div class="related mt-5">
            <h4><?= __x('User is member of {list of groups}', '{user} is member of',
                    ['user' => $user->first_name]) ?></h4>
            <?= $this->element('Group/index_table', [
                'groups' => $user->groups,
                'paginator' => false,
                'hierarchical' => false,
            ]); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($user->manageable_groups)): ?>
        <div class="related mt-5">
            <h4><?= __x('User can manage {list of groups}', '{user} can manage',
                    ['user' => $user->first_name]) ?></h4>
            <p><?= __('and of their subordinated groups') ?></p>
            <?= $this->element('Group/index_table', [
                'groups' => $user->manageable_groups,
                'paginator' => false,
                'hierarchical' => false,
            ]); ?>
        </div>
    <?php endif; ?>
</div>
