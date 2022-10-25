<?php

switch ($app_module_action) {
  case 'save_settings':
    $message = azure_login::save_settings($_POST);
    $alerts->add(db_prepare_input($message));
    redirect_to('azure_login/azure_login/index');
    break;
}
