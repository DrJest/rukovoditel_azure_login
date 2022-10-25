<link rel="stylesheet" href="<?php echo url_for_file('plugins/azure_login/assets/css/admin.css'); ?>">
<h2><?php echo TEXT_PLUGIN_AZURE_LOGIN_SETTINGS; ?></h2>
<div id="azure_login_messages"></div>
<div class="px-4">
  <?php
  echo form_tag(
    'azure_login_form',
    url_for('azure_login/azure_login/index', 'action=save_settings'),
    array('class' => 'form-horizontal')
  );
  ?>
  <div class="form-group">
    <label class="col-xs-4"></label>
    <div class="col-xs-8">
      <label class="checkbox-inline">
        <input type="checkbox" name="enabled">
        <?php echo TEXT_PLUGIN_AZURE_LOGIN_ENABLED; ?>
      </label>
    </div>
  </div>
  <div class="form-group" data-if-enabled>
    <label class="col-xs-4"></label>
    <div class="col-xs-8">
      <label class="checkbox-inline">
        <input type="checkbox" name="allow_signup">
        <?php echo TEXT_PLUGIN_AZURE_LOGIN_ALLOW_SIGNUP; ?>
      </label>
    </div>
  </div>
  <div class="form-group" data-if-enabled>
    <div data-if-allow_signup>
      <label for="default_group" class="control-label col-xs-4"><?php echo TEXT_PLUGIN_AZURE_LOGIN_DEFAULT_GROUP; ?></label>
      <div class="col-xs-8">
        <select id="default_group" name="default_group" class="select form-control">
          <?php foreach (access_groups::get_choices() as $id => $label) : ?>
            <option value="<?php echo $id; ?>"><?php echo $label; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>
  <div class="form-group" data-if-enabled>
    <label for="hide_default" class="control-label col-xs-4"><?php echo TEXT_PLUGIN_AZURE_LOGIN_HIDE_DEFAULT; ?></label>
    <div class="col-xs-8">
      <select id="hide_default" name="hide_default" class="select form-control">
        <option value="0"><?php echo TEXT_PLUGIN_AZURE_LOGIN_HIDE_DEFAULT_SHOW; ?></option>
        <option value="1"><?php echo TEXT_PLUGIN_AZURE_LOGIN_HIDE_DEFAULT_SBTN; ?></option>
        <option value="2"><?php echo TEXT_PLUGIN_AZURE_LOGIN_HIDE_DEFAULT_HIDE; ?></option>
      </select>
    </div>
  </div>
  <div class="form-group" data-if-enabled>
    <label for="show_before_after" class="control-label col-xs-4"><?php echo TEXT_PLUGIN_AZURE_LOGIN_SHOW_BEFORE_AFTER; ?></label>
    <div class="col-xs-8">
      <select id="show_before_after" name="show_before_after" class="select form-control">
        <option value="0"><?php echo TEXT_PLUGIN_AZURE_LOGIN_SHOW_BEFORE; ?></option>
        <option value="1"><?php echo TEXT_PLUGIN_AZURE_LOGIN_SHOW_AFTER; ?></option>
      </select>
    </div>
  </div>
  <div class="form-group" data-if-enabled>
    <label for="redirect_uri" class="control-label col-xs-4"><?php echo TEXT_PLUGIN_AZURE_LOGIN_REDIRECT_URI; ?></label>
    <div class="col-xs-8">
      <div class="input-group">
        <input readonly value="<?php echo url_for('azure_login/public/index', '', true); ?>" id="redirect_uri" type="text" class="form-control">
        <div class="input-group-addon c-pointer" id="copy-redirect-url">
          <i class="fa fa-copy"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group" data-if-enabled>
    <label for="label" class="control-label col-xs-4"><?php echo TEXT_PLUGIN_AZURE_LOGIN_LABEL; ?>*</label>
    <div class="col-xs-8">
      <input id="label" name="label" type="text" class="form-control" required="required">
    </div>
  </div>
  <div class="form-group" data-if-enabled>
    <label for="provider_url" class="control-label col-xs-4"><?php echo TEXT_PLUGIN_AZURE_LOGIN_PROVIDER_URL; ?>*</label>
    <div class="col-xs-8">
      <input id="provider_url" name="provider_url" type="text" class="form-control" required="required">
    </div>
  </div>
  <div class="form-group" data-if-enabled>
    <label for="client_id" class="control-label col-xs-4"><?php echo TEXT_PLUGIN_AZURE_LOGIN_CLIENT_ID; ?>*</label>
    <div class="col-xs-8">
      <input id="client_id" name="client_id" type="text" class="form-control" required="required">
    </div>
  </div>
  <div class="form-group" data-if-enabled>
    <label for="client_secret" class="control-label col-xs-4"><?php echo TEXT_PLUGIN_AZURE_LOGIN_CLIENT_SECRET; ?>*</label>
    <div class="col-xs-8">
      <div class="input-group">
        <input required id="client_secret" name="client_secret" type="text" class="form-control content-hidden">
        <div class="input-group-addon c-pointer" onclick="$('#client_secret').toggleClass('content-hidden')">
          <i class="fa fa-eye"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group" data-if-enabled>
    <label for="scopes" class="control-label col-xs-4"><?php echo TEXT_PLUGIN_AZURE_LOGIN_SCOPES; ?>*</label>
    <div class="col-xs-8">
      <input id="scopes" name="scopes" type="text" class="form-control" required="required">
    </div>
  </div>
  <div class="form-group row">
    <div class="col-xs-offset-4 col-xs-8">
      <button name="submit" type="submit" class="btn btn-primary">
        <?php echo TEXT_PLUGIN_AZURE_LOGIN_SUBMIT; ?>
      </button>
    </div>
  </div>

  </form>
</div>
<script>
  $(function() {
    const settings = <?php echo json_encode($azure_login_settings); ?>;
    $('#azure_login_form').validate();
    $('#copy-redirect-url').click(function(e) {
      e.preventDefault();
      var el = document.getElementById("redirect_uri");
      el.select();
      el.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(el.value);
      const div = $(`<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">×</button><?php echo TEXT_PLUGIN_AZURE_LOGIN_COPIED; ?></div>`);
      $('#azure_login_messages').append(div)
      setTimeout(() => {
        div.remove();
      }, 3000);
    });
    for (key in settings) {
      let control = $('#azure_login_form').find(`[name="${key}"]`);
      if (control.attr('type') === 'checkbox') {
        if (settings[key]) {
          control.prop('checked', true);
          $(`[data-if-${key}]`).show();
        } else {
          $(`[data-if-${key}]`).hide();
        }
      } else {
        control.val(settings[key]);
      }
    }
    $('input[type="checkbox"]').on('change', function() {
      let key = $(this).attr('name');
      if ($(this).is(':checked')) {
        $(`[data-if-${key}]`).show();
      } else {
        $(`[data-if-${key}]`).hide();
      }
    });
  });
</script>