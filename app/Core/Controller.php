<?php

class Controller
{
  /**
   * Fungsi untuk memnanggil file view
   * 
   * @param String $view
   * @param Array $data
   */
  protected function view(String $view, array $data = [])
  {
    require "views/{$view}.php";
  }

  /**
   * Method untuk memanggil file model dan menjalankan class-nya
   * 
   * @param String $model
   * 
   * @return Object
   */
  protected function model(String $model)
  {
    require_once "app/Models/{$model}.php";
    return new $model;
  }
}
