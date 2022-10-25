<?php

require __DIR__ . '/../vendor/autoload.php';

class azure_login
{
  static function save_settings($data)
  {
    $settings = (object) [
      'enabled' => $data['enabled'] === 'on',
      'allow_signup' => $data['allow_signup'] === 'on',
      'hide_default' => intval($data['hide_default']),
      'show_before_after' => intval($data['show_before_after']),
      'default_group' => intval($data['default_group']),
      'label' => $data['label'],
      'provider_url' => $data['provider_url'],
      'client_id' => $data['client_id'],
      'client_secret' => $data['client_secret'],
      'scopes' => $data['scopes'],
    ];

    $e_sql = "SELECT count(*) AS count FROM `app_configuration` WHERE `configuration_name` = 'CFG_PLUGIN_AZURE_LOGIN'";
    $e_query = db_query($e_sql);
    $e_info = db_fetch_array($e_query);
    if (!$e_info) {
      return TEXT_PLUGIN_AZURE_LOGIN_SAVE_ERROR;
    }
    if ($e_info['count'] === '0') {
      $sql = "INSERT INTO `app_configuration` (`configuration_name`, `configuration_value`) VALUES ('CFG_PLUGIN_AZURE_LOGIN', '" . db_input(json_encode($settings)) . "')";
    } else {
      $sql = "UPDATE `app_configuration` SET `configuration_value` = '" . json_encode($settings) . "' WHERE `configuration_name` = 'CFG_PLUGIN_AZURE_LOGIN'";
    }

    $e_query = db_query($sql);
    return TEXT_PLUGIN_AZURE_LOGIN_SAVE_SUCCESS;
  }

  static function get_settings()
  {
    $e_sql = "SELECT `configuration_value` AS config FROM `app_configuration` WHERE `configuration_name` = 'CFG_PLUGIN_AZURE_LOGIN'";
    $e_query = db_query($e_sql);
    $e_info = db_fetch_array($e_query);
    if ($e_info && isset($e_info['config'])) {
      try {
        $settings = json_decode($e_info['config']);
        return $settings;
      } catch (Exception $e) {
      }
    }

    return (object) [];
  }

  static function login($claims)
  {
    global $alerts;

    $u_sql = "SELECT * FROM `app_entity_1` WHERE `field_9` = '" . db_input($claims->email) . "'";
    $u_query = db_query($u_sql);
    $u_info = db_fetch_array($u_query);
    if ($u_info) {
      return users::login($u_info['field_12'], null, true, $u_info['password']);
    }
    if (self::get_settings()->allow_signup) {
      return self::register($claims);
    }
    $alerts->add(TEXT_PLUGIN_AZURE_LOGIN_NO_SIGNUP, 'error');
    return redirect_to('users/login');
  }

  static function register($claims)
  {
    $settings = self::get_settings();

    $parts = isset($claims->name) ? explode(' ', $claims->name) : [];
    $fname = count($parts) ? $parts[0] : '';
    $lname = count($parts) > 1 ? $parts[1] : '';

    $username = isset($claims->preferred_username) ? $claims->preferred_username : $claims->email;

    if (strpos($username, '@') > 0) {
      $username = explode('@', $username)[0];
    }

    $sql_data = [
      'date_added' => 0,
      'created_by' => 0,
      'parent_item_id' => 0,
      'field_6' => $settings->default_group,
      'multiple_access_groups' => '',
      'field_5' => 1,
      'is_email_verified' => 1,
      'field_13' => CFG_APP_LANGUAGE,
      'field_7' => db_prepare_input(isset($claims->given_name) ? $claims->given_name : $fname),
      'field_8' => db_prepare_input(isset($claims->family_name) ? $claims->family_name : $lname),
      'field_9' => db_prepare_input($claims->email),
      'field_12' => db_prepare_input($username),
    ];

    db_perform('app_entity_' . 1, $sql_data);
    $item_id = db_insert_id();

    app_session_register('app_logged_users_id', $item_id);

    users_login_log::success($sql_data['field_12'], $item_id);

    return redirect_to('users/account');
  }
}
