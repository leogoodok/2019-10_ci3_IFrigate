# ci3.wotskill.ru - GitHub репозиторий фреймворка CodeIgniter v3.1.11

## Подготовка CodeIgniter к использованию

### Установка дополнений

1. Пакет Русского языка
  - Скачать дополнение с "https://github.com/bcit-ci/codeigniter3-translations"
  - Скопировать из архива папку с нужным языком (russian).
  - Разместить в "~\system\language\russian\"


### Создание приложения CI3 для FRONTEND части приложения

#### Перенос файлов Фреймворка из WEB доступной директории

1. Создание WEB доступной директории "~\docs"
2. Перемещение "Входного скрипта" (~\index.php) в WEB доступную директорию,
  новый путь "~\docs\index.php"
3. Настройки путей к папкам Фреймворка в файле "~\docs\index.php".
  - Новый путь к папке SYSTEM:
```
$system_path = realpath(__DIR__.'/../system');
```
  - Новый путь к папке APPLICATION:
```
$application_folder = realpath(__DIR__.'/../application');
```
4. Добавление файла "~\docs\.htaccess"

5. !Внимание. Перед публикацией (размещением) приложения переключить его
  в режим "production", во входном скрипте страницы
```
// define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
define('ENVIRONMENT', 'production');
```

6. Примечание. Можно удалить файлы "index.html" из всех директорий Приложения, так как диступ из WEB к этим папкам невозможен.

#### Настройка Фреймворка

1. Редактирование файла конфигурации "~\application\config\config.php".
  - Установка базового URL сайта.
```
$protocol = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http';
$path = '';
$config['base_url'] = "$protocol://{$_SERVER['HTTP_HOST']}/$path";
unset($protocol,$path);
```
  - Удаление префикса пользовательских классов. Использовать пространства имен!
```
$config['subclass_prefix'] = '';
```
  - Установка ключа шифрования.
```
$config['encryption_key'] = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
```
  - Включение защиты от Подделки межсайтовых запросов
```
$config['csrf_protection'] = TRUE;
```
  - Изменение Языка по умолчанию
```
$config['language']	= 'russian';
```

2. Редактирование настроек подключения к БД в файле "~\application\config\database.php".

3. Настройка автозагрузки библиотек в файле "~\application\config\autoload.php".
```
$autoload['libraries'] = ['session', 'database', 'email'];
```

4. Настройка отправки Email. Создан файл с настройками "~\application\config\email.php"


### Создание второго приложения CI3, для BACKEND части приложения

#### Копирование файлов приложения

1. Создание в "~\application\" папки "~\application\admin\".
  Копирование в неё папок (со всем содержимым) из первого приложения "application".
```
~\application\admin\cache
~\application\admin\config
~\application\admin\controllers
~\application\admin\libraries
~\application\admin\logs
~\application\admin\models
~\application\admin\views
```

2. Создание в "~\docs\" папки "~\docs\admin\".
  Копирование в неё папок (со всем содержимым) из первого приложения "docs".
```
~\docs\admin\index.php
~\docs\admin\.htaccess
~\docs\admin\favicon.ico
```

#### Редактирование входного скрипта приложения

1. Настройки путей к папкам Фреймворка в файле "~\docs\admin\index.php".
  - Путь к папке SYSTEM:
```
$system_path = realpath(__DIR__.'/../../system');
```
  - Путь к папке APPLICATION:
```
$application_folder = realpath(__DIR__.'/../../application/admin');
```

2. !Внимание. Перед публикацией (размещением) приложения переключить его
  в режим "production"
```
// define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
define('ENVIRONMENT', 'production');
```

3. Примечание. Можно удалить файлы "index.html" из всех директорий Приложения, так как диступ из WEB к этим папкам невозможен.

#### Настройка Фреймворка

1. Редактирование файла конфигурации "~\application\admin\config\config.php".
  - Установка базового URL сайта.
```
$protocol = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http';
$path = 'admin/';
$config['base_url'] = "$protocol://{$_SERVER['HTTP_HOST']}/$path";
unset($protocol,$path);
```
  - Удаление префикса пользовательских классов. Использовать пространства имен!
```
$config['subclass_prefix'] = '';
```
  - Установка ключа шифрования.
```
$config['encryption_key'] = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
```
- Изменение пути сокетов, для устранения конфликтов с FRONTEND частью
```
$config['cookie_path']		= '/'.$path;
```
  - Включение защиты от Подделки межсайтовых запросов
```
$config['csrf_protection'] = TRUE;
```
  - Изменение Языка по умолчанию
```
$config['language']	= 'russian';
```

2. Редактирование настроек подключения к БД в файле "~\application\admin\config\database.php".

3. Настройка автозагрузки библиотек в файле "~\application\admin\config\autoload.php".
```
$autoload['libraries'] = ['session', 'database'];
```


## Создание "FRONTEND" части приложения

### Создание папок для подключаемых ресурсов и создание/копирование в них файлов
```
~\docs\assets\        - дополнительные ресурсы (js, css, ...)
~\docs\css\           - папка для CSS-файлов
~\docs\css\fonts.css  - подключаемые шрифты
~\docs\css\main.css   - CSS-стили "Основного шаблона страниц сайта"
~\docs\fonts\         - папка для шрифтов
~\docs\js\            - папка для JS-файлов
~\docs\js\main.js     - JS-скрипты "Основного шаблона страниц сайта"
```

### Создание Контроллера "Site".
    Размещено в файле "~\application\controllers\Site.php".

1. Назначение контроллера по умолчанию в файле "~\application\config\routes.php".
```
$route['default_controller'] = 'site';
```

2. Создание Действия "index" - Формирует данные и . Выполняет функционал:
  - Валидации входных POST данных.
  - Сохранение сообщений пользователя в БД.
  - Отправляет сообщение администратору по Email.
  - Рендерит страницу "Отправки сообщения администратору"

### Создание Класса (Модели) таблицы "message" БД.
    Размещено в файле "~\application\models\db\TabMessage.php".

### Создание Представления (Вида)

1. Создание папки шаблонов частей Представлений страниц.
    Размещено "~\application\views\layouts\".

2. Создание Основного шаблона всех страниц сайта.
    Размещено в файле "~\application\views\layouts\main.php".

3. Создание Представления "Отправить сообщение администратору".
    Размещено в файле "~\application\views\message.php".


## Создание "BACKEND" части приложения

### Создание папок для подключаемых ресурсов и создание/копирование в них файлов
```
~\docs\admin\assets\        - дополнительные ресурсы (js, css, ...)
~\docs\admin\css\           - папка для CSS-файлов
~\docs\admin\css\fonts.css  - подключаемые шрифты
~\docs\admin\css\main.css   - CSS-стили "Основного шаблона страниц сайта"
~\docs\admin\js\            - папка для JS-файлов
~\docs\admin\js\main.js     - JS-скрипты "Основного шаблона страниц сайта"
```

### Создание Контроллера "Site".
    Размещено в файле "~\application\admin\controllers\Site.php".

1. Назначение контроллера по умолчанию в файле "~\application\admin\config\routes.php".
```
$route['default_controller'] = 'site';
```

2. Создание Действия "index" - ...

### Создание Класса (Модели)...




### Создание Представления (Вида)

1. Создание папки шаблонов частей Представлений страниц.
    Размещено "~\application\admin\views\layouts\".

2. Создание Основного шаблона всех страниц сайта.
    Размещено в файле "~\application\admin\views\layouts\main.php".

3. Создание Представления "Сообщения администратору".
    Размещено в файле "~\application\admin\views\message.php".



...
