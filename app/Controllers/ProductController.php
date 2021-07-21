<?php

class ProductController extends Controller
{
  /**
   * Method index
   */
  public function index()
  {
    $data["title"] = "Product";

    $this->view('layouts/header', $data);
    $this->view('home/index', $data);
    $this->view('layouts/footer');
  }

  public function create()
  {
    $data["title"] = "Create New Product";
    
    $this->view('home/index', $data);
  }
}
