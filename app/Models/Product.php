<?php

class Product
{
  private $db;
  private $table = "products";

  public function __construct()
  {
    $this->db = new DB;
  }

  public function getAll()
  {
    $sql = "SELECT * FROM {$this->table}";
  }
}
