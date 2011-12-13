<?php
/**
 * Link to export, form to upload an import.
 */

$export_link = elgg_view('output/url', array(
	'href' => get_config('site')->url . 'action/transfer_plugins/export',
	'text' => elgg_echo('transfer_plugins:export'),
	'is_action' => true
));

$input = elgg_view('input/file', array(
	'internalname' => 'plugins'
));

$button = elgg_view('input/submit', array(
	'value' => elgg_echo('transfer_plugins:import')
));

$form = elgg_view('input/form', array(
	'body' => $input . '<br />' . $button,
	'enctype' => 'multipart/form-data',
	'action' => $vars['url'] . '/action/transfer_plugins/import'
));

$title = elgg_echo('transfer_plugins');

$content = elgg_view_title($title);

$main_content = <<<___HTML
<p>
$export_link
</p>

<p>
$form
</p>
___HTML;

$content .= elgg_view('page_elements/contentwrapper', array(
	'body' =>  $main_content
));


$body = elgg_view_layout('two_column_left_sidebar', '', $content);
page_draw($title, $body);


