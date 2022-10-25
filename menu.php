<?php

if ($app_user['group_id'] === 0) {
  $app_plugin_menu['menu'][] = array(
    'title' => TEXT_PLUGIN_AZURE_LOGIN,
    'url' => url_for('azure_login/azure_login/index'),
    'class' => 'fa-windows'
  );
}
