<?php

/**********************************************************************

*			CRM Connector Admin Page					*

*********************************************************************/

function crm_connector_options() {
	global $wpdb;

	$poststable = $wpdb->posts;
	$enable_plugin = sanitize_key($_POST['enable_plugin']);
	$crm_connector_acct = sanitize_text_field($_POST['crm_connector_acct']);
	$crm_connector_domain = sanitize_text_field($_POST['crm_connector_domain']);

	$crm_connector_settings = crm_connector_read_options();

	if(isset($_POST['crm_connector_save']) && $_POST['crm_connector_save']) {
		$crm_connector_settings['enable_plugin'] = ((isset($enable_plugin) && $enable_plugin) ? true : false);
		$crm_connector_settings['crm_connector_acct'] = ($crm_connector_acct);
		$crm_connector_settings['crm_connector_domain'] = ($crm_connector_domain);

		update_option('crm_connector_settings', $crm_connector_settings);

		$str = '<div id="message" class="updated fade"><p>'. __('Options saved successfully.',CRM_CONNECTOR_LOCAL_NAME) .'</p></div>';

		echo $str;
	}

	$crm_connector_acct = esc_attr(stripslashes($crm_connector_settings['crm_connector_acct']));
	$crm_connector_domain = esc_attr($crm_connector_settings['crm_connector_domain']);
?>

<div class="wrap">
	<div id="page-wrap">
		<div id="inside">
			<div id="options-div">
				<div id="headerimage">
					<img src="<?php echo esc_url(plugins_url('header.png', __FILE__)); ?>" alt="CRM Connector logo"/>
				</div>

				<form method="post" id="crm_connector_options" name="crm_connector_options" style="border: #ccc 1px solid; padding: 10px" onsubmit="return checkForm();">
					<fieldset class="options">
						<table class="form-table">
							<tr style="vertical-align: top; font-size:15px;"><th scope="row" style="background:#<?php if ($crm_connector_settings['enable_plugin']) echo 'cfc'; else echo 'fcc'; ?>"><b><label for="enable_plugin" id="enable"><?php _e('Enable Tracking: ',CRM_CONNECTOR_LOCAL_NAME); ?></label></b></th>
								<td style="background:#<?php if ($crm_connector_settings['enable_plugin']) echo 'cfc'; else echo 'fcc'; ?>"><input type="checkbox" name="enable_plugin" id="enable_plugin" <?php if ($crm_connector_settings['enable_plugin']) echo 'checked="checked"' ?> /></td>
							</tr>
						</table>
						<br />
						<table class="form-table">
							<tr style="vertical-align: top; font-size:13.2px;"><th scope="row"><b><label for="crm_connector_acct"><?php _e('Account Number: ',CRM_CONNECTOR_LOCAL_NAME); ?></label></b></th>
								<td><input type="textbox" name="crm_connector_acct" id="crm_connector_acct" value="<?php echo $crm_connector_acct; ?>" style="width:250px" placeholder="12345" /></td>
							</tr>
							<tr style="vertical-align: top; font-size:13.2px;"><th scope="row"><b><label for="crm_connector_domain"><?php _e('Portal Domain: ',CRM_CONNECTOR_LOCAL_NAME); ?></label></b></th>
								<td><input type="textbox" name="crm_connector_domain" id="crm_connector_domain" value="<?php echo $crm_connector_domain; ?>" style="width:250px" placeholder="app.portal.com" /></td>
							</tr>
							<tr style="vertical-align: top; ">
								<td scope="row" colspan="2"><textarea name="crm_connector_other" id="crm_connector_other" rows="8" cols="80" readonly>
<script type="text/javascript">
var bfpa=<?php echo $crm_connector_acct; ?>; var bfpp=window.location.href; var bfpr=window.document.referrer; var bfpd='<?php echo $crm_connector_domain; ?>'; var bfpq=0;
(function(){var bfp1=document.createElement('script');bfp1.type='text/javascript';bfp1.async=true;bfp1.src='//'+bfpd+'/t.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(bfp1,s);})();
</script>
								</textarea></td>
							</tr>
						</table>
					</fieldset>
					<p>
						<input type="submit" name="crm_connector_save" id="crm_connector_save" value="Save" style="border:#00CC00 1px solid" />
					</p>
				</form>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</div>

<?php
}

function crm_connector_admin_notice() {
	$plugin_settings_page = '<a href="' . admin_url( 'admin.php?page=options-writing.php?page=crm_connector_options' ) . '">' . __('plugin settings page', CRM_CONNECTOR_LOCAL_NAME ) . '</a>';

	$crm_connector_settings = crm_connector_read_options();

	if ($crm_connector_settings['enable_plugin']) return;
	if ( !current_user_can( 'manage_options' ) ) return;

	echo '<div class="error">
		<p>'.__('The CRM Connector Analytics plugin is disabled.').'</p>
	</div>';

}

add_action('admin_notices', 'crm_connector_admin_notice');

function crm_connector_adminmenu() {
	$crm_connector_is_admin = false;

	if (function_exists('current_user_can')) {
		if (current_user_can('manage_options')) {
			$crm_connector_is_admin = true;
		}
	}

	if (function_exists('add_options_page') && $crm_connector_is_admin) {
		$plugin_page = add_menu_page('CRM Connector Analytics Admin Page', 'CRM Connector', 'add_users', '/options-writing.php?page=crm_connector_options', 'crm_connector_options',   plugins_url('icon.png', __FILE__), 76);
		add_action('admin_head-' . $plugin_page, 'crm_connector_adminhead');
	}
}

add_action('admin_menu', 'crm_connector_adminmenu');

function crm_connector_adminhead() {
	wp_enqueue_style('admin-styles', esc_url(plugins_url('admin-styles.css', __FILE__)));
?>

<script type="text/javascript" language="JavaScript">
	function checkForm() {
		var hasValue = document && document.getElementById('crm_connector_acct') && document.getElementById('crm_connector_acct').value;
		var hasDomain = document && document.getElementById('crm_connector_domain') && document.getElementById('crm_connector_domain').value;
		var enabled = document && document.getElementById('enable_plugin') && document.getElementById('enable_plugin').checked;

		if (enabled && !hasValue) {
			alert('Please enter your Account Number');
			return false;
		} else if (enabled && !hasDomain) {
			alert('Please enter your Portal Domain');
			return false;
		} else {
			return true;
		}
	}
</script>

<?php
}

add_action('admin_enqueue_scripts', 'crm_connector_adminhead');

?>
