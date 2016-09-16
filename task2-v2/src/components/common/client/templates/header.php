<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $PAGE_TITLE; ?></title>
    <?php foreach($PAGE_STYLE_PATHES as $pageStylePath) : ?>
    <link rel="stylesheet" href="<?php echo $pageStylePath; ?>" media="screen" title="no title">
    <?php endforeach; ?>
  </head>
  <body>
    <h1><?php echo $PAGE_TITLE; ?></h1>
