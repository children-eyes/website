<?php

class Shortcodes{
  public static function bootsrap(){
    add_shortcode( 'children-form', 'Shortcodes::do_child_form');
  }

  public static function do_child_form($attributes){
    ob_start();
    Child_App::$form->render();
    return ob_get_clean();
  }
}
