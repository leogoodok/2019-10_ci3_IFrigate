<?php
// namespace models\db;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Класс ассоциированный с таблицей "user"
 * @author "BigLeoGood"
 */
class TabUser
{
  /**
   * Свойства класса - поля таблицы
   */
  public $id;
  public $username;
  public $auth_key;
  public $password_hash;
  public $password_reset_token;
  public $email;
  public $status;
  public $created_at;
  public $updated_at;
  public $verification_token;


  /**
   * @return string название таблицы, сопоставленной с классом
   */
  public static function tableName()
  {
    // return '{{%user}}';
    return 'user';
  }


//!!! добавить метод получения названия первичного ключа !!!


  /**
   * @return int значение первичного ключа
   */
  public function getPrimaryKey()
  {
    return $this->id;
  }


  /**
   * Сброс значения первичного ключа
   */
  public function resetPrimaryKey()
  {
    $this->id = null;
  }
}
