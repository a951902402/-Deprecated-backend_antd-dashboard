<?php
  class Sql
  {
    private $host;      // 服务器地址
    private $root;      // 用户名
    private $password;  // 密码
    private $database;  // 数据库名

    // 初始化
    function __construct($host,$root,$password,$database)
    {
      $this->host = $host;
      $this->root = $root;
      $this->password = $password;
      $this->database = $database;
      $this->connect();
    }

    // 数据库基础操作
    // connect DB
    function connect()
    {
      $this->conn = mysqli_connect($this->host,$this->root,$this->password) or die(json_encode(['code' => 500]).mysqli_error($this));
      mysqli_select_db($this->conn,$this->database);
      mysqli_query($this->conn,"set names utf8");
    }
    // close DB
    function dbClose()
    {
      mysqli_close($this->conn);
    }

    //执行语句
    // mysqli_query()
    function query($sql)
    {
      return mysqli_query($this->conn,$sql);
    }

    //获取结果
    // mysqli_fetch_array()
    function sqlarray($result)
    {
      return mysqli_fetch_assoc($result);
    }
    // mysqli_num_rows()
    function rows($result)
    {
      return mysqli_num_rows($result);
    }
    // mysqli_affected_rows
    function affrows($conn)
    {
      return mysqli_affected_rows($conn);
    }

    // 进行的操作
    // select function
    function select($tableName,$condition)
    {
      return $this->query("SELECT * FROM $tableName $condition");
    }
    // insert function
    function insert($tableName,$fields,$value)
    {
      return $this->query("INSERT INTO $tableName $fields VALUES $value");
    }
    // update function
    function update($tableName,$change,$condition)
    {
      return $this->query("UPDATE $tableName SET $change $condition");
    }
    // delete function
    function delete($tableName,$condition)
    {
      return $this->query("DELETE FROM $tableName $condition");
    }
  }
