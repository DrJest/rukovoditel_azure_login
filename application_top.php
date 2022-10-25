<?php

if ($_SERVER['REQUEST_URI'] !== '/index.php?module=users/login') return;

require('plugins/azure_login/classes/azure_login.php');

$settings = azure_login::get_settings();

if (!$settings->enabled) return;

?>
<style>
  .azure-login-btn,
  .toggle-credentials {
    display: block;
    text-align: center;
    margin: 16px auto !important;
  }

  <?php
  if ($settings->hide_default > 0) {
  ?>#login_form,
  .forget-password {
    display: none;
  }

  <?php
  }
  ?>
</style>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const mode = <?php echo $settings->show_before_after; ?>;
    const show_button = <?php echo $settings->hide_default === 1 ? 'true' : 'false'; ?>;
    const label = '<?php echo $settings->label; ?>';
    const btn = $('<button>')
      .addClass('btn btn-default btn-block azure-login-btn')
      .html(`<i class="fa fa-windows"></i>&nbsp;&nbsp;${label}`);
    btn.on('click', function(e) {
      e.preventDefault();
      location.href = '<?php echo url_for('azure_login/public/index'); ?>';
    });
    if (mode === 0 || show_button) {
      $(btn).insertBefore('#login_form');
    } else {
      $(btn).insertAfter('#login_form');
    }
    if (show_button) {
      $('<a>')
        .attr('href', '#')
        .addClass('toggle-credentials')
        .html('<small><?php echo TEXT_PLUGIN_AZURE_LOGIN_CREDENTIALS; ?></small>')
        .click(function(e) {
          e.preventDefault();
          $('#login_form, .forget-password').slideToggle();
        })
        .insertAfter(btn);
    }
  });
</script>