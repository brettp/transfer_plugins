<?php
/**
 * Import form
 */

$input = elgg_view('input/file', array(
	'name' => 'plugins'
));

$settings = elgg_view('input/dropdown', array(
	'name' => 'settings_mode',
	'options_values' => array(
		'ignore' => elgg_echo('transfer_plugins:settings_mode:ignore'),
		'overwrite' => elgg_echo('transfer_plugins:settings_mode:overwrite'),
		'if_not_exists' => elgg_echo('transfer_plugins:settings_mode:if_not_exists'),
	),
	'value' => 'if_not_exists'
));

$button = elgg_view('input/submit', array(
	'value' => elgg_echo('transfer_plugins:import'),
	'class' => 'mtl'
));

echo $input;

echo '<br /><br /><label>' . elgg_echo('transfer_plugins:settings_mode') . $settings . '</label>';

echo '<br />' . $button;