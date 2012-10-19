<?php
/**
 * Import plugins
 */

$settings_mode = get_input('settings_mode', 'if_not_exists');

$plugins_filename = null;
if (isset($_FILES['plugins']) && $_FILES['plugins']['error'] >= 0) {
	$plugins_filename = $_FILES['plugins']['tmp_name'];
}

if (!$plugins_filename) {
	register_error(elgg_echo('transfer_plugins:import:nothing_to_import'));
	forward(REFERRER);
}

$plugins = file_get_contents($plugins_filename);

if (!$plugins) {
	register_error(elgg_echo('transfer_plugins:import:nothing_to_import'));
	forward(REFERRER);
}

if (transfer_plugins_import($plugins, $settings_mode)) {
	system_message(elgg_echo('transfer_plugins:import:plugins_imported'));
} else {
	register_error(elgg_echo('transfer_plugins:import:could_not_import'));
}

elgg_invalidate_simplecache();
elgg_reset_system_cache();

forward(REFERRER);
