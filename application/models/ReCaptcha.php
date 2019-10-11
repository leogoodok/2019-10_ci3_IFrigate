<?php
// namespace models;
/**
 * Класс Google ReCaptcha
 * @author "BigLeoGood"
 */
class ReCaptcha
{
  /**
   * @var array массив кодов - описаний ошибок
   */
  protected static $_errorsDescription = [
    'missing-input-secret' => 'Параметр secret отсутствует',
    'invalid-input-secret' => 'Параметр secret является недопустимым или искаженным',
    'missing-input-response' => 'Параметр ответа отсутствует',
    'invalid-input-response' => 'Параметр response является недопустимым или искаженным',
    'bad-request' => 'Запрос является недействительным или искаженным',
    'timeout-or-duplicate' => 'Ответ больше не действителен: либо слишком стар, либо использовался ранее',
  ];

  /**
   * @var array curl options по умолчанию
   */
  protected $_defaultOptions = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FRESH_CONNECT => true,
    CURLOPT_HEADER => false,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_POST => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
  ];


  /**
   * Метод получения описания ошибок API AmoCRM
   * @param null|int $code - код ошибки
   * @return array|string|null
   */
  public static function getErrorsDescription($code = null)
  {
    if (isset($code)) {
      if (isset(static::$_errorsDescription[$code])) {
        return static::$_errorsDescription[$code];
      } else {
        return;
      }
    } else {
      return static::$_errorsDescription;
    }
  }


  /**
   * Метод выполнения cURL запроса в Google ReCaptcha API
   * @param string $secret - Общий ключ между вашим сайтом и reCAPTCHA
   * @param string $response - Маркер ответа пользователя, предоставляемый
   *                           клиентской интеграцией reCAPTCHA на сайте
   * @param null|string $remoteip - массив опций сеанса cURL, дополнительно
   *                              к устанавливаемым по умолчанию
   * @param array $curlOptions - массив опций сеанса cURL, дополнительно
   *                              к устанавливаемым по умолчанию
   * @return array
   *  [
   *    'status'    - ('ok'|'error') Статус выполнения запроса
   *    'response'  - (null|array) Ответ сервера, если он есть
   *      [
   *        'success',      - (bool) true|false Статус выполнения проверка Каптчи
   *        'challenge_ts', - (int) timestamp of the challenge load
   *                                 (ISO format yyyy-MM-dd'T'HH:mm:ssZZ)
   *        'hostname'      - (string) the hostname of the site where the reCAPTCHA was solved
   *        'error-codes'   - (array) optional
   *      ]
   *    'error'     - (null|array) Информация об ошибке, если она есть
   *      [
   *        'code',    - (int) код ошибки
   *        'message'  - (string) описание ошибки
   *      ]
   *  ]
   */
  public function verifyUserResponse($secret, $response, $remoteip = null, $curlOptions = null)
  {
    //Формирование массива опций сеанса cURL
    $options = $this->_defaultOptions;
    if (!empty($curlOptions) && is_array($curlOptions)) {
      $options = array_replace($options, $curlOptions);
    }

    //Добавление значений POST-параметров
    $post_params = [
      'secret' => $secret,
      'response' => $response,
    ];
    if (isset($remoteip)) {
      $post_params['remoteip'] = $remoteip;
    }
    $options = array_replace($options, [CURLOPT_POSTFIELDS => http_build_query($post_params),]);

    //Инициализация запроса к серверу.
    if ($ch = curl_init()) {
      //Установка параметров cURL
      curl_setopt_array($ch, $options);
      //Выполнение запроса
      $data = curl_exec($ch);
      //HTTP-код ответа сервера
      $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      //Завершение сеанса cURL
      curl_close($ch);
      //Обработка ответа сервера
      $code = (int)$code;
      try {
        //Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
        if ($code != 200 && $code != 204) {
          $description = $this->getErrorsDescription($code);
          throw new \Exception(isset($description) ? $description : 'Undescribed error', $code);
        }
      } catch (\Exception $e) {
        return [
          'status' => 'error',
          'error' => [
            'code' => $e->getCode(),
            'message' => 'Ошибка: '.$e->getMessage().PHP_EOL,
          ],
        ];
      }
      if (!empty($data)) {
        if ($result = json_decode($data, true)) {
          return [
            'status' => 'ok',
            'response' => $result,
          ];
        }
        return [
          'status' => 'error',
          'error' => [
            'code' => 2,//??? определить "пользовательские" коды ошибок
            'message' => 'Ошибка: Преодразования JSON ответа сервера',
          ]
        ];
      }
      return [
        'status' => 'error',
        'error' => [
          'code' => 1,//??? определить "пользовательские" коды ошибок
          'message' => 'Ошибка: Получен пустой ответ сервера',
        ]
      ];
    }
    return [
      'status' => 'error',
      'error' => [
        'code' => 0,//??? определить "пользовательские" коды ошибок
        'message' => 'Ошибка: Инициализация cURL запроса к серверу',
      ],
    ];
  }
}
