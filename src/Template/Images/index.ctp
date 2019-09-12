<?php

use Cake\I18n\Time;

?>
</div>
<div class="images index ml-md-3 mr-md-3 container-fluid">
    <div class="mt-4 mb-3">
        <div class="clearfix mb-2">
            <h3 class="float-left mr-3"><?= __( 'Gallery' ) ?></h3>
            <a href="<?= $this->Url->build( [ 'action' => 'add' ] ) ?>"
               class="btn btn-outline-primary"><?= __( 'Create Image' ) ?></a>
        </div>
        <p><?= __( 'Have a look at the images your fellows have created.' ) ?></p>
    </div>

    <div class="input-group mb-3 col-12 col-lg-6">
        <input id="gallery-search" type="text" class="form-control" placeholder="<?= __('Search title bars text, name of the creator or the logo subline') ?>">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" id="gallery-search-submit"><?= __('Search') ?></button>
        </div>
    </div>

    <div class="gallery">
        <?php
        $user_id     = $this->request->session()->read( 'Auth.User.id' );
        $super_admin = $this->request->session()->read( 'Auth.User.super_admin' );
        ?>
        <?php foreach ( $images as $image ): ?>
            <?php
            if ( ! ( $image->src && $image->thumbSrc ) ) {
                continue;
            }

            $time    = new Time( $image->created );
            $created = $time->timeAgoInWords( [
                'end'      => '2 days',
                'accuracy' => [ 'hour' => 'hour', 'day' => 'day' ],
            ] );
            $by      = '<a href="mailto:' . $image->user->email . '">' . $image->user->first_name . ' ' . $image->user->last_name . '</a>';
            ?>
            <div class="gallery-card">
                <img src="<?= $image->thumbSrc ?>" alt="<?= $image->flattext ?>" class="gallery-image">
                <div class="caption gallery-image-caption d-none">
                    <p><?= __( 'Created {time_ago} by {user_name}.', [
                            'time_ago'  => $created,
                            'user_name' => $by
                        ] ) ?></p>
                    <a href="<?= $image->src ?>" download="download"
                       class="btn btn-outline-primary btn-sm"><?= __( 'Download' ) ?></a>
                    <?php if ( $image->user->id === $user_id || $super_admin ): ?>
                        <button class="btn btn-link btn-sm image-delete-button" data-image-id="<?= $image->id ?>"><?= __( 'Delete' ) ?></button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ( $this->Paginator->hasPage( null, 2 ) ) {
        echo $this->element( 'paginator' );
    } ?>
