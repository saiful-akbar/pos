<?php

class ItemGroupsController extends Controller
{
  /**
   * Method index
   */
  public function index()
  {
    $data["title"] = "Item Groups";
    $this->view('home/index', $data);
  }

  public function create()
  {
    $data["title"] = "Create Item Groups";
    $this->view('home/index', $data);
  }
}
