<?php

$PAGE_TITLE = 'Default page title';
$PAGE_CONTENT_PATHES = [];
include_once($PAGE_PATH);
include_once('header.php');

foreach ($PAGE_CONTENT_PATHES as $pageContentPath) {
  include_once($pageContentPath);
}

include_once('footer.php');

?>
