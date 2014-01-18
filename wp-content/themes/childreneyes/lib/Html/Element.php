<?php

class Html_Element{
  public $tag;
  public $extra;
  public $description='';
  public $content='';
  protected $params=[];

  public function __construct($tag, $params, $extra=null){
    $this->tag = $tag;
    $this->params = $params;
    $this->extra = $extra;
    if(isset($this->params['description'])){
      $this->description = $this->params['description'];
      unset($this->params['description']);
    }

    if(isset($this->params['content'])){
      $this->content = $this->params['content'];
      unset($this->params['content']);
    }
  }

  public function __get($name){
    if(!isset($this->params[$name]))
      return '';

    return $this->params[$name];
  }

  public function __unset($name){
    unset($this->params[$name]);
  }

  public function __set($name, $value){
    $this->params[$name] = $value;
  }

  public function render_params(){
    return Util::array_2_attributes($this->params);
  }

  public function __toString(){
    switch($this->tag){
      case 'input':
      case 'br':
      case 'img':
      case 'file':
        $short = true;
      break;
      default:
        $short = false;
    }

    if($short)
      return '<'.$this->tag.' '.$this->render_params().'/>';
    else
      return '<'.$this->tag.' '.$this->render_params().'>'.$this->content.'</'.$this->tag.'>';
  }
}
