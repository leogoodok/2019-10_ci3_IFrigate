<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {
  /**
  * Формирует данные и отображает страницу "Отправки сообщения администратору".
  * @return mixed
   */
  public function index()
  {
    /**
     * Массив настроек Представлений и контент страницы вставляемый в шаблон
     */
    $main = [
      'title' => 'Отправить сообщение администратору',
      'header' => [
        'class' => 'bg-dark',
        'brandLabel' => 'Задание от ifrigate.ru (Frontend)',
        'active_item' => 'message',
      ],
      'breadcrumbs' => [
        [
          'link' => $this->config->config['base_url'],
          'name' => 'Главная',
        ],
        [
          'active' => true,
        ],
      ],
      'content' => [
        'class' => 'container site-message',
        'params' => [],
      ],
      'footer' => [
        'class' => 'bg-dark',
        'link_github' => 'https://github.com/leogoodok',
      ],
    ];


    /**
     * NOTE: Создоние уведомлений, отображаемых в Представлении.
     *       Прим. Отображаются однократно, после перезагрузки страницы.
     *
     * 1. Новое(ые) уведомление(я) (Внимание. Затирает все уведомления, записанные ранее)
     *  $this->session->set_flashdata('view', array( array( 'type' => 'msg' ), [array] ) );
     * 2. Добавление уведомления(й) (Прим. Сохраняет все уведомления, записанные ранее)
     *  $this->session->set_flashdata('view', array_merge( $this->session->flashdata('view'), array( array( 'type' => 'msg' ), [array] ) ));
     *    Где:
     *      'view'  - Представление (Вид) в котором необходимо отобразить уведомление
     *      'type'  - Тип уведомления (список допустимых типов см. ниже)
     *      'msg'   - Содержание уведомления. Прим. можно передать HTML код.
     *
     * NOTE: Типы уведомлений:
     *      'primary' - основное уведомление
     *      'secondary' - дополнительное уведомление
     *      'success' - уведомление об успехе
     *      'danger' - уведомление об опасности
     *      'warning' - уведомление-предупреждение
     *      'info' - инфо-уведомление
     *      'light' - светлое уведомление
     *      'dark' - темное уведомление
     */


    /**
     * Подключение библиотек
     */
    $this->load->helper(array('form', 'url'));
    $this->load->library('form_validation');
    $this->load->library('email');
    //! Настройки 'email' в файле конфигурации "email.php"


    /**
     * Настройка и установка правил валидации
     */
     $config_valid = [
       [
         'field' => "SiteMessageForm[name]",
         'label' => 'Ваше Имя',
         'rules' => [
           'trim',
           'required',
           'min_length[3]',
           'max_length[100]',
         ],
         'errors' => [
           'required' => 'Пожалуйста, заполните поле',
         ],
       ],
       [
         'field' => "SiteMessageForm[email]",
         'label' => 'Email',
         'rules' => [
           'trim',
           'required',
           'valid_email',
         ],
         'errors' => [
           'required' => 'Пожалуйста, заполните поле',
         ],
       ],
       [
         'field' => "SiteMessageForm[subject]",
         'label' => 'Тема сообщения',
         'rules' => [
           'trim',
           'required',
           'min_length[3]',
         ],
         'errors' => [
           'required' => 'Пожалуйста, заполните поле',
         ],
       ],
       [
         'field' => "SiteMessageForm[body]",
         'label' => 'Содержание сообщения',
         'rules' => [
           'trim',
           'required',
         ],
         'errors' => [
           'required' => 'Пожалуйста, заполните поле',
         ],
       ],
     ];
     $this->form_validation->set_rules($config_valid);


    /**
     * Получение POST-данных. Валидация.
     */
    if (($main['validation']['status'] = $this->form_validation->run()) == true) {
      /**
       * Создание экземпляра класса, его заполнение
       */
      $post = $this->input->post();
      load_class('TabMessage', 'models/db', '');
      $tab_message = new TabMessage();
      $tab_message->status = 1;
      $tab_message->name = $post['SiteMessageForm']['name'];
      $tab_message->email = $post['SiteMessageForm']['email'];
      if (!empty($post['SiteMessageForm']['files'])) {
        $tab_message->number_attachment = count($post['SiteMessageForm']['files']);//!!!! ?????????
      }
      $tab_message->created_at = time();


      /**
       * Формирование сообщения администратору
       */
      $this->email->from('yii2advanced_noreply@wotskill.ru', 'WotSkill.ru email');
      $this->email->to('yii2advanced_admin@wotskill.ru');
      $this->email->reply_to($post['SiteMessageForm']['email'], $post['SiteMessageForm']['name']);
      $this->email->subject($post['SiteMessageForm']['subject']);
      $this->email->message(
        '<h3>Здравствуйте,</h3>'
        .$post['SiteMessageForm']['body']
        .'<p style="font-weight: bold;">С уважением,</p>'
        .'<p style="font-weight: bold;">'.$post['SiteMessageForm']['name'].'</p>'
        .'<p>Email: '.$post['SiteMessageForm']['email'].'</p>'
      );


      /**
       * Выполнение записи в БД и отправки Email сообщения
       */
      if ($this->db->insert(TabMessage::tableName(), $tab_message) && $this->email->send()) {
        $this->session->set_flashdata($main['header']['active_item'], [['success' => 'Благодарим Вас за обращение к нам. Мы ответим вам как можно скорее.']]);
      } else {
        $this->session->set_flashdata($main['header']['active_item'], [['danger' => 'Произошла ошибка в процессе отправки сообщения.']]);
      }
      unset($post,$tab_message);


      /**
       * Перезагрузка страницы, для сброса резутьтата валидации и POST-параметров
       */
      redirect($this->config->config['base_url'].'site/index', 'refresh');
    }


    /**
     * Редеринг представления "message" в переменную
     * Редеринг заполненного "основного шаблона страницы"
     */
    $main['content']['body'] = $this->load->view('message', $main, true);
    $this->load->view('layouts/main', $main);
  }
}
