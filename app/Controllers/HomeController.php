<?php

class HomeController extends Controller
{
  /**
   * Method index
   */
  public function index()
  {
    $data["title"] = "Home";

    // views
    $this->view('layouts/header', $data);
    $this->view('home/index', $data);
    $this->view('layouts/footer', $data);
  }

  public function create()
  {
    $data["title"] = "Create";
    $this->view('home/index', $data);
  }
}
