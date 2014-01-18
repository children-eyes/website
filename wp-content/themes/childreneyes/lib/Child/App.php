<?php

class Child_App{
  public static $form;

  public static function bootstrap(){

    add_action( 'init', 'Child_App::create_post_type' );

    self::$form = new Child_Form();

    if(!is_admin()){
      add_action( 'wp_enqueue_scripts', 'Child_App::enqueue_case_scripts_and_styles' );

      add_action( 'wp_loaded',[self::$form, 'dispatch'] );

      Shortcodes::bootsrap();
    }
    else{
      add_action( 'admin_init', 'Child_App::register_admin_formular_extension' );

      add_action('save_post_'.Child_Case::POST_TYPE_NAME, [self::$form, 'dispatch']);

      JQuery::ready("
                    var mask = $('<img src=\"/wp-content/themes/childreneyes/images/mask.png\"/>').css({position:'absolute', height:'98px', left:'12px', top: '49px'});
                    var cloned_link = $('#set-post-thumbnail').clone().html(mask);
                    $('#postimagediv').append(cloned_link);");

      JQuery::ready("$('.attachment-post-thumbnail').css({height: '98px'})");
      add_filter('admin_footer', function(){
        echo JQuery::ready();;
      });
    }
  }

  public static function enqueue_case_scripts_and_styles() {
    wp_enqueue_style( 'children-eyes',  get_template_directory_uri().'/css/childeyes.css' );

    wp_enqueue_style( 'font-raleway', 'http://fonts.googleapis.com/css?family=Raleway%3Ar%2Cb%2Ci|Open+Sans%3Ar%2Cb%2Ci' );
  }

  public static function create_post_type() {
    register_post_type( Child_Case::POST_TYPE_NAME,
      array(
        'labels' => array(
        'name' => __( 'Kinder Augen' ),
        'singular_name' => __( 'Augenpaar' )),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => Child_Case::POST_TYPE_NAME),
        'supports' => array('post-thumbnails', 'thumbnail', 'title', 'editor')//, 'custom-fields')
      )
    );
  }

  public static function register_admin_formular_extension(){
    $config = [
      'id'       => 'cases_post_extension',
      'title'    => 'Formular',
      'page'     => Child_Case::POST_TYPE_NAME,
      'context'  => 'normal',
      'priority' => 'default'
    ];
    add_meta_box(
              $config['id'],
              $config['title'],
              [self::$form, 'render'],
              $config['page'],
              $config['context'],
              $config['priority']);
  }
}
