<?php

class Html_Form{
  protected $elements = [];
  protected $post_id = null;

  public function __construct(){
  }

  public function get_meta($post_id, $name, $default='', $single = true){
    if($post_id === null || empty($name))
      return $default;

    $val = get_post_meta($post_id, $name, $single);

    return $val === '' ? $default : $val;
  }

  protected $nounce_seed = null;
  protected $nounce_name = null;
  public function nounce($name, $seed, $params=[]){
    $this->nounce_seed = $seed;
    $this->nounce_name = $name.'_meta_box_nonce';
    $this->elements[]    = new Html_Element(
                                'input',
                                array_merge($params, ['type' =>'hidden', 'name' => $this->nounce_name, 'value' => wp_create_nonce($seed)]));
    return $this;
  }

  public function hidden($name, $value, $params){
    $this->elements[] = new Html_Element(
                                'input',
                                array_merge($params, ['type' =>'hidden', 'name' => $name, 'value' => $value]));
    return $this;
  }

  protected $multipart=false;
  public function image($name, $src='', $params=[]){
    #TODO
    /*echo '<input type="button" style="width: 200px;" value="Datei ausw&auml;hlen" ', $params, ' class="image-upload" ', '" id="', $field['id'].'_button', '" value="" style="width:97%" />';
          echo '<input type="text" id="'. $field['id'] .'_input" name="', $field['id'].'" value="'.$meta.'">';
          echo '<br />', $field['desc'];

          if($meta)
            echo '<p><img src="' . wp_get_attachment_url($meta) . '"/></p>';

          echo "<script type='text/javascript'>
                  jQuery(document).ready(function() {

                  var formfield;

                  jQuery('#{$field['id']}_button').click(function() {
                    jQuery('html').addClass('Image');
                    formfield = jQuery('#{$field['id']}_input').attr('name');
                    tb_show('', 'media-upload.php?type=image&TB_iframe=true');
                    return false;
                  });

                  // user inserts file into post. only run custom if user started process using the above process
                  // window.send_to_editor(html) is how wp would normally handle the received data

                  window.original_send_to_editor = window.send_to_editor;
                  window.send_to_editor = function(html){

                    if (formfield) {
                      fileurl = jQuery('img',html).attr('src');

                      jQuery('#{$field['id']}_input').val(fileurl);

                      tb_remove();

                      jQuery('html').removeClass('Image');

                    } else {
                      window.original_send_to_editor(html);
                    }
                  };

                });
              </script>";*/
  }

  public function file($name, $src='', $params=[]){
    $this->multipart = true;

    if(is_admin())
      add_action('post_edit_form_tag', function(){
        echo ' enctype="multipart/form-data"';
      });

    $this->elements[] = new Html_Element(
                                'input',
                                array_merge($params, ['type' => 'file', 'name' =>$name, 'src' => $src]));
    if(Util::is_image($src))
      $this->elements[] = new Html_Element(
                                'p', ['content' => '<img src="'.$src.'"/>']);
    return $this;
  }

  public function submit($value='Abschicken', $params=[]){
    $this->elements[] = new Html_Element(
                                'input',
                                array_merge($params, ['type' => 'submit', 'value' => $value]));
    return $this;
  }

  public function text($name, $value, $params=[]){
    $this->elements[] = new Html_Element(
                                'input',
                                array_merge($params, ['type' => 'text', 'name' => $name, 'value' => $value]));
    return $this;
  }

  public function radio($name, $value, $params=[]){
    $this->elements[] = new Html_Element(
                                'input',
                                array_merge($params, ['type' => 'radio', 'name' => $name, 'value' => $value]));
    return $this;
  }

  public function checkbox($name, $value, $params=[]){
    $this->elements[] = new Html_Element(
                                'input',
                                array_merge($params, ['type' => 'checkbox', 'name' => $name, 'value' => $value]));
    return $this;
  }

  public function textarea($name, $value, $params=[]){
    $this->elements[] = new Html_Element(
                                'textarea',
                                array_merge($params, ['name' => $name, 'content' => $value]));
    return $this;
  }

  public function select($name, $options, $selected, $params){
    $this->elements[] = new Html_Element(
                                'select',
                                array_merge($params, ['name' => $name]),
                                $options);
    return $this;
  }

  public function render($params, $post_id = null){
    if(!isset($params['method'])) $params['method'] = 'GET';
    if(!isset($params['action'])) $params['action'] = '';
    if(!isset($params['prefix'])) $params['prefix'] = '';
    if(!isset($params['suffix'])) $params['suffix'] = '';
    if(isset($params['name']))
      echo '<a name="'.$params['name'].'" title="anchor"></a>';

    echo $params['prefix'];

    if(!is_admin()){
      if($this->multipart)
        $params['enctype'] = 'multipart/form-data';

      echo '<form '.Util::array_2_attributes(array_diff_key($params, ['prefix' => '', 'suffix' => ''])).'>';
    }

    echo '<table class="form-table">';

    if(!isset($post_id)){
      global $post;
      if(isset($post))
        $post_id = $post->ID;
    }

    foreach($this->elements as $form_element){
      switch($form_element->tag){
        case 'select':
          $db_value = $this->get_meta($post_id, $form_element->name, $form_element->value);
          $form_element->content = '';

          foreach($form_element->extra as $key => $value){
            $option_element = new Html_Element(
                                          'option',
                                          [
                                            'content' => $value,
                                            'value'   => $key
                                          ]);
            if($db_value == $key)
              $option_element->selected = 'selected';

            $form_element->content.=''.$option_element;
          }
        break;
        case 'checkbox':
        case 'radio':
          throw new Error();

        break;
        default:
          $form_element->value = $this->get_meta($post_id, $form_element->name, $form_element->value);
      }

      echo '<tr><th>'.$form_element->description.'</th><td>'.$form_element.'</td></tr>';
    }

    echo '</table>';

    if(!is_admin())
      echo '</form>';

    echo $params['suffix'];
  }

  function dispatch($post_id){
    if ($this->nounce_seed
    && !wp_verify_nonce(@$_POST[$this->nounce_name], $this->nounce_seed))
      return $post_id;

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
      return $post_id;

    // check permissions
    if ('page' == $_POST['post_type'])
     if (!current_user_can('edit_page', $post_id))
        return $post_id;

    elseif(!current_user_can('edit_post', $post_id))
      return $post_id;


    $upload_file_index = null;
    foreach ($this->elements as $element) {
      if($element->tag == 'input'
      && $element->type == 'file'){
        $upload_file_index = $element->name;
        continue;
      }

      $old = $this->get_meta($post_id, $element->name, '', $single = true);
      $new = $_POST[$element->name];
      if ($new && $new != $old)
        update_post_meta($post_id, $element->name, $new);
      elseif ('' == $new && $old)
        delete_post_meta($post_id, $element->name, $old);
    }

    if($upload_file_index
    && !empty($_FILES[$upload_file_index])) {

      $file   = $_FILES[$upload_file_index];
      $upload = wp_handle_upload($file, array('test_form' => false));
      if(!isset($upload['error']) && isset($upload['file'])) {
        $filetype   = wp_check_filetype(basename($upload['file']), null);
        $title      = $file['name'];
        $ext        = strrchr($title, '.');
        $title      = ($ext !== false) ? substr($title, 0, -strlen($ext)) : $title;
        $attachment = array(
            'post_mime_type'    => $wp_filetype['type'],
            'post_title'        => addslashes($title),
            'post_content'      => '',
            'post_status'       => 'inherit',
            'post_parent'       => $post_id
        );

        $attach_key = $upload_file_index;
        $attach_id  = wp_insert_attachment($attachment, $upload['file']);
        $existing_download = (int) get_post_meta($post_id, $attach_key, true);

        if(is_numeric($existing_download))
          wp_delete_attachment($existing_download);

        update_post_meta($post_id, $attach_key, $attach_id);
      }
    }
  }
}
