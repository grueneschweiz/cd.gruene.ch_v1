<?php $add = isset( $add ) ? $add : false; ?>

<?= $this->Form->create( $user ) ?>
<fieldset>
    <?php
    echo '<div class="mb-4">';
    echo $this->Form->input( 'first_name', [
        'class'    => 'form-control',
        'required' => true,
    ] );
    echo '</div>';
    echo '<div class="mb-4">';
    echo $this->Form->input( 'last_name', [
        'class'    => 'form-control',
        'required' => true,
    ] );
    echo '</div>';
    echo '<div class="mb-4">';
    echo $this->Form->input( 'email', [
        'class' => 'form-control',
    ] );
    echo '</div>';
    echo $this->element( 'User/change_password_field', [ 'mode' => $add ? 'add' : 'edit' ] );
    if ( $super_admin ) {
        echo '<div class="mb-4">';
        echo $this->Form->input( 'super_admin', [
            'options' => [ 1 => __( 'Yes' ), 0 => __( 'No' ) ],
            'default' => 0,
            'label'   => __( 'Super Admin' ),
            'class'   => 'form-control',
        ] );
        echo '</div>';
    }
    echo '<div class="mb-4">';
    echo $this->Form->input( 'lang', [
        'options' => [
            LANG_DE => __( 'German' ),
            LANG_FR => __( 'French' ),
            LANG_EN => __( 'English' ),
        ],
        'class'   => 'chosen form-control form-control-chosen',
        'label'   => __( 'Language' ),
    ] );
    echo '</div>';

    if ( $admin || $super_admin ) {
        echo '<div class="mb-4">';
        echo $this->Form->input( 'managed_by_group_id',
            [
                'options' => $groups,
                'empty'   => false,
                'label'   => __( 'The following group is responsible for managing this user' ),
                'class'   => 'chosen form-control form-control-chosen',
            ] );
        echo '</div>';
        echo '<div class="mb-4">';
        echo $this->Form->input( 'groups._ids',
            [
                'options' => $removableLogoGroups,
                'empty'   => false,
                'label'   => __( 'This user can use the logos of the following {strong}groups{close_strong}', [
                    'strong'       => '<strong>',
                    'close_strong' => '</strong>',
                ] ),
                'escape'  => false,
                'class'   => 'chosen form-control form-control-chosen',
            ] );
        if ( $nonRemovableGroups ) {
            echo '<small class="form-text text-muted">';
            echo __( 'This user can also use the logos of {groups}. Ask the admins of those groups to change that.',
                [ 'groups' => '<strong>' . implode( ', ', $nonRemovableGroups ) . '</strong>' ] );
            echo '</small>';
        }
        if ( $inheritedLogoGroups ) {
            echo '<small class="form-text text-muted">';
            echo __( 'This user can also use the logos of {groups}. Revoke his admin privileges for those groups, to change that',
                [ 'groups' => '<strong>' . implode( ', ', $inheritedLogoGroups ) . '</strong>' ] );
            echo '</small>';
        }
        echo '</div>';
        echo '<div class="mb-4">';
        echo $this->Form->label( 'admin_groups', __( 'This user is admin of the following groups' ) );
        echo $this->Form->select( 'admin_groups', $groups,
            [
                'empty'    => false,
                'multiple' => true,
                'class'    => 'chosen form-control form-control-chosen',
                'val'      => $adminGroups,
            ] );
        if ( $nonRemovableAdminGroups ) {
            echo '<small class="form-text text-muted">';
            echo __( 'This user is also admin of {groups}. Ask him or the other admins of those groups to change that.',
                [ 'groups' => '<strong>' . implode( ', ', $nonRemovableAdminGroups ) . '</strong>' ] );
            echo '</small>';
        }
        if ( $inheritedAdminGroups ) {
            echo '<small class="form-text text-muted">';
            echo __( 'This user inherited admin privileges of {groups}. Remove the corresponding parent group to change that.',
                [ 'groups' => '<strong>' . implode( ', ', $inheritedAdminGroups ) . '</strong>' ] );
            echo '</small>';
        }
        echo '</div>';
        echo '<div class="mb-4 form-check">';
        echo $this->Form->checkbox( 'notify', [
            'class'   => 'form-check-input',
            'checked' => (bool) $add,
            'id'      => 'notify',
        ] );
        $notifyLabel = $add ? __( 'Notify the user by email and send him a link to set a password.' ) : __( 'Resend user invitation' );
        echo $this->Form->label( 'notify', $notifyLabel,
            [
                'class' => 'form-check-label',
            ] );
        echo '</div>';
    }
    ?>

</fieldset>
<?= $this->Form->button( __( 'Save User' ), [ 'class' => 'btn btn-outline-primary mb-4' ] ) ?>
<?= $this->Form->end() ?>
