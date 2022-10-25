<?php
if (isset($app_user)) {
  return redirect_to('dashboard/');
}

$settings = azure_login::get_settings();

$oidc = new \Jumbojett\OpenIDConnectClient(
  $settings->provider_url,
  $settings->client_id,
  $settings->client_secret
);

$oidc->setRedirectURL(url_for('azure_login/public/index', '', true));
$oidc->addScope('openid');
$oidc->addScope('email');
$oidc->addScope('profile');

try {
  $oidc->authenticate();
  $data = $oidc->getVerifiedClaims();
  azure_login::login($data);
} catch (Exception $e) {
  $alerts->add(TEXT_PLUGIN_AZURE_LOGIN_ERROR, 'error');
  return redirect_to('/');
}

exit();
