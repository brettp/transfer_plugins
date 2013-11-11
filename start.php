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

	elgg_register_action('transfer_plugins/export', "$plugin_path/actions/transfer_plugins/export.php", 'admin');
	elgg_register_action('transfer_plugins/import', "$plugin_path/actions/transfer_plugins/import.php", 'admin');

	elgg_register_admin_menu_item('configure', 'transfer_plugins', 'settings');
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

	$info['plugins'] = array();
	$plugins = elgg_get_plugins('all');

  foreach ($plugins as $plugin) {
    if(is_object($plugin) && is_object($plugin->getManifest())){
		  $plugin_info = array(
			  'id' => $plugin->getID(),
			  'version' => $plugin->getManifest()->getVersion(),
			  'active' => (bool) $plugin->isActive(),
			  'settings' => $plugin->getAllSettings(),
			  'priority' => $plugin->getPriority()
	  	);
    }
		$plugin_order[$plugin->getPriority() * 10] = $plugin->getID();

		$info['plugins'][] = $plugin_info;
	}

	$info['17_pluginorder'] = serialize($plugin_order);

	return serialize($info);
}

/**
 * Import plugin settings
 *
 * @param string $info
 * @param string $settings_mode Options to load plugin settings. One of overwrite, if_not_exists, or ignore
 * @return type bool
 */
function transfer_plugins_import($info, $settings_mode = 'if_not_exists') {
	$info = unserialize($info);

	if (!$info) {
		return false;
	}

	$version = elgg_extract('transfer_plugins_format', $info);

	if ($version != TRANSFER_PLUGINS_FORMAT) {
		return false;
	}

	// @todo check elgg, plugin, and transfer_plugin version compatibility.
	if (!isset($info['plugins'])) {
		return false;
	}

	$r = true;

	foreach ($info['plugins'] as $plugin_info) {
		$plugin_id = $plugin_info['id'];
		$plugin = new ElggPlugin($plugin_id);

		// not installed
		if (!$plugin->isValid()) {
			continue;
		}

		$r &= $plugin->setPriority($plugin_info['priority']);

		if ($plugin_info['active'] && !$plugin->isActive()) {
			$r &= $plugin->activate();
		}

		if ($settings_mode != 'ignore' && $plugin_info['settings']) {
			foreach($plugin_info['settings'] as $name => $value) {
				switch($settings_mode) {
					case 'overwrite':
						$plugin->setSetting($name, $value);
						break;

					case 'if_not_exists':
						// @todo not sure if this works because isset isn't overloaded in ElggPlugin
						if (!isset($plugin->$name)) {
							$plugin->setSetting($name, $value);
						}
						break;
				}

			}
		}
	}

	return $r;
}

elgg_register_event_handler('init', 'system', 'transfer_plugins_init');
