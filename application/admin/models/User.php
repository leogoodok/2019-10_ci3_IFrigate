<?php
// namespace models\db;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Класс
 * @author "BigLeoGood"
 */
class User extends CI_Model
{
  /**
   * Свойства класса
   */
  public $enableAutoLogin = true;
  public $enableSession = true;
  public $identityCookie = ['name' => '_identity', 'httpOnly' => true];
  public $authTimeout;
  public $absoluteAuthTimeout;
  public $autoRenewCookie = true;
  public $idParam = '__id';
  public $authTimeoutParam = '__expire';
  public $absoluteAuthTimeoutParam = '__absoluteExpire';

  /**
   * Свойство содержит экземпляр класса Действий с таблицей "пользователей"
   * и в сойстве "$_attributes" экземпляр класса строки таблицы, который
   * заполняется после успешной авторизации пользователя
   */
  private $_identity;

  /**
   * Свойство содержит названия классов
   */
  private $_tableClass;
  private $_securityClass;
  private $_actionClass;


  /**
   * Конструктор класса
   */
  public function __construct($tableClass = 'TabUser', $directoryTableClass = 'models/db', $actionClass = 'ActionTabUser', $directoryactionClass = 'models/db', $securityClass = 'SecurityUser', $directorySecurityClass = 'models/db') {
    parent::__construct();

    $this->_tableClass = $tableClass;
    load_class($tableClass, $directoryTableClass);

    $this->_securityClass = $securityClass;
    load_class($securityClass, $directorySecurityClass);

    $this->_actionClass = $actionClass;
    load_class($actionClass, $directoryactionClass, ['tableClass' => $tableClass, 'securityClass' => $securityClass]);
    $class = $this->_actionClass;
    $this->_identity = new $class(['tableClass' => $tableClass, 'securityClass' => $securityClass]);
  }


  /**
   * Поискт пользователя в БД по Логину
   * Валидация пароля.
   * Авторизация пользователя.
   */
  public function loginUserByName($username, $password, $rememberMe) {
    if (($attributes = $this->_identity->findByUsername($username)) === null) {
      return false;
    }

    if (($valid = $this->_identity->validatePassword($attributes, $password)) === null) {
      return $valid;
    }

    return !($isGuest = $this->login($attributes, !empty($rememberMe) ? 30*24*3600 : 0));
  }


  /**
   * Возвращает объект идентификации связанный с вошедшим в данный момент пользователем.
   * Когда [[enableSession]] равен true этот метод может пытаться прочитать данные аутентификации пользователя сохраненные в сеансе и восстановить соответствующий объект идентификации если это не было сделано ранее.
   * @param bool $autoRenew стоит ли автоматически обновлять статус аутентификации если это не было сделано ранее.
   * Это полезно только когда [[enableSession]] имеет значение true.
   * @return TabUser|null объект идентификации связанный с вошедшим в данный момент пользователем.
   * `null` возвращается если пользователь не вошел в систему (не аутентифицирован).
   * @see login()
   * @see logout()
   */
  public function getIdentity($autoRenew = true) {
    if ($this->_identity->isIdentity() !== true) {
      if ($this->enableSession && $autoRenew) {
        try {
          $this->renewAuthStatus();
        } catch (\Exception $e) {
          throw $e;
        } catch (\Throwable $e) {
          throw $e;
        }
      }
    }
    return $this->_identity;
  }


  /**
   * Вход пользователя.
   *
   * После входа в систему пользователя:
   * - идентификационная информация пользователя доступна из [[_identity->getAttributes()]]
   *
   * Если [[enableSession]] равна `true`:
   * - идентификационная информация будет сохранена в сеансе и будет доступна в следующих запросах
   * - в случае `$duration == 0` пока сеанс остается активным или пока пользователь не закроет браузер
   * - в случае `$ duration> 0` до тех пор пока сеанс остается активным или пока cookie
   * остается действительным, `$duration` в секундах когда [[enableAutoLogin]] установлено в `true`.
   *
   * Если [[enableSession]] равна `false`:
   * - параметр `$duration` будет игнорироваться
   *
   * @param TabUser $identity идентификатор пользователя (который уже должен быть аутентифицирован)
   * @param int $duration количество секунд в течение которых пользователь может оставаться
   *                      в состоянии входа в систему по умолчанию `0`
   * @return bool вошел ли пользователь в систему
   */
  // public function login(TabUser $identity, $duration = 0)
  public function login($attributes, $duration = 0)
  {
    $this->switchIdentity($attributes, $duration);

    return !$this->getIsGuest();
  }


  /**
   * Вход пользователя по cookie.
   *
   * Этот метод пытается войти в систему используя идентификатор и информацию
   * authKey предоставленную в [[identityCookie|identity cookie]].
   */
  protected function loginByCookie()
  {
    $data = $this->getIdentityAndDurationFromCookie();
    if (isset($data['attributes'], $data['duration'])) {
      $attributes = $data['attributes'];
      $duration = $data['duration'];
      $this->switchIdentity($attributes, $this->autoRenewCookie ? $duration : 0);
    }
  }


  /**
   * Выйти для текущего пользователя.
   * Это удалит данные сеанса связанные с аутентификацией.
   * Если значение `$destroySession` равно true все данные сеанса будут удалены.
   * @param bool $destroySession следует ли уничтожить весь сеанс.  По умолчанию true.
   * Этот параметр игнорируется если [[enableSession]] равно false.
   * @return bool вышел ли пользователь из системы
   */
  public function logout($destroySession = true)
  {
    $this->switchIdentity(null);

    return $this->getIsGuest();
  }


  /**
   * Возвращает значение указывающее является ли пользователь гостем (не аутентифицирован).
   * @return bool является ли текущий пользователь гостем.
   */
  public function getIsGuest()
  {
    return $this->_identity->isIdentity();
  }


  /**
   * Возвращает значение которое уникально представляет пользователя.
   * @return string|int уникальный идентификатор для пользователя.  Если `null` это означает что пользователь является гостем.
   */
  public function getId()
  {
    return $this->_identity->getId();
  }


  /**
   * Обновляет идентификационный файл cookie.
   * Этот метод установит время истечения срока действия
   * идентификационного куки-файла как текущее время плюс
   * первоначально указанная продолжительность куки-файла.
   */
  protected function renewIdentityCookie()
  {
    $name = $this->identityCookie['name'];

    $cookie = $this->_identity->CI->input->cookie();
    $value = isset($cookie[$this->identityCookie['name']]) ? $cookie[$this->identityCookie['name']] : null;

    if ($value !== null) {
      $data = json_decode($value, true);
      if (is_array($data) && isset($data[2])) {

        $config = $this->_identity->CI->config->config;
        delete_cookie($this->identityCookie['name'], $config['cookie_domain'], $config['cookie_path']);

        $expire = time() + (int) $data[2];
        set_cookie($this->identityCookie['name'], $value, $expire, $config['cookie_domain'], $config['cookie_path'], $config['cookie_prefix'], $config['cookie_secure'], $config['cookie_httponly']);
      }
    }
  }


  /**
   * Устанавливает идентификационный файл cookie.
   * Этот метод используется когда [[enableAutoLogin]] имеет значение true.
   * Сохраняются [[id]], [[TabUser::getAuthKey()|auth key]], и duration в cookie.
   * @param TabUser $identity
   * @param int $duration количество секунд в течение которых пользователь может оставаться в состоянии входа в систему.
   * @see loginByCookie()
   */
  // protected function sendIdentityCookie($identity, $duration)
  protected function sendIdentityCookie($attributes, $duration)
  {
    $config = $this->_identity->CI->config->config;
    $value = json_encode([
      $attributes->getPrimaryKey(),
      $attributes->auth_key,
      $duration,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $expire = time() + $duration;
    set_cookie($this->identityCookie['name'], $value, $expire, $config['cookie_domain'], $config['cookie_path'], $config['cookie_prefix'], $config['cookie_secure'], $config['cookie_httponly']);
  }


  /**
   * Определяет имеет ли идентификационный файл cookie допустимый формат и содержит ли он действительный ключ авторизации.
   * Этот метод используется когда [[enableAutoLogin]] имеет значение true.
   * Этот метод пытается аутентифицировать пользователя используя информацию из файла cookie идентификации.
   * @return array|null Возвращает массив «identity» и «duration» если они действительны в противном случае - null.
   * @see loginByCookie()
   */
  protected function getIdentityAndDurationFromCookie()
  {
    $cookie = $this->_identity->CI->input->cookie();
    $value = isset($cookie[$this->identityCookie['name']]) ? $cookie[$this->identityCookie['name']] : null;

    if ($value === null) {
        return null;
    }

    $data = json_decode($value, true);
    if (is_array($data) && count($data) == 3) {
      list($id, $authKey, $duration) = $data;
      $attributes = $this->_identity->getAttributes($id);
      if ($attributes !== null) {
        if (!$attributes instanceof $this->_tableClass) {
          throw new InvalidValueException("Ответ должен быть объектом {$this->_tableClass}.");
        } elseif (!$this->_identity->validateAuthKey($authKey)) {
          return null;
        } else {
          return ['attributes' => $attributes, 'duration' => $duration];
        }
      }
    }

    $this->removeIdentityCookie();
    return null;
  }


  /**
   * Удаляет идентификационный файл cookie.
   * Этот метод используется когда [[enableAutoLogin]] имеет значение true.
   */
  protected function removeIdentityCookie()
  {
    $config = $this->_identity->CI->config->config;

    delete_cookie($this->identityCookie['name'], $config['cookie_domain'], $config['cookie_path']);
  }


  /**
   * Переключение на новую личность для текущего пользователя.
   *
   * Когда [[enableSession]] имеет значение true этот метод может использовать session
   * и/или cookie для хранения идентификационной информации пользователя в соответствии со
   * значением `$duration`. Пожалуйста обратитесь к [[login()]] для более подробной информации.
   *
   * Этот метод в основном вызывается [[login()]], [[logout()]] и [[loginByCookie()]] когда
   * текущего пользователя необходимо связать с соответствующей информацией об идентичности.
   *
   * @param TabUser|null $attributes идентификационная информация которая будет связана с
   * текущим пользователем. Если null это означает что текущий пользователь становится гостем.
   * @param int $duration количество секунд в течение которых пользователь может оставаться в состоянии
   * входа в систему. Этот параметр используется только тогда когда `$identity` не равно null.
   */
  public function switchIdentity($attributes, $duration = 0)
  {
    $this->_identity->setAttributes($attributes);

    if (!$this->enableSession) {
      return;
    }

    // Убедитесь что все существующие идентификационные куки удалены.
    if ($this->enableAutoLogin && ($this->autoRenewCookie || $attributes === null)) {
        $this->removeIdentityCookie();
    }

    $session = $this->_identity->CI->session;
    if ($session->has_userdata($this->idParam)) {
      $session->unset_userdata($this->idParam);
    }
    if ($session->has_userdata($this->authTimeoutParam)) {
      $session->unset_userdata($this->authTimeoutParam);
    }

    if ($attributes) {
      $session->set_userdata([$this->idParam => $attributes->getPrimaryKey()]);
      if ($this->authTimeout !== null) {
        $session->set_userdata([$this->authTimeoutParam => time() + $this->authTimeout]);
      }
      if ($this->absoluteAuthTimeout !== null) {
        $session->set_userdata([$this->absoluteAuthTimeoutParam => time() + $this->absoluteAuthTimeout]);
      }
      if ($this->enableAutoLogin && $duration > 0) {
        $this->sendIdentityCookie($attributes, $duration);
      }
    }
  }


  /**
   * Обновляет статус аутентификации используя информацию из сессии и cookie.
   *
   * Этот метод попытается определить личность пользователя используя переменную session [[idParam]].
   *
   * Если установлено [[authTimeout]] этот метод обновит таймер.
   *
   * Если идентификация пользователя не может быть определена сессией этот метод попытается
   * [[loginByCookie ()|войти по cookie]] если [[enableAutoLogin]] равно true.
   */
  protected function renewAuthStatus()
  {
    $session = $this->_identity->CI->session;
    $id = $session->has_userdata($this->idParam) ? $session->userdata($this->idParam) : null;

    if ($id === null) {
      $identity = null;
    } else {
      if (($identity = $this->_identity->getAttributes($id)) !== null) {
        // $this->_access = [];
      }
    }

    if ($identity && ($this->authTimeout !== null || $this->absoluteAuthTimeout !== null)) {
      $expire = $this->authTimeout !== null ? $session->userdata($this->authTimeoutParam) : null;
      $expireAbsolute = $this->absoluteAuthTimeout !== null ? $session->userdata($this->absoluteAuthTimeoutParam) : null;

      if (($expire !== null && $expire < time()) || ($expireAbsolute !== null && $expireAbsolute < time())) {
        $this->logout(false);
      } elseif ($this->authTimeout !== null) {
        $session->set_userdata([$this->authTimeoutParam => time() + $this->authTimeout]);
      }
    }

    if ($this->enableAutoLogin) {
      if ($this->getIsGuest()) {
        $this->loginByCookie();
      } elseif ($this->autoRenewCookie) {
        $this->renewIdentityCookie();
      }
    }
  }
}
