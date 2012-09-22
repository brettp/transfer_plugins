<?php
/**
 * Export plugin settings
 */

$file = transfer_plugins_export();

header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=plugins.txt");
header("Content-Type: text");

echo $file;

// don't try to forward.
exit;