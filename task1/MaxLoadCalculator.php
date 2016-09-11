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

  private static function addLoadInterval(&$curLoad, &$curObj, &$loadIntervals) {
    $curLoad += SELF::$eventLoads[$curObj[SELF::$fieldNames['eventType']]];
    $lastLoadInterval = &$loadIntervals[count($loadIntervals) - 1];
    if ($curObj[SELF::$fieldNames['coordinate']] === $lastLoadInterval['left']) {
      $lastLoadInterval['load'] = $curLoad;
      SELF::mergeEqualLoadIntervals($loadIntervals);
    } else {
      $lastLoadInterval['right'] = $curObj[SELF::$fieldNames['coordinate']];
      $loadIntervals[] = [
        'load' => $curLoad,
        'left' => $curObj[SELF::$fieldNames['coordinate']],
        'right' => SELF::$defaultCoordinates['unload'],
      ];
    }
  }
  private static function addLoadIntervalFirst(&$curLoad, &$loadIntervals) {
    $loadIntervals[] = [
      'load' => $curLoad,
      'left' => SELF::$defaultCoordinates['load'],
      'right' => SELF::$defaultCoordinates['unload'],
    ];
  }
  private static function calculateEventTypeRepeatsById(&$dataArr) {
    $eventTypeRepeatsById = [];
    foreach ($dataArr as $curObj) {
      $curObjId = $curObj[self::$fieldNames['id']];
      $curObjEventType = $curObj[self::$fieldNames['eventType']];
      if (!array_key_exists($curObjId, $eventTypeRepeatsById)) {
        $eventTypeRepeatsById[$curObjId] = [];
        foreach (SELF::$eventTypes as $eventType) {
          $eventTypeRepeatsById[$curObjId][$eventType] = 0;
        }
      }
      $eventTypeRepeatsById[$curObjId][$curObjEventType]++;
    }
    return $eventTypeRepeatsById;
  }
  private static function calculateLoadIntervals(&$dataArr) {
    $curLoad = SELF::$defaultLoad;
    $loadIntervals = [];
    SELF::addLoadIntervalFirst($curLoad, $loadIntervals);
    for ($i = 0, $len = count($dataArr); $i < $len; $i++) {
      SELF::addLoadInterval($curLoad, $dataArr[$i], $loadIntervals);
    }
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
  private static function insertEventType(&$dataArr, &$id, &$eventType, $times = 0) {
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
      $lastInterval = &$loadIntervals[$count - 1];
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
  private static function printLoadIntervals(&$loadIntervals) {
    echo "\n";
    foreach ($loadIntervals as $interval) {
      echo
        'С ' .
        $interval['left'] .
        ' по ' .
        $interval['right'] .
        ' активно соединений: ' .
        $interval['load'] .
        "\n";
    };
  }
  private static function printMaxLoad(&$maxLoad) {
    echo
      'Максимум активных соединений: ' .
      $maxLoad .
      "\n\n";
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
