<?php

// class database
class DB
{
  private $host = "localhost";
  private $user = "root";
  private $password = "";
  private $db_name = "db_stock_control";
  private $mysqli;

  public function __construct()
  {
    $this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->db_name);
    if ($this->mysqli->connect_errno) {
      printf("Connect failed: %s\n", $this->mysqli->connect_error);
      exit();
    }
  }
}
