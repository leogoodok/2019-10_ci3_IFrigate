<?php
// namespace models\db;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Класс ассоциированный с таблицей "message"
 * @author "BigLeoGood"
 */
class TabMessage // extends CI_Model
{
  /**
   * Свойства класса - поля таблицы
   */
  public $id;
  public $status;
  public $name;
  public $email;
  public $number_attachment;
  public $created_at;
  public $updated_at;
  public $delete_at;


  /**
   * @var array массив названий статусов сообщения
   */
  static protected $_nameStatus = [
    'Удалено',
    'Новое',
    'Прочитано',
    'Принято',
  ];


  /**
   * @return string название таблицы, сопоставленной с классом
   */
  public static function tableName()
  {
    // return '{{message}}';
    return 'message';
  }


  /**
   * @param int $index названия статуса сообщения
   * @return array|string|null
   *     1) $index не задан метод возвращает весь масив названий
   *     2) $index задан метод возвращает название $index статуса
   *        или null если название $index статуса не определено
   */
  static public function nameStatus($index = null)
  {
    return isset($index) && is_int($index)
      ? (array_key_exists($index, static::$_nameStatus) ? static::$_nameStatus[$index] : null)
      : static::$_nameStatus;
  }


  /**
   * @return string|null получить название статуса сообщения
   */
  public function getStatusText()
  {
    return $this->nameStatus((int)$this->status);
  }


  /**
   * @param string $format Шаблон вывода даты
   * @return string строковое представление даты создания сообщения
   */
  public function getCreatedDate($format = 'H:i d.m.Y')
  {
    return date($format, $this->created_at);
  }


  /**
   * @param string $format Шаблон вывода даты
   * @return string строковое представление даты создания обновления статуса
   */
  public function getUpdatedDate($format = 'H:i d.m.Y')
  {
    return date($format, $this->updated_at);
  }


  /**
   * @param string $format Шаблон вывода даты
   * @return string строковое представление даты удаления сообщения
   */
  public function getDeleteDate($format = 'H:i d.m.Y')
  {
    return date($format, $this->delete_at);
  }
}
