<?php

class MaxLoadCalculator
{

  private static $defaultCoordinates = [
    'load' => null,
    'unload' => null,
  ];
  private static $defaultLoad = 0;
  private static $eventLoads = [
    '1' => 1,
    '0' => -1,
  ];
  private static $eventTypes = [
    '1',
    '0',
  ];
  private static $fieldNames = [
    'id' => 'id',
    'eventType' => 'flag',
    'coordinate' => 'time',
  ];
  private static $loadTypes = [
    '1' => 'load',
    '0' => 'unload',
  ];
  private static $precedingEventTypes = [
    '0' => '1',
  ];

  private static function &getLastLoadInterval(&$loadIntervals) {
    return $loadIntervals[count($loadIntervals) - 1];
  }
  private static function getObjCoordinate(&$curObj) {
    return $curObj[SELF::$fieldNames['coordinate']];
  }
  private static function getObjEventType(&$curObj) {
    return $curObj[SELF::$fieldNames['eventType']];
  }
  private static function getObjId(&$curObj) {
    return $curObj[SELF::$fieldNames['id']];
  }
  private static function getObjLoad(&$curObj) {
    return SELF::$eventLoads[SELF::getObjEventType($curObj)];
  }

  private static function insertLoadInterval(&$left, &$load, &$loadIntervals, &$right) {
    $loadIntervals[] = [
      'load' => $load,
      'left' => $left,
      'right' => $right,
    ];
  }
  private static function insertLoadIntervalFirst(&$curLoad, &$loadIntervals) {
    SELF::insertLoadInterval(SELF::$defaultCoordinates['load'], $curLoad, $loadIntervals, SELF::$defaultCoordinates['unload']);
  }
  private static function insertLoadIntervalMiddle(&$curLoad, &$curObj, &$loadIntervals) {
    $curLoad += SELF::getObjLoad($curObj);
    $lastLoadInterval = &SELF::getLastLoadInterval($loadIntervals);
    $curObjCoordinate = SELF::getObjCoordinate($curObj);
    if ($curObjCoordinate === $lastLoadInterval['left']) {
      $lastLoadInterval['load'] = $curLoad;
      SELF::mergeEqualLoadIntervals($loadIntervals);
    } else {
      $lastLoadInterval['right'] = $curObjCoordinate;
      SELF::insertLoadInterval($curObjCoordinate, $curLoad, $loadIntervals, SELF::$defaultCoordinates['unload']);
    }
  }
  private static function insertLoadIntervalsMiddle(&$curLoad, &$dataArr, &$loadIntervals) {
    for ($i = 0, $len = count($dataArr); $i < $len; $i++) {
      SELF::insertLoadIntervalMiddle($curLoad, $dataArr[$i], $loadIntervals);
    }
  }
  private static function calculateEventTypeRepeatsById(&$dataArr) {
    $eventTypeRepeatsById = [];
    foreach ($dataArr as $curObj) {
      $curObjId = SELF::getObjId($curObj);
      if (!array_key_exists($curObjId, $eventTypeRepeatsById)) {
        SELF::formEventTypeRepeatsById($curObj, $eventTypeRepeatsById);
      }
      SELF::incrementEventTypeRepeatsById($curObj, $eventTypeRepeatsById);
    }
    return $eventTypeRepeatsById;
  }
  private static function calculateLoadIntervals(&$dataArr) {
    $curLoad = SELF::$defaultLoad;
    $loadIntervals = [];
    SELF::insertLoadIntervalFirst($curLoad, $loadIntervals);
    SELF::insertLoadIntervalsMiddle($curLoad, $dataArr, $loadIntervals);
    return $loadIntervals;
  }
  private static function calculateMaxLoad(&$loadIntervals) {
    $curLoadMax = 0;
    foreach ($loadIntervals as $interval) {
      $curLoad = $interval['load'];
      if ($curLoadMax < $curLoad) {
        $curLoadMax = $curLoad;
      }
    };
    return $curLoadMax;
  }
  private static function compareCoordinates(&$objLeft, &$objRight) {
    return ($objLeft[SELF::$fieldNames['coordinate']] < $objRight[SELF::$fieldNames['coordinate']]) ? -1 : 1;
  }
  private static function formEventTypeRepeatsById(&$curObj, &$eventTypeRepeatsById) {
    $curObjId = SELF::getObjId($curObj);
    $eventTypeRepeatsById[$curObjId] = [];
    foreach (SELF::$eventTypes as $eventType) {
      $eventTypeRepeatsById[$curObjId][$eventType] = 0;
    }
  }
  private static function incrementEventTypeRepeatsById(&$curObj, &$eventTypeRepeatsById) {
    $eventTypeRepeatsById[SELF::getObjId($curObj)][SELF::getObjEventType($curObj)]++;
  }
  private static function insertPrecedingEventType(&$dataArr, &$id, &$precedingEventType, &$eventType, &$eventTypeRepeats) {
    $eventTypeRepeatsDifference = $eventTypeRepeats[$eventType] - $eventTypeRepeats[$precedingEventType];
    if ($eventTypeRepeatsDifference > 0) {
      SELF::insertEventType($dataArr, $id, $precedingEventType, $eventTypeRepeatsDifference);
    }
  }
  private static function insertPrecedingEvents(&$dataArr) {
    $eventTypeRepeatsById = SELF::calculateEventTypeRepeatsById($dataArr);
    SELF::updateDefaultCoordinates();
    foreach ($eventTypeRepeatsById as $id => $eventTypeRepeats) {
      SELF::insertPrecedingEventTypes($dataArr, $id, $eventTypeRepeats);
    }
  }
  private static function insertPrecedingEventTypes(&$dataArr, &$id, &$eventTypeRepeats) {
    foreach (SELF::$precedingEventTypes as $eventType => $precedingEventType) {
      SELF::insertPrecedingEventType($dataArr, $id, $precedingEventType, $eventType, $eventTypeRepeats);
    }
  }
  private static function insertEventType(&$dataArr, &$id, &$eventType, $times = 1) {
    for ($i = 0; $i < $times; $i++) {
      $dataArr[] = [
        SELF::$fieldNames['id'] => $id,
        SELF::$fieldNames['eventType'] => $eventType,
        SELF::$fieldNames['coordinate'] => SELF::$defaultCoordinates[SELF::$loadTypes[$eventType]],
      ];
    }
  }
  private static function mergeEqualLoadIntervals(&$loadIntervals) {
    $count = count($loadIntervals);
    if ($count > 1) {
      $lastInterval = &SELF::getLastLoadInterval($loadIntervals);
      $prevLastInterval = &$loadIntervals[$count - 2];
      if ($lastInterval['load'] === $prevLastInterval['load']) {
        array_pop($loadIntervals);
        $prevLastInterval['right'] = SELF::$defaultCoordinates['unload'];
      }
    }
  }
  private static function prepareData(&$dataArr) {
    SELF::insertPrecedingEvents($dataArr);
    usort($dataArr, [get_called_class(), 'compareCoordinates']);
  }
  private static function printLoadInterval(&$loadInterval) {
    printf(
      'С %s по %s активно соединений: %s%s',
      $loadInterval['left'],
      $loadInterval['right'],
      $loadInterval['load'],
      "\n"
    );
  }
  private static function printLoadIntervals(&$loadIntervals) {
    echo "\n";
    foreach ($loadIntervals as $loadInterval) {
      SELF::printLoadInterval($loadInterval);
    };
  }
  private static function printMaxLoad(&$maxLoad) {
    printf(
      'Максимум активных соединений: %s%s',
      $maxLoad,
      "\n\n"
    );
  }
  private static function updateDefaultCoordinates() {
    if (is_null(SELF::$defaultCoordinates['load'])) {
      SELF::$defaultCoordinates['load'] = 0;
    }
    if (is_null(SELF::$defaultCoordinates['unload'])) {
      SELF::$defaultCoordinates['unload'] = time();
    }
  }

  public static function processData($dataArr) {
    SELF::prepareData($dataArr);

    $loadIntervals = SELF::calculateLoadIntervals($dataArr);
    $maxLoad = SELF::calculateMaxLoad($loadIntervals);

    SELF::printLoadIntervals($loadIntervals);
    SELF::printMaxLoad($maxLoad);

    return $maxLoad;
  }

}

?>
