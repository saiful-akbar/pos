<?php

class ItemGroups
{
  private $db;
  private $table = "item_groups";

  public function __construct()
  {
    $this->db = new DB;
  }

  public function getALl()
  {
    $sql = "SELECT * FROM {$this->table}";
  }
}
