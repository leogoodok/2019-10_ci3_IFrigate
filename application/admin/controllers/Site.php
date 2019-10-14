<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {

  /**
  * Перехватывает вызовы методов.
  * Проверяет авторизацию пользователя по данным из session и/или cookie.
  * Перенаправляет:
  *   - "Гостей" на страницу авторизации;
  *   - "Авторизованных пользователей" на страницу "Сообщений администратору"
  * @return mixed
   */
  public function _remap($method)
  {
    /**
     * Подключение библиотек
     */
    $this->load->helper('cookie');


    /**
     * Подключение моделей
     */
    $this->load->model('User');


    /**
     * Проверка авторизации пользователя по данным из session и/или cookie.
     * Перенаправление
     */
    if ($this->User->getIdentity()->isIdentity()) {
      if ($method === 'logout') {
        $this->User->logout();
        $action = 'login';
      } else if ($method !== 'login') {
        $this->$method();
        return;
      } else {
        $action = 'index';
      }
    } else {
      $action = 'login';
    }
    $this->$action();
  }


  /**
   * Формирует данные и отображает страницу "Сообщений администратору".
   * @return mixed
   */
  public function index()
  {
    /**
     * Массив настроек Представлений и контент страницы вставляемый в шаблон
     */
    $main = [
      'title' => 'Сообщения администратору',
      'header' => [
        'class' => 'bg-dark',
        'brandLabel' => 'Задание от ifrigate.ru (Backend)',
        'active_item' => 'message',
      ],
      'navbar' => [
        'is_identity' => false,
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
        'class' => 'container-fluid site-message',
        'params' => [],
      ],
      'footer' => [
        'class' => 'bg-dark',
        'link_github' => 'https://github.com/leogoodok/ci3_IFrigate',
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
    $this->load->helper('cookie');
    $this->load->helper(array('form', 'url'));
    $this->load->library('pagination');


    /**
     * Подключение моделей
     */
    $this->load->model('User');


    /**
    * Проверка авторизации пользователя по данным из session и/или cookie.
    */
    if (!empty($identity = $this->User->getIdentity()) && $identity->isIdentity()) {
      /**
       * Получение статуса авторизации и Логина пользователя
       */
      $main['navbar']['is_identity'] = $identity->isIdentity();
      $main['navbar']['username'] = $identity->getUsername();
      unset($identity);
    }


    /**
     * Подключение класса "TabMessage"
     */
    // load_class('TabMessage', 'models/db', '');
    require_once realpath(__DIR__.'/../../models/db/TabMessage.php');


    /**
     * Получение количества строк в таблице
     */
    $main['content']['params']['total_count'] = $this->db->count_all(TabMessage::tableName());
    $this->db->reset_query();


    /**
     * Создание Пагинатора и запись в переменную
     */
    $config['base_url'] = $this->config->config['base_url'].'site/index';
    // $config['total_rows'] = 200;//!!! из запросы БД - количество строк в таблице
    $config['total_rows'] = $main['content']['params']['total_count'];
    $config['per_page'] = 10;

    $config['uri_segment'] = 3;
    $config['num_links'] = 2;
    $config['reuse_query_string'] = true;

    //Настройки форматирования (перенести массив в файл, читать из него и объединять массивы)
    $config['first_link'] = 1;
    $config['first_tag_open'] = "\n\t\t\t\t\t<li class=\"page-item\">\n\t\t\t\t\t\t";
    $config['first_tag_close'] = "\n\t\t\t\t\t</li>";
    $config['last_link'] = round($config['total_rows'] / $config['per_page']);
    $config['last_tag_open'] = "\n\t\t\t\t\t<li class=\"page-item\">\n\t\t\t\t\t\t";
    $config['last_tag_close'] = "\n\t\t\t\t\t</li>\n\t\t\t\t";
    $config['prev_link'] = '<span aria-hidden="true">&laquo;</span>';
    $config['prev_tag_open'] = "\n\t\t\t\t\t<li class=\"page-item\">\n\t\t\t\t\t\t";
    $config['prev_tag_close'] = "\n\t\t\t\t\t</li>";
    $config['next_link'] = '<span aria-hidden="true">&raquo;</span>';
    $config['next_tag_open'] = "\n\t\t\t\t\t<li class=\"page-item\">\n\t\t\t\t\t\t";
    $config['next_tag_close'] = "\n\t\t\t\t\t</li>";
    $config['cur_tag_open'] = "\n\t\t\t\t\t<li class=\"page-item active\" aria-current=\"page\"><a class=\"page-link\" href=\"#\">";
    $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
    $config['num_tag_open'] = "\n\t\t\t\t\t<li class=\"page-item\">\n\t\t\t\t\t\t";
    $config['num_tag_close'] = "\n\t\t\t\t\t</li>";
    $config['attributes'] = array('class' => 'page-link');
    $config['attributes']['rel'] = FALSE;

    $this->pagination->initialize($config);
    $main['content']['pagination'] = $this->pagination->create_links();
    unset($config);


    /**
     * Получение POST-данных. Валидация.
     */
    if (!empty($post = $this->input->post()) && !empty($post['SiteMessageAction'])) {
      $num_delete = 0;
      /**
       * Запись новых статусов сообщений
       */
      if (!empty($post['SiteMessageAction']['status']) && is_array($post['SiteMessageAction']['status'])) {
        $result = true;
        $nameStatus = TabMessage::nameStatus();
        foreach ($post['SiteMessageAction']['status'] as $id => $status) {
          //ЕСЛИ: новый "статус" имеет допустимое значение, ТО: записать новый статус
          if (array_key_exists($status, $nameStatus)) {
            $param_update = ['status' => $status];
            if ($status != 0) {
              $param_update['updated_at'] = time();
            } else {
              $param_update['delete_at'] = time();
            }
            if (!$this->db->where('id', $id)->update(TabMessage::tableName(), $param_update)) {
              $result = false;
            }
            $this->db->reset_query();
          }
        }
        if ($result) {
          $this->session->set_flashdata($main['header']['active_item'], [['success' => 'Изменение статусов выбранных сообщений выполнено.']]);
        } else {
          $this->session->set_flashdata($main['header']['active_item'], [['danger' => 'Произошла ошибка в процессе изменения статусов сообщений.']]);
        }
        unset($result,$nameStatus,$param_update);
      }


      /**
       * Удаление сообщений
       */
      if (!empty($post['SiteMessageAction']['delete']) && is_array($post['SiteMessageAction']['delete'])) {
        $result = true;
        foreach ($post['SiteMessageAction']['delete'] as $id => $value) {
          //ЕСЛИ: Значение "1", ТО: удалить сообщение
          if (!empty($value)) {
            if (!$this->db->where('id', $id)->delete(TabMessage::tableName())) {
              $result = false;
            }
            $this->db->reset_query();
            $num_delete++;
          }
        }
        if ($result) {
          $flashdata = $this->session->flashdata($main['header']['active_item']);
          if (!empty($flashdata) && is_array($flashdata)) {
            $this->session->set_flashdata($main['header']['active_item'], array_merge($flashdata, [['success' => 'Удаление выбранных сообщений выполнено.']]));
          } else {
            $this->session->set_flashdata($main['header']['active_item'], [['success' => 'Удаление выбранных сообщений выполнено.']]);
          }
        } else {
          $flashdata = $this->session->flashdata($main['header']['active_item']);
          if (!empty($flashdata) && is_array($flashdata)) {
            $this->session->set_flashdata($main['header']['active_item'], array_merge($flashdata, [['danger' => 'Произошла ошибка в процессе удаления сообщений.']]));
          } else {
            $this->session->set_flashdata($main['header']['active_item'], [['success' => 'Произошла ошибка в процессе удаления сообщений.']]);
          }
        }
        unset($result,$flashdata);
      }
      unset($post);


      /**
       * Перезагрузка страницы, для сброса POST-параметров
       */
      $page = ($this->pagination->cur_page - 1) * $this->pagination->per_page;
      if ($page >= $main['content']['params']['total_count'] - $num_delete) {
        $page -= $this->pagination->per_page;
      }
      redirect("{$this->config->config['base_url']}site/index/$page", 'refresh');
    }


    /**
     * Для запроса в БД и строки
     * "Показаны сообщения <b>$begin-$end</b> из <b>$totalCount</b>."
     */
    $main['content']['params']['current_page'] = $this->pagination->cur_page;
    $main['content']['params']['page_size'] = $this->pagination->per_page;
    $main['content']['params']['begin'] = ($main['content']['params']['current_page'] - 1) * $main['content']['params']['page_size'] + 1;
    $main['content']['params']['end'] = $main['content']['params']['begin'] + $main['content']['params']['page_size'] - 1;
    if ($main['content']['params']['end'] > $main['content']['params']['total_count']) {
      $main['content']['params']['end'] = $main['content']['params']['total_count'];
    }


    /**
     * Создание экземпляра класса
     * Чтение из БД сообщений администратору,
     * для текущей страницы, с заданым количество записей на странице
     * и получение резутьтата как экземпляров класс "TabMessage"
     */
    $query = $this->db->from(TabMessage::tableName())
    ->limit($main['content']['params']['page_size'], $main['content']['params']['begin'] - 1)
    ->order_by('id', 'ASC')->get();

    foreach ($query->result('TabMessage') as $row) {
      $main['content']['data'][] = $row;
    }
    $this->db->reset_query();


    /**
     * Редеринг представления "message" в переменную
     * Редеринг заполненного "основного шаблона страницы"
     */
    $main['content']['body'] = $this->load->view('message', $main, true);
    $this->load->view('layouts/main', $main);
  }


  /**
   * Формирует данные и отображает страницу "Сообщений администратору".
   * @return mixed
   */
  public function login()
  {
    /**
     * Подключение конфигурации Google reCAPTCHA
     */
//     $reCaptcha = require realpath(__DIR__.'/../../config/GoogleReCaptcha.php');


    /**
     * Массив настроек Представлений и контент страницы вставляемый в шаблон
     */
    $main = [
      'title' => 'Авторизация',
      'header' => [
        'class' => 'bg-dark',
        'brandLabel' => 'Задание от ifrigate.ru (Backend)',
        'active_item' => 'login',
      ],
      'navbar' => [
        'is_identity' => false,
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
        'params' => [
          'min_length' => 3,
          'max_length' => 100,
        ],
        // 'captcha' => [
        //   'public_key' => $reCaptcha['v2_checkbox']['public_key'],
        //   'is_valid' => null,
        //   'error_msg' => '',
        // ],
      ],
      'footer' => [
        'class' => 'bg-dark',
        'link_github' => 'https://github.com/leogoodok/ci3_IFrigate',
      ],
    ];


    /**
     * Подключение библиотек
     */
    $this->load->helper('cookie');
    $this->load->helper(array('form', 'url'));
    $this->load->library('form_validation');
    $this->load->library('pagination');


    /**
     * Подключение моделей
     */
    $this->load->model('User');


    /**
     * Настройка и установка правил валидации.
     *
     * // WARNING: !!! Перенести в файл конфигурации !!!
     */
    $config_valid = [
      [
        'field' => "SiteLoginForm[username]",
        'label' => 'Ваш Логин',
        'rules' => [
          'trim',
          'required',
          "min_length[{$main['content']['params']['min_length']}]",
          "max_length[{$main['content']['params']['max_length']}]",
        ],
        'errors' => [
          'required' => 'Пожалуйста, заполните поле',
          'min_length' => "Не менее {$main['content']['params']['min_length']} символов",
          'max_length' => "Не более {$main['content']['params']['max_length']} символов",
        ],
      ],
      [
        'field' => "SiteLoginForm[password]",
        'label' => 'Пароль',
        'rules' => [
          'trim',
          'required',
          'alpha_numeric',
          "min_length[{$main['content']['params']['min_length']}]",
          "max_length[{$main['content']['params']['max_length']}]",
        ],
        'errors' => [
          'required' => 'Пожалуйста, заполните поле',
          'alpha_numeric' => 'Допускается только алфавитно-цифровые символы',
          'min_length' => "Не менее {$main['content']['params']['min_length']} символов",
          'max_length' => "Не более {$main['content']['params']['max_length']} символов",
        ],
      ],
      // [
      //   'field' => "g-recaptcha-response",
      //   'rules' => [
      //     'required',
      //   ],
      //   'errors' => [
      //     'required' => 'Пожалуйста, пройдите проверку',
      //   ],
      // ],
    ];
    $this->form_validation->set_rules($config_valid);


    /**
     * Получение POST-данных. Валидация.
     * Проверка корректности и актуальности кода Капчи
     */
    if (($main['validation']['status'] = $this->form_validation->run()) == true) {
      $post = $this->input->post();

      /**
       * Проверка reCAPTCHA
       */
      //...
//       require_once realpath(__DIR__.'/../../models/ReCaptcha.php');
// //       load_class('ReCaptcha', 'models', '');
//       $out = (new ReCaptcha())->verifyUserResponse($reCaptcha['v2_checkbox']['secret_key'], $post['g-recaptcha-response'], $post['g-recaptcha-user-ip']);


//       if ($out['status'] == 'ok' && isset($out['response']['success']) && $out['response']['success']) {
        /**
         * Успешная верификация каптчи
         */
//         $main['content']['captcha']['is_valid'] = true;
//         $main['content']['captcha']['error_msg'] = '';


      /**
       * Авторизация пользователя по Логину, Паролю
       */
      if ($is_identity = $this->User->loginUserByName($post['SiteLoginForm']['username'], $post['SiteLoginForm']['password'], $post['SiteLoginForm']['rememberMe'])) {
        /**
         * Успешная авторизация!
         * Переадресация на страницу "index"
         */
        redirect($this->config->config['base_url'].'site/index', 'refresh');
      }


      /**
       * Ошибка авторизации!
       * Запись сообщения в сессию.
       * Перезагрузка страницы, для сброса POST-параметров.
       */
      $this->session->set_flashdata($main['header']['active_item'], [['danger' => 'Отказ в авторизации: Неправильное имя пользователя или пароль.']]);
      redirect($this->config->config['base_url'].'site/login', 'refresh');



      // } else {
      //   /**
      //    * Ошибка верификации каптчи
      //    */
      //   $main['content']['captcha']['is_valid'] = false;
      //   $captcha_error = '';
      //   if (isset($out['response']['error-codes'])) {
      //     if (is_array($out['response']['error-codes'])) {
      //       $captcha_error = [];
      //       foreach ($out['response']['error-codes'] as $code) {
      //         $description = ReCaptcha::getErrorsDescription($code);
      //         if (isset($description)) {
      //           $captcha_error[] = $description;
      //         }
      //       }
      //     } else {
      //       $captcha_error = $out['response']['error-codes'];
      //     }
      //   }
      //   $main['content']['captcha']['error_msg'] = is_array($captcha_error) ? "<div>".implode("</div>\n<div>", $captcha_error)."</div>" : $captcha_error;
      //   $this->session->set_flashdata($main['header']['active_item'], [['danger' => 'Произошла ошибка при проверке "Я не робот!"']]);
      //
      //
      // }
    }


     /**
      * Редеринг представления "login" в переменную
      * Редеринг заполненного "основного шаблона страницы"
      */
     $main['content']['body'] = $this->load->view('login', $main, true);
     $this->load->view('layouts/main', $main);
  }
}
