<?php
$nav = [
    [
        'label' => __('Create Image'),
        'route' => ['controller' => 'Images', 'action' => 'add'],
        'active' => ['Images.add'],
    ],
    /*[
        'label' => __( 'Find Images' ),
        'route' => [],
        'class' => 'disabled'
    ],
    [
        'label' => __( 'Get Inspiration' ),
        'route' => [],
        'class' => 'disabled'
    ],*/
    [
        'label' => __('Admin'),
        'route' => ['controller' => 'Users', 'action' => 'index'],
        'admin_only' => true,
        'active' => ['Groups.*', 'Logos.*', 'UsersGroups.*', 'Users.index', 'Users.add', 'Users.edit'],
    ],
    [
        'label' => __('My Account'),
        'route' => ['controller' => 'Users', 'action' => 'edit', $this->request->session()->read('Auth.User.id')],
    ],
    [
        'label' => __('Logout'),
        'route' => ['controller' => 'Users', 'action' => 'logout'],
    ],
];
?>


<ul class="nav nav-tabs">
    <?php foreach ($nav as $item) {
        if (empty($item['admin_only']) || false == $item['admin_only'] || $admin) {
            $active = '';
            if (!isset($item['active'])) {
                $active = !empty($item['route'])
                    && $this->request->controller === $item['route']['controller']
                    && $this->request->action === $item['route']['action'];
            } else {
                foreach ($item['active'] as $path) {
                    if (0 === strpos($path, $this->request->controller)) {
                        $active = $path === $this->request->controller . '.*' || $path === $this->request->controller . '.' . $this->request->action;
                        if ($active) {
                            break;
                        }
                    }
                }
            }

            $active = $active ? 'active' : '';
            $addClass = empty($item['class']) ? '' : $item['class'];
            $class = 'nav-link ' . $active . ' ' . $addClass;
            echo '<li class="nav-item">' . $this->Html->link($item['label'], $item['route'],
                    ['class' => $class]) . '</li>';
        }
    } ?>
</ul>