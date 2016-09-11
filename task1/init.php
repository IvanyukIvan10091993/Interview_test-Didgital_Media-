<?php

require_once 'MaxLoadCalculator.php';
require_once 'datasamples.php';

if (
  MaxLoadCalculator::processData($dataSample1) === 2 &&
  MaxLoadCalculator::processData($dataSample2) === 3 &&
  MaxLoadCalculator::processData($dataSample3) === 3 &&
  MaxLoadCalculator::processData($dataSample4) === 1 &&
  MaxLoadCalculator::processData($dataSample5) === 0 &&
  MaxLoadCalculator::processData($dataSample6) === 0
) {
  echo "\n" . 'Все тесты успешно пройдены' . "\n\n";
}

?>
