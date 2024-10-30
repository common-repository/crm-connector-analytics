<?php

/*
Plugin Name: CRM Connector Analytics
Plugin URI:  https://wordpress.org/plugins/crm-connector-analytics/
Description: Plugin to add CRM Connector Analytics to the footer of your WordPress pages
Author:      CRM Connector
Author URI:  http://www.completecrm.com
Version:     1.5.6
*/


if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

define('CRM_CONNECTOR_DIR', dirname(__FILE__));
define('CRM_CONNECTOR_LOCAL_NAME', 'crm_connector');

/*********************************************************************

*				Main Function (Do not edit)							*

********************************************************************/

add_action('wp_footer','crm_connector');

function crm_connector() {
	$crm_connector_settings = crm_connector_read_options();
	$enable_plugin = stripslashes($crm_connector_settings['enable_plugin']);
	$crm_connector_acct = stripslashes($crm_connector_settings['crm_connector_acct']);
	$crm_connector_domain = esc_attr($crm_connector_settings['crm_connector_domain']);

	if ($enable_plugin) {
?>

	<!-- Start CRM Connector Analytics -->
	<script type="text/javascript">
		var bfpa=<?php echo $crm_connector_acct; ?>; var bfpp=window.location.href; var bfpr=window.document.referrer; var bfpd='<?php echo $crm_connector_domain; ?>'; var bfpq=0;
		(function(){var bfp1=document.createElement('script');bfp1.type='text/javascript';bfp1.async=true;bfp1.src='//'+bfpd+'/t.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(bfp1,s);})();
	</script>
	<!-- End CRM Connector Analytics -->

<?php
	}
}

// Default Options

function crm_connector_default_options() {
	$ga_url = ".".parse_url(get_option('home'),PHP_URL_HOST);

	$crm_connector_settings = 	Array (
		'enable_plugin' => false,		// Enable plugin switch
		'crm_connector_acct' => '',
		'crm_connector_domain' => '',
	);

	return $crm_connector_settings;
}

// Function to read options from the database
function crm_connector_read_options() {
	$crm_connector_settings_changed = false;

	//crm_connector_activate();

	$defaults = crm_connector_default_options();

	$crm_connector_settings = array_map('stripslashes',(array)get_option('crm_connector_settings'));

	unset($crm_connector_settings[0]); // produced by the (array) when there is nothing in the database

	foreach ($defaults as $k=>$v) {
		if (!isset($crm_connector_settings[$k])) {
			$crm_connector_settings[$k] = $v;
			$crm_connector_settings_changed = true;
		}
	}

	if ($crm_connector_settings_changed == true) {
		update_option('crm_connector_settings', $crm_connector_settings);
	}

	return $crm_connector_settings;
}

// This function adds an Options page in WP Admin
if (is_admin() || strstr($_SERVER['PHP_SELF'], 'wp-admin/')) {
	require_once(CRM_CONNECTOR_DIR . "/admin.inc.php");

	// Add meta links
	function crm_connector_plugin_actions($links, $file) {
		static $plugin;

		if (!$plugin) $plugin = plugin_basename(__FILE__);

		// create link
		if ($file == $plugin) {
			$links[] = '<a href="' . admin_url( 'admin.php?page=options-writing.php?page=crm_connector_options' ) . '">' . __('Settings', CRM_CONNECTOR_LOCAL_NAME ) . '</a>';
		}

		return $links;
	}

	add_filter('plugin_row_meta', 'crm_connector_plugin_actions', 10, 2);
}

?>
