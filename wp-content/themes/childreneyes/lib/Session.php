<?php

class Session{
  protected $ns;
  public function __construct($ns = 'default'){
    $this->ns = $ns;
    @session_start();
    if(!isset($_SESSION[$this->ns]))
      $_SESSION[$this->ns] = [];
  }

  public function __get($name){
    if(!isset($_SESSION[$this->ns][$name]))
      return null;

    return $_SESSION[$this->ns][$name];
  }

  public function __set($name, $value){
    $_SESSION[$this->ns][$name] = $value;
  }

  public function __isset($name){
    return isset($_SESSION[$this->ns][$name]);
  }
}
