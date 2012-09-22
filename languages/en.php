<?php

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$en = array(
	'transfer_plugins:import:nothing_to_import' => 'Nothing to import.',
	'transfer_plugins:import:plugins_imported' => 'Plugin settings imported',
	'transfer_plugins:import:could_not_import' => 'Could not import plugin settings',

	'transfer_plugins:export' => 'Export current plugin settings',
	'transfer_plugins:import' => 'Import plugin settings',
	'transfer_plugins' => 'Transfer plugin settings',

	'transfer_plugins:instructions' => 'Click the "Export current plugin settings" button to download a file of your current plugin settings.'
		. ' To restore plugin settings, make sure you have all the plugins in the mod/ directory, then upload the exported file.',
	'admin:settings:transfer_plugins' => 'Transfer plugin settings',

	'transfer_plugins:settings_mode' => 'Plugin settings: ',
	'transfer_plugins:settings_mode:ignore' => 'Do not import plugin settings',
	'transfer_plugins:settings_mode:overwrite' => 'Import all settings, overwriting any settings on this installation',
	'transfer_plugins:settings_mode:if_not_exists' => 'Import only settings that don\'t exist on this installation'
);

add_translation('en', $en);