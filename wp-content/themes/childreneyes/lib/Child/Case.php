<?php

class Child_Case{
  const METAKEY_MISSED     = 'missing';
  const METAKEY_DATE       = 'date';
  const METAKEY_COSTS      = 'costs';
  const KEY_MISSED_DAYS    = 'missed_days';
  const KEY_COSTS_EURO     = 'costs_euro';
  const KEY_COSTS_TAXPAYER = 'costs_taxpayer';
  const POST_TYPE_NAME     = 'children';

  protected static $meta_keys = [self::METAKEY_COSTS, self::METAKEY_DATE, self::METAKEY_MISSED];

  protected $post;
  public $comment;
  public $name;
  protected $meta;
  public $image;

  public function __construct($post){
    $this->post = $post;
    $this->name = $post->post_title;
    $this->comment = $post->post_content;
    $this->image =$this->image();
  }

  public function image($dimension = 'medium'){
    if(!$this->has_image())
      return null;

    $tmp = wp_get_attachment_image_src( get_post_thumbnail_id( $this->post->ID ), $dimension );

    return $tmp[0];
  }

  public function has_image(){
    return has_post_thumbnail($this->post->ID);
  }

  public function __get($name){
    if(in_array($name, ['missed_days', 'missing', 'costs_euro', 'costs', 'date', 'image', 'costs_taxpayer'])){

      $this->load_meta();

      return $this->meta[$name];
    }

    return isset($this->post->$name) ? $this->post->$name : null;
  }

  const COST_PER_DAY_FOR_TAXPAYER = 100;
  protected function load_meta(){
    if(isset($this->meta))
      return ;

    foreach(self::$meta_keys as $key)
      $this->meta[$key] = get_post_meta($this->post->ID, $key, $single=true);

    $this->meta[self::KEY_MISSED_DAYS]    = self::map_missed_2_days($this->meta[self::METAKEY_MISSED], $this->meta[self::METAKEY_DATE]);
    $this->meta[self::KEY_COSTS_EURO]     = self::map_costs_2_euro($this->meta[self::METAKEY_COSTS]);
    $this->meta[self::KEY_COSTS_TAXPAYER] = $this->meta[self::KEY_MISSED_DAYS] * self::COST_PER_DAY_FOR_TAXPAYER;
  }

  public static function map_costs_2_euro($key){
    return preg_replace("/[^\d]/", "", $key);
  }

  public static function map_missed_2_days($days_from_form, $date_from=null){
    $days = $days_from_form;

    if($date_from){
      $a = strptime($date_from, '%d.%m.%Y');
      $add_days_since_timestamp =  mktime(0, 0, 0, $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
      $days+=round((time() - $add_days_since_timestamp) / 86400);
    }

    return $days;
  }

  public static function insert($name, $comment, $missing, $costs, $date, $image_index = null){
    $new_entry = array();
    $new_entry['post_title'] = esc_html($_POST['name']); unset($_POST['name']);
    $new_entry['post_content'] = esc_html($_POST['comment']); unset($_POST['comment']);
    $new_entry['post_status'] = 'draft';
    $new_entry['post_type'] = 'children';
    $new_entry['post_author'] = 0;

    $post_id = wp_insert_post($new_entry);

    update_post_meta($post_id, 'missing', $missing);
    update_post_meta($post_id, 'costs', $costs);
    update_post_meta($post_id, 'date', $date);

    if(!empty($image_index)
    && !empty($_FILES[$image_index]))
      $newupload = Util::insert_attachment($image_index, $post_id, $setthumb = true);

    return $post_id;
  }
}
