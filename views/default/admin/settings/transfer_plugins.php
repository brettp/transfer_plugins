<?php
/**
 * Settings view for export / import
 */

$instructions = elgg_echo('transfer_plugins:instructions');

$export_link = elgg_view('output/url', array(
	'href' => 'action/transfer_plugins/export',
	'text' => elgg_echo('transfer_plugins:export'),
	'class' => 'elgg-button elgg-button-action',
	'is_action' => true
));

$form = elgg_view_form('transfer_plugins/import', array(
	'enctype' => 'multipart/form-data'
));

$title = elgg_echo('transfer_plugins');

$content = elgg_view_title($title);

echo <<<___HTML
<p>$instructions</p>

<p>$export_link<p>

<hr class="mvl" />

$form
___HTML;

