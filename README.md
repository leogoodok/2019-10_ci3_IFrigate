#  Задание.

1. Установить фреймворк CodeIgniter 3.x.x.
2. Создать модуль обратной связи, включающий в себя администраторскую и пользовательскую части.
3. Пользовательская часть:
	- Форма отправки сообщений администратору с полями: имя, email, заголовок, текст сообщения, каптча;
4. Администраторская часть:
	- форма входа для доступа к управлению сообщениями по логину/паролю;
	- постраничный список принятых сообщений в виде таблицы с колонками: дата отправки, имя, email, статус;
	- функция удаления сообщений из списка;
	- функция просмотра и изменения статуса сообщения (возможные статусы: новое, прочитано, принято);
5. Требования к реализации:
	- отправка сообщений должна происходить средствами jQuery AJAX;
	- пользовательские данные должны валидироваться средствами фреймворка и выводиться на форме отправки в случае наличия ошибок;
	- полученное сообщение пользователя необходимо сохранять в БД (MySQL), плюс, отправлять на email админа средствами фреймворка в формате HTML;
	- аккуратное оформление интерфесов;
	- комментирование php-кода по стандарту php-doc.


# Выполнение задания

Размещено:

Frontend - http://ci3ifrigate.wotskill.ru/

Backend  - http://ci3ifrigate.wotskill.ru/admin/

Ход выполнения см. в README_GitHub.md

Пользователь для авторизации.

Логин: wotskill

Пароль: x6Aj5bQ7xw


## Структура приложения

### FRONTEND часть

#### Web Доступная директория: "~\docs\"
```
Входной скрипт: ~\docs\index.php
Подключаемые ресурсы: ~\docs\assets\
CSS файлы: ~\docs\admin\css\
JS файлы: ~\docs\admin\js\
Шрифты: ~\docs\fonts\
```

#### Контроллеры: "~\application\controllers\"
```
~\application\controllers\Site.php – контроллер Site
```

#### Модели: "~\application\models\"
```
~\application\models\db\TabMessage.php – модель таблицы БД
```

#### Представления: "~\application\views\"
```
~\application\views\layouts\main.php – основной шаблон страниц
~\application\views\message.php – отправить сообщение администратору
```

### BACKEND часть

#### Web Доступная директория: "~\docs\admin\"
```
Входной скрипт: ~\docs\admin\index.php
Подключаемые ресурсы: ~\docs\admin\assets\
CSS файлы: ~\docs\admin\admin\css\
JS файлы: ~\docs\admin\admin\js\
```

#### Контроллеры: "~\application\admin\controllers\"
```
~\application\admin\controllers\Site.php – контроллер Site
```

#### Модели: "~\application\admin\models\"
```
~\application\models\db\TabMessage.php – модель таблицы БД
```

#### Представления: "~\application\admin\views\"
```
~\application\admin\views\layouts\main.php – основной шаблон страниц
~\application\admin\views\message.php – сообщения администратору
```

# Описание и инструкции

## Просмотр сообщений администратору

Все принятые сообщения отображаются в таблице с постраничным разделением. \
Содержание колони таблицы "Статус" изменяется в зависимости от статуса сообщения. \
Примечание. В колонке "Статус" для сообщений со статусом "удаленные" отображается текст, \
а для остальных статусов - элемент выбора нового статуса.

## Инструкции

### Инструкция пользователю по отправке сообщения администратору.

Примечание. Отправить сообщение администратору может любой пользователь, не обязательно авторизованный.

Откройте любую страницу сайта. \
В главном меню нажмите кнопку "Отправить сообщение". \
В открывшейся странице авторизации заполните обязательные поля:
- Имя;
- Email;
- Тема сообщения;
- Содержание сообщения;

Нажмите кнопку отправить сообщение

### Инструкция по просмотру принятых сообщений, изменению их статуса и удалению

Откройте любую страницу backend части сайта. \
Все принятые сообщения отображаются в таблице с постраничным разделением.

Изменение статуса сообщения осуществляется выбором нового статуса в раскрывающемся списке колонки "Статус". \
Примечание. Изменить статус сообщения "Удалено" нельзя! \
Удаление сообщений выполняется кликом на кнопке "крестик" колонки "Удалить".

Внимание. Фиксация изменений статусов и удаления сообщений осуществляется нажатием кнопки "Сохранить изменения статусов сообщений и удалить выбранные сообщения".
