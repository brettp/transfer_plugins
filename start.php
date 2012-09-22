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

define('TRANSFER_PLUGINS_FORMAT', 2);

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
		'transfer_plugins_format' => TRANSFER_PLUGINS_FORMAT
	);

	$site = get_config('site');
	$info['17_pluginorder'] = $site->pluginorder;
	
	$info['plugins'] = array();
	$plugins = get_plugin_list();

	$priority = 1;
	foreach ($plugins as $plugin_id) {
		// find_plugin_settings() doesn't do anything like what it claims to do.
		$plugin_entity = find_plugin_settings($plugin_id);

		$settings = false;

		if ($plugin_entity) {
			$dbprefix = get_config("dbprefix");
			$q = "SELECT * FROM {$dbprefix}private_settings
				WHERE entity_guid = $plugin_entity->guid";
			$setting_objs = get_data($q);

			foreach($setting_objs as $setting) {
				$settings[$setting->name] = $setting->value;
			}
		}

		$manifest = load_plugin_manifest($plugin_id);
		$plugin_info = array(
			'id' => $plugin_id,
			'version' => $manifest['version'],
			'active' => is_plugin_enabled($plugin_id),
			'settings' => $settings,
			'priority' => $priority
		);

		$info['plugins'][] = $plugin_info;
		$priority++;
	}

	return serialize($info);
}

/**
 * Import plugin settings
 *
 * @param type $info
 * @return type bool
 */
function transfer_plugins_import($info, $settings_mode = 'if_not_exists') {
	$info = unserialize($info);
	// @todo check elgg, plugin, and transfer_plugin version compatibility.

	$version = $info['transfer_plugins_format'];

	if ($version != TRANSFER_PLUGINS_FORMAT) {
		return false;
	}

	$site = get_config('site');
	$r = (bool) $site->pluginorder = $info['17_pluginorder'];

	foreach ($info['plugins'] as $plugin_info) {
		$plugin_id = $plugin_info['id'];
		$local_manifest = load_plugin_manifest($plugin_id);
		if (!$local_manifest) {
			continue;
		}
		if ($plugin_info['active'] && !is_plugin_enabled($plugin_info['id'])) {
			$r &= enable_plugin($plugin_info['id']);
		}

		if ($settings_mode != 'ignore' && $plugin_info['settings']) {
			$plugin_entity = find_plugin_settings($plugin_id);
			
			foreach($plugin_info['settings'] as $name => $value) {
				switch($settings_mode) {
					case 'overwrite':
						set_plugin_setting($name, $value, $plugin_id);
						break;

					case 'if_not_exists':
						// @todo not sure if this works because isset isn't overloaded in ElggPlugin
						if (!isset($plugin_entity->$name)) {
							set_plugin_setting($name, $value, $plugin_id);
						}
						break;
				}

			}
		}
	}

	return $r;
}

register_elgg_event_handler('init', 'system', 'transfer_plugins_init');