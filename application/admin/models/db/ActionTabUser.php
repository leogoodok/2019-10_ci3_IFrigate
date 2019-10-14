<?php
// namespace models\db;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Класс ассоциированный с таблицей "user"
 * @author "BigLeoGood"
 */
class ActionTabUser // extends CI_Model
{
  /**
   * Константы класса
   */
  const STATUS_DELETED = 0;
  const STATUS_INACTIVE = 9;
  const STATUS_ACTIVE = 10;

  /**
   * @property integer время действия токена сброса пароля
   */
  public $passwordResetTokenExpire = 3600;//==1*60*60 (1 час)
  /**
   * Выполнена идентификация ?
   * загружены данные в объект $this->_attributes
   */
  private $_is_identity;


  /**
   * Названия и экземпляры вспомогательных классов,
   * ссылка на объект Приложения
   */
  protected $_tableClass;
  private $_attributes;
  protected $_securityClass;
  private $_security;

  public $CI;


  /**
   * Конструктор класса
   */
  public function __construct($nameClasses = ['tableClass' => 'TabUser', 'securityClass' => 'SecurityUser'])
  {
    $this->_tableClass = $nameClasses['tableClass'];
    $class = $this->_tableClass;
    $this->_attributes = new $class();
    $this->_securityClass = $nameClasses['securityClass'];
    $class = $this->_securityClass;
    $this->_security = new $class();
    $this->CI =& get_instance();
  }


  /**
   * Получить статус идентификации пользователя (инициализации атрибутов)
   */
  public function isIdentity()
  {
    return $this->_is_identity;
  }


  /**
   * Получение значений атрибутов (класса "TabUser")
   * 'id' незадан -
   * по 'id' -
   */
  public function getAttributes($id = null)
  {
    if ($id === null) {
      return new $this->_tableClass();
    }

    if ($id == $this->_attributes->getPrimaryKey()) {
      return $this->_attributes;
    }

    if (($response = $this->findIdentity($id)) !== null) {
      $this->_is_identity = true;
      return $this->_attributes = $response;
    }

    return null;
  }


  /**
   * Установка значений атрибутов, Сбросс значений атрибутов,
   * Сохранение значений атрибутов в БД.
   * Установка статуса идентификации
   */
  public function setAttributes($attributes, $save = false)
  {
    if ($attributes instanceof $this->_tableClass) {
      $this->_attributes = $attributes;
      $this->_is_identity = true;
      if ($save) {
        $this->saveAttributes();
      }
    } else if ($attributes === null) {
      $this->resetAttributes();
      $this->_is_identity = false;
    } else {
      throw new InvalidValueException("Объект должен быть класса {$this->_tableClass}.");
    }
  }

  /**
   * Запись/перезапись данных в таблицу пользователей
   * Установка статуса идентификации
   */
  public function saveAttributes()
  {
    if (!empty($id = $this->_attributes->getPrimaryKey())) {
      if ($this->findIdentity($id) !== null) {
        //Перезапись
        return $this->CI->db->update($this->_tableClass::tableName(), $this->_attributes, ['id' => $id]);
      } else {
        $this->_attributes->resetPrimaryKey();
      }
    }
    //Запись
    return $this->CI->db->insert($this->_tableClass::tableName(), $this->_attributes);
  }


  /**
   * Сброс инициализации атрибутов
   * Сброс статуса идентификации
   */
  public function resetAttributes()
  {
    $this->_attributes = new $this->_tableClass();
    $this->_is_identity = false;

    return $this->isIdentity();
  }


  /**
   * Поиск в таблице данных пользователя по идентификатору
   *
   * @param int $id
   * @return TabUser|null
   */
  public function findIdentity($id)
  {
    $response = $this->CI->db
      ->where('id', $id)
      ->where('status', self::STATUS_ACTIVE)
      ->limit(1)
      ->get($this->_tableClass::tableName())
      ->result($this->_tableClass);
    if (!empty($response[0])) {
      return $response[0];
    }
    return null;
  }


  /**
   * Поиск в таблице данных пользователя по имени пользователя
   *
   * @param string $username
   * @return TabUser|null
   */
  public function findByUsername($username)
  {
    $response = $this->CI->db
      ->where('username', $username)
      ->where('status', self::STATUS_ACTIVE)
      ->limit(1)
      ->get($this->_tableClass::tableName())
      ->result($this->_tableClass);
    if (!empty($response[0])) {
      return $response[0];
    }
    return null;
  }


  /**
   * Находит пользователя по токену сброса пароля
   *
   * @param string $token токен сброса пароля
   * @return static|null
   */
  public static function findByPasswordResetToken($token)
  {
    if (!static::isPasswordResetTokenValid($token)) {
      return null;
    }

    $response = $this->CI->db
      ->where('password_reset_token', $token)
      ->where('status', self::STATUS_ACTIVE)
      ->limit(1)
      ->get($this->_tableClass::tableName())
      ->result($this->_tableClass);
    if (!empty($response[0])) {
      return $response[0];
    }
    return null;
  }


  /**
   * Выясняет является ли токен сброса пароля действительным
   *
   * @param string $token токен сброса пароля
   * @return bool
   */
  public static function isPasswordResetTokenValid($token)
  {
    if (empty($token)) {
      return false;
    }

    $timestamp = (int) substr($token, strrpos($token, '_') + 1);
    return $timestamp + $this->passwordResetTokenExpire >= time();
  }


  /**
   * Поиск в таблице данных пользователя по адресу Email
   *
   * @param string $email
   * @return TabUser|null
   */
  public function findByEmail($email)
  {
    $response = $this->CI->db
      ->where('email', $email)
      ->where('status', self::STATUS_ACTIVE)
      ->limit(1)
      ->get($this->_tableClass::tableName())
      ->result($this->_tableClass);
    if (!empty($response[0])) {
      return $response[0];
    }
    return null;
  }


  /**
   * Поиск в таблице данных пользователя по токену подтверждения электронной почты
   *
   * @param string $token
   * @return TabUser|null
   */
  public function findByVerificationToken($token)
  {
    $response = $this->CI->db
      ->where('verification_token', $token)
      ->where('status', self::STATUS_ACTIVE)
      ->limit(1)
      ->get($this->_tableClass::tableName())
      ->result($this->_tableClass);
    if (!empty($response[0])) {
      return $response[0];
    }
    return null;
  }


  /**
   * Получить логин авторизованного пользователя
   *
   * @return string|null - NULL если пользователь не авторизован
   */
  public function getUsername()
  {
    return $this->_attributes->username;
  }


  /**
   * Получить значение ID записи в таблице авторизованного пользователя
   *
   * @return int|null - NULL если пользователь не авторизован
   */
  public function getId()
  {
    return $this->_attributes->getPrimaryKey();
  }


  /**
   * Получить значение AuthKey авторизованного пользователя
   *
   * @return string|null - NULL если пользователь не авторизован
   */
  public function getAuthKey()
  {
      return $this->_attributes->auth_key;
  }


  /**
   * Валидация ключа авторизации
   *
   * @param string $authKey
   * @return bool
   */
  public function validateAuthKey($authKey)
  {
    return $this->getAuthKey() === $authKey;
  }


  /**
   * Валидация пароля
   *
   * @param string $password пароль для подтверждения
   * @return bool если пароль действителен для текущего пользователя
   */
  public function validatePassword($attributes, $password)
  {
    if (!empty($password) && $attributes instanceof $this->_tableClass && $attributes->password_hash !== null) {
      return $this->_security->validatePassword($password, $attributes->password_hash);
    }
    return null;
  }


  /**
   * Создает хэш пароля из пароля и устанавливает его для модели таблицы
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->_attributes->password_hash = $this->_security->generatePasswordHash($password);
  }


  /**
   * Генерирует ключ аутентификации "запомнить меня"
   */
  public function generateAuthKey()
  {
    $this->_attributes->auth_key = $this->_security->generateRandomString();
  }


  /**
   * Создает новый токен сброса пароля
   */
  public function generatePasswordResetToken()
  {
    $this->_attributes->password_reset_token = $this->_security->generateRandomString() . '_' . time();
  }


  public function generateEmailVerificationToken()
  {
    $this->_attributes->verification_token = $this->_security->generateRandomString() . '_' . time();
  }


  /**
   * Удаляет токен сброса пароля
   */
  public function removePasswordResetToken()
  {
    $this->_attributes->password_reset_token = null;
  }
}
