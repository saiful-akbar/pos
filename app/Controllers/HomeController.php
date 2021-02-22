<?php

class HomeController extends Controller
{
  /**
   * Method index
   */
  public function index()
  {
    $data["title"] = "Home";
    $this->view('home/index', $data);
  }

  public function create()
  {
    $data["title"] = "Create";
    $this->view('home/index', $data);
  }
}
