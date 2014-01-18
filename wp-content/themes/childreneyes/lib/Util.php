<?php

define('CHILD_AUTOLOAD_PATH', dirname(__FILE__));

class Util{
  public static function start_autoloader(){
    spl_autoload_register('Util::autoload');
  }

  public static function autoload($clazz){
    $file = CHILD_AUTOLOAD_PATH.'/'.str_replace('_', '/', $clazz).'.php';
    if(file_exists($file))
      include_once $file;

    return class_exists($clazz);
  }

  public static function array_2_attributes($attributes){
    $result = [];
    foreach($attributes as $key => $value)
      $result[]= $key.'="'.addcslashes($value, '"').'"';

    return implode(' ', $result);
  }

  public static $image_extensions = ['gif', 'jpeg', 'png'];
  public static function is_image($path){
    $ext = array_pop(explode('.', $path));

    return in_array(strtolower($ext), self::$image_extensions);
  }

  public static function insert_attachment($file_index, $post_id, $setthumb = false) {
    if(!isset($_FILES[$file_index])
    || $_FILES[$file_index]['error'] !== UPLOAD_ERR_OK)
      return false;

    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');

    $attach_id = media_handle_upload( $file_index, $post_id );

    if ($setthumb) update_post_meta($post_id, '_thumbnail_id', $attach_id);

    return $attach_id;
  }
}
