<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->fetch('title') ?></title>
    <?= $this->element('favicon') ?>
    <?= $this->Html->css('bundle.css?v=' . filemtime(WWW_ROOT . 'css' . DS . 'bundle.css')) ?>
    <?= $this->Html->script('bundle.js?v=' . filemtime(WWW_ROOT . 'js' . DS . 'bundle.js')) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

</head>