<?php
$nav = [
    ['label' => __('Users'), 'route' => ['controller' => 'Users', 'action' => 'index']],
    ['label' => __('Groups'), 'route' => ['controller' => 'Groups', 'action' => 'index']],
    ['label' => __('Logos'), 'route' => ['controller' => 'Logos', 'action' => 'index']],
];
?>

<ul class="nav nav-tabs pt-3 mb-4">
    <?php foreach ($nav as $item) {
        if (empty($item['admin_only']) || false == $item['admin_only'] || $admin) {
            if (empty($item['route'])) {
                $active = '';
            } else {
                $active = $this->request->controller === $item['route']['controller'] &&
                $this->request->action === $item['route']['action'] ? 'active' : '';
            }
            $addClass = empty($item['class']) ? '' : $item['class'];
            $class = 'nav-link ' . $active . ' ' . $addClass;
            echo '<li class="nav-item">' . $this->Html->link($item['label'], $item['route'],
                    ['class' => $class]) . '</li>';
        }
    } ?>
</ul>