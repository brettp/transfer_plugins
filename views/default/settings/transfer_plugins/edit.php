<?php
/**
 * Settings view for export / import
 */

$link = elgg_view('output/url', array(
	'href' => $vars['url'] . 'pg/transfer_plugins/',
	'text' => elgg_echo('transfer_plugins'),
));

echo $link;

