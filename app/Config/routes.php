<?php
Router::connect('/', array('controller' => 'Dashboards', 'action' => 'Index'));
Router::connect('/UserGroups/*', array('controller' => 'AdminGroups'));
require CAKE . 'Config' . DS . 'routes.php';
