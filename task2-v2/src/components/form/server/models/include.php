<?php

//include_once($ROOT_PATH . '/models/common/include.php');
$fields = DbConnection::getRequiredFields();
if (0 < count($_GET)) {
  foreach ($_GET as $key => $value) {
    if ($value) {
      echo $key . ': заполнен<br>';
    } else {
      echo $key . ': пусто<br>';
    }
  }
  die();
}

?>
