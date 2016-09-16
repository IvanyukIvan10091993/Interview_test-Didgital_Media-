<?php

class DbConnection
{
  private static $dbname = 'ivanivanyuk_task2';
  private static $host = 'localhost';
  private static $password = '123456';
  private static $tableName = 'tbl_user';
  private static $dbType = 'mysql';
  private static $user = 'root';

  private static $fields = [
    'name',
    'email',
    'password',
  ];
  private static $requiredFields = [
    'email',
    'password',
  ];

  private static function getConnection()
  {
    return new PDO(
      SELF::getPdoParams(),
      SELF::$user,
      SELF::$password
    );
  }
  private static function getPdoParams()
  {
    return SELF::$dbType . ':host=' . SELF::$host . ';dbname=' . SELF::$dbname;
  }

  public static function getRequiredFields()
  {
    return SELF::$requiredFields;
  }
  public static function readAll()
  {
    try {
      $data = [];
      $connection = SELF::getConnection();
      foreach($connection->query('SELECT * from ' . SELF::$tableName) as $row) {
        $data[] = $row;
      }
      $connection = null;
      return $data;
    } catch (PDOException $error) {
      printf('Error!: %s<br/>', $error->getMessage());
      die();
    }
  }
}

?>
