<?php

class App
{
  private $controller = "HomeController";
  private $method = "index";

  public function __construct()
  {
    $this->routing();
  }

  /**
   * Fungsi untuk membuat routing dan menjalankan class controller beserta method-nya
   */
  public function routing()
  {
    // Cek apakah ada query string ctrl
    if (isset($_GET["ctrl"]) && !empty($_GET["ctrl"])) {

      // Mencegah url berbahaya
      // membersihkan dari spasi di awal dan akhir
      // membuat karakter pertama menjadi kapital
      $controller = filter_var(trim(ucwords($_GET["ctrl"])), FILTER_SANITIZE_URL);
      $controller .= "Controller";

      // cek apakah file ada atau tidak
      if (file_exists("app/Controllers/{$controller}.php")) {
        $this->controller = $controller;
        unset($controller);
      }
    }

    // panggil dan instansiasi controller
    require "app/Controllers/{$this->controller}.php";
    $this->controller = new $this->controller;

    // cek apakah ada query sting mthd
    if (isset($_GET["mthd"]) && !empty($_GET["mthd"])) {

      // Mencegah url berbahaya
      // membersihkan dari spasi di awal dan akhir
      $method = filter_var(trim($_GET["mthd"]), FILTER_SANITIZE_URL);

      // cek apakah method ada atau tidak
      if (method_exists($this->controller, $method)) {
        $this->method = $method;
        unset($method);
      }
    }

    // jalankan controller beserta method-nya
    call_user_func([$this->controller, $this->method]);
  }
}
