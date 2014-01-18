<?php

class Child_Form extends Html_Form{
  protected $form_config = [];
  public function __construct(){
    $this->session = new Session('case');

    $this->form_config = [
      'method' => 'POST',
      'action' => '/?submit=case',
      'name'   => 'childrenform'
    ];

    $this->text('date', date('d.m.Y'), ['readonly' => 'readonly', 'description' => 'Datum']);

    if(!is_admin())
      $this->text('name', '', ['style' => 'min-width:200px;', 'description' => 'Name des Kindes']);

    $this->select('missing', [
                          6*30  => 'ca. 6 Monate',
                          12*30  => 'ca. 1 Jahr',
                          2*12*30  => 'ca. 2 Jahre',
                          3*12*30  => 'ca. 3 Jahre',
                          4*12*30  => 'ca. 4 Jahre',
                          5*12*30  => 'ca. 5 Jahre',
                          6*12*30  => 'mehr als 5 Jahre'],
                          null,
                  ['description' => 'Wie lange von der Familie getrennt']);

    $this->select('costs', [
                          '0'   => 'keine Kosten',
                          '1000'   => 'bis 1.000 € ',
                          '2500'   => 'bis 2.500 €',
                          '5000'   => 'bis 5.000 €',
                          '10000'  => 'bis 10.000 €',
                          'g10000' => 'mehr als 10.000 €',
                          'g50000' => 'mehr als 50.000 €' ],
                          null,
                  ['description' => 'Anwaltskosten bisher']);

    if(!is_admin())
      $this->file('eyes', null, ['description' => 'Bild der Augen', 'size' => '50', 'maxlength'=>'100000', 'accept'=>'image/*'])
           ->textarea('comment', '', [
                              'maxlength' => 200,
                              'style'=>'min-width:200px; min-height:120px; width:97%;',
                              'description' => 'Kurz-Kommentar'])
           ->submit();
  }

  public function render(){
    if(isset($_REQUEST['msg'])){
      switch($_REQUEST['msg']){
        case 'ok':
          $this->form_config['prefix'] = '<div class="msg ok"> Vielen Dank! Es wird einige Zeit brauchen, bis wir alles geprüft haben. </div>';
        break;
        case 'wait':
          $this->form_config['prefix'] = '<div class="msg wait"> Bitte warten: <span class="countdown">'.$_REQUEST['until'].'<span></div>';
        break;
        case 'wait_ok':
          $this->form_config['prefix'] = '<div class="msg ok"> Vielen Dank! Es wird einige Zeit brauchen, bis wir alles geprüft haben. </div>'.
                                         '<div class="msg wait"> Bitte warten: <span class="countdown">'.$_REQUEST['until'].'<span></div>';
        break;
        case 'missing_name':
          $this->form_config['prefix'] = '<div class="msg error"> Bitte wenigstens den Namen angeben. Danke. </div>';
        break;
        default:
          $this->form_config['prefix'] = '<div class="msg error"> Entschuldigung! Es ist ein Fehler aufgetreten. </div>';
      }
    }
    return parent::render($this->form_config);
  }

  public function dispatch($post_id){
    if(!is_admin())
      return $this->dispatch_user_submitted($post_id);

    return parent::dispatch($post_id);
  }

  const FORM_ANCHOR = 'childrenform';
  public function redirect($path){
    header('Location: '.$path.'#'.self::FORM_ANCHOR);
    die();
  }

  const SUBMIT_DELAY = 600;//seconds = 10minutes
  public function do_session_time_check_and_redirect(){
    if(!is_admin()
    && !isset($_REQUEST['until'])
    && isset($this->session->user_posted_case)
    && $this->session->user_posted_case + self::SUBMIT_DELAY > time())
      $this->redirect('/submit?msg=wait&until='.($this->session->user_posted_case + self::SUBMIT_DELAY));
  }

  public function dispatch_user_submitted($post_id){
    if(substr_count($_SERVER['REQUEST_URI'], '/submit'))
      $this->do_session_time_check_and_redirect();

    if(!isset($_REQUEST['submit'])
    || $_REQUEST['submit'] != 'case')
      return ;

    if(!is_admin()
    && empty($_POST['name']))
      $this->redirect('/submit?msg=missing_name');

    $this->session->user_posted_case = time();

    Child_Case::insert(esc_html($_POST['name']),
                 esc_html($_POST['comment']),
                 esc_html($_POST['missing']),
                 esc_html($_POST['costs']),
                 date('d.m.Y'),
                 'eyes');

    $this->redirect('/submit?msg=wait_ok&until='.($this->session->user_posted_case + self::SUBMIT_DELAY));
  }
}
