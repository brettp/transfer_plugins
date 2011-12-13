<?php
/**
 * This allows you to export and import plugin configurations in Elgg sites.
 *
 * The data is saved as a PHP serialized file. The follow information is exported:
 *	Active plugins
 *	Plugin settings
 *	Plugin priority
 *
 *  The settings will only work for plugins that save their information in private setting using the
 *	sliding forms on the "Tool Administration" page.
 */

define('TRANSFER_PLUGINS_VERSION', 1.0);

function transfer_plugins_init() {
	// actions
	$plugin_path = dirname(__FILE__);
	register_action('transfer_plugins/export', false, "$plugin_path/actions/transfer_plugins/export.php", true);
	register_action('transfer_plugins/import', false, "$plugin_path/actions/transfer_plugins/import.php", true);

	register_page_handler('transfer_plugins', 'transfer_plugins_page_handler');
}

/**
 * Serves the transfer plugins page
 */
function transfer_plugins_page_handler($page) {
	admin_gatekeeper();
	set_context('admin');
	include dirname(__FILE__) . '/pages/transfer_plugins/transfer.php';
	return true;
}

/**
 * Exports plugins and their configuration
 */
function transfer_plugins_export() {
	// metadata
	$info = array(
		'elgg_version' => get_version(true),
		'elgg_release' => get_version(false),
		'transfer_plugins_version' => TRANSFER_PLUGINS_VERSION
	);

	$site = get_config('site');

	// plugin order
	$info['pluginorder'] = $site->pluginorder;

	// active or inactive, @todo settings
	$info['plugins'] = array();
	$plugins = get_plugin_list();

	foreach ($plugins as $plugin_id) {
		$plugin_info = array(
			'id' => $plugin_id,
			'manifest' => load_plugin_manifest($plugin_id),
			'active' => is_plugin_enabled($plugin_id)
		);

		$info['plugins'][] = $plugin_info;

//		$settings = find_plugin_settings($plugin_id);
//		var_dump($settings);
	}

	return serialize($info);
}

/**
 * Import plugin settings
 *
 * @param type $info
 * @return type bool
 */
function transfer_plugins_import($info) {
	$info = unserialize($info);
	// @todo check elgg, plugin, and transfer_plugin version compatibility.

	if (!isset($info['pluginorder']) || !isset($info['plugins'])) {
		return false;
	}

	$site = get_config('site');
	$r = (bool) $site->pluginorder = $info['pluginorder'];

	foreach ($info['plugins'] as $plugin_info) {
		$plugin_id = $plugin_info['id'];
		$local_manifest = load_plugin_manifest($plugin_id);
		if (!$local_manifest) {
			continue;
		}
		if ($plugin_info['active'] && !is_plugin_enabled($plugin_info['id'])) {
			$r &= enable_plugin($plugin_info['id']);
		}
	}

	return $r;
}

register_elgg_event_handler('init', 'system', 'transfer_plugins_init');