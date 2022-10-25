<?php

if ($app_user['group_id'] > 0) {
  redirect_to('dashboard/access_forbidden');
}

$azure_login_settings = azure_login::get_settings();
