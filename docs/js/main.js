/**
 * Обёртка jQuery для избежания конфликтов  при использовании
 * псевдонима $ с другими JS библиотеками
 */
jQuery(function($){

  /**
   * Функция перевода текста из Английской раскладки клавиатуры в Русскую и наоборот
   * @param string str - переводимая Настройка
   * @param null|false|true direction - направление перевода
   *        (true)        -  из Английского в Русский
   *        (null|false)  -
   * @return string
   *
   * @example Перевод с Русской раскладки клавиатуры в Английскую
   *  if(/[^a-z]+/i.test(str)){
   *    str = AutoSwitchKeyboardLang(str);
   *  }
   * @example Перевод с Английской раскладки клавиатуры в Русскую
   *  if(/[^а-я]+/i.test(str)){
   *    str = AutoSwitchKeyboardLang(str, true);
   *  }
   */
  function AutoSwitchKeyboardLang(str, direction) {
        //Массивы соответствия символов раскладок клавиатуры
    var rus = ["й","ц","у","к","е","н","г","ш","щ","з","х","ъ","ф","ы","в","а","п","р","о","л","д","ж","э","я","ч","с","м","и","т","ь","б","ю",'"',"№",";",":","?"],
        eng = ["q","w","e","r","t","y","u","i","o","p","[","]","a","s","d","f","g","h","j","k","l",";","'","z","x","c","v","b","n","m",",",".","@","#","$","^","&"],
        //Преобразовать из
        from_lang = direction ? eng : rus,
        //Преобразовать в
        to_lang = direction ? rus : eng;

    //Цикл по символам в строке
    for(var j = 0; j < from_lang.length; j++) {
          //Шаблон поиска с Экранированнием спец.символов
      var template = ("["===from_lang[j] || "\\"===from_lang[j] || "^"===from_lang[j] || "$"===from_lang[j] || "."===from_lang[j] || "|"===from_lang[j] || "?"===from_lang[j] || "*"===from_lang[j] || "+"===from_lang[j] || "("===from_lang[j] || ")"===from_lang[j]) ? ("\\" + from_lang[j]) : from_lang[j],
          re = new RegExp(template, "mig" );

      str = str.replace(re, function(x){
          return x == x.toLowerCase() ? to_lang[j] : to_lang[j].toUpperCase()
      })
    }
    return str
  }


  /**
   *  Выполнить после построения страницы
   */
  $(document).ready(function () {
    /**
     *  Скрипт кнопки прокрутки страницы вверх
     */
    $(window).scroll(function(){
      if($(this).scrollTop()!=0){
        $("#butToTop").fadeIn();
      }else{
        $("#butToTop").fadeOut();
      }
    });
    $("#butToTop").click(function(){
      $("body,html").animate({scrollTop:0},800);
    });


    /**
     *  Назначение обработчика события ввода символа в input "Ваше Имя*"
     */
    $('#site-message-form-name').on('input', function() {
      var value = $(this).val();

      //Удаление не разрешенных символов
      if(/[^\da-zа-я_ \-]+/i.test(value)){
        value = value.replace(/[^\da-zа-я_ \-]+/ig,'');
      }

      //Удаление более одного "пробела" подряд
      if(/[ ]{2,}/.test(value)){
        value = value.replace(/[ ]{2,}/g,' ');
      }

      //Проверка и ограничение максимальной длинны (не более ...)
      if (value.length > $(this).data('max_length')) {
        value = value.substr(0, $(this).data('max_length'));
      }

      //Запись нового значения
      $(this).val(value);
    });


    /**
     *  Назначение обработчика события потери фокуса input-а "Ваше Имя*"
     */
    $('#site-message-form-name').blur( function() {
      var value = $(this).val();

      //Скрытие панели оповещения, если она отображается
      if (!($($(this).data('targetResult')).css('none'))) {
        $($(this).data('targetResult')).slideUp().html('');
      }


      //Валидация (не менее ... символов)
      if (value.length < $(this).data('min_length')) {
        $(this).attr('aria-invalid', 'true');
        $(this).removeClass('is-valid').addClass('is-invalid');
        if (value.length) {
          $($(this).data('targetError')).text('В поле должно быть не менее ' + $(this).data('min_length') + ' символов');
        } else {
          $($(this).data('targetError')).text('Пожалуйста, заполните поле');
        }
      } else {
        $(this).removeAttr('aria-invalid');
        $(this).removeClass('is-invalid').addClass('is-valid');
      }
    });


    /**
     *  Назначение обработчика события ввода символа в input "Email*"
     */
    $('#site-message-form-email').on('input', function() {
      var value = $(this).val();

      //Перевод с Русской раскладки клавиатуры в Английскую, если необходимо
      if(/[^a-z]+/i.test(value)){
        value = AutoSwitchKeyboardLang(value);
      }

      //Удаление НЕ разрешенных символов
      if(/[^\w\-\.@]+/i.test(value)){
        value = value.replace(/[^\w\-\.@]+/ig,'');
      }

      //Запись нового значения
      $(this).val(value);
    });


    /**
     *  Назначение обработчика события потери фокуса input-а "Email*"
     */
    $('#site-message-form-email').blur( function() {
      var re = /^[a-z0-9_\-\.]+@[a-z0-9\-]+\.([a-z]{1,6}\.)?[a-z]{2,6}$/i,
          value = $(this).val();

      //Скрытие панели оповещения, если она отображается
      if (!($($(this).data('targetResult')).css('none'))) {
        $($(this).data('targetResult')).slideUp().html('');
      }

      //Валидация
      if (value.length) {
        if (value.search(re) == 0) {
          $(this).removeAttr('aria-invalid');
          $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
          $(this).attr('aria-invalid', 'true');
          $(this).removeClass('is-valid').addClass('is-invalid');
          $($(this).data('targetError')).text('Некорректный адрес электронной почты');
        }
      } else {
        $(this).attr('aria-invalid', 'true');
        $(this).removeClass('is-valid').addClass('is-invalid');
        $($(this).data('targetError')).text('Пожалуйста, заполните поле');
      }
    });


    /**
     *  Назначение обработчика события ввода символа в input "Тема сообщения*"
     */
    $('#site-message-form-subject').on('input', function() {
      var value = $(this).val();

      //Удаление не разрешенных символов
      if(/[^\da-zа-я_ \-]+/i.test(value)){
        value = value.replace(/[^\da-zа-я_ \-]+/ig,'');
      }

      //Удаление более одного "пробела" подряд
      if(/[ ]{2,}/.test(value)){
        value = value.replace(/[ ]{2,}/g,' ');
      }

      //Запись нового значения
      $(this).val(value);
    });


    /**
     *  Назначение обработчика события потери фокуса input-а "Тема сообщения*"
     */
    $('#site-message-form-subject').blur( function() {
      var value = $(this).val();

      //Скрытие панели оповещения, если она отображается
      if (!($($(this).data('targetResult')).css('none'))) {
        $($(this).data('targetResult')).slideUp().html('');
      }

      //Валидация (не менее ... символов)
      if (value.length < $(this).data('min_length')) {
        $(this).attr('aria-invalid', 'true');
        $(this).removeClass('is-valid').addClass('is-invalid');
        if (value.length) {
          $($(this).data('targetError')).text('В поле должно быть не менее ' + $(this).data('min_length') + ' символов');
        } else {
          $($(this).data('targetError')).text('Пожалуйста, заполните поле');
        }
      } else {
        $(this).removeAttr('aria-invalid');
        $(this).removeClass('is-invalid').addClass('is-valid');
      }
    });


    /**
     *  Назначение обработчика события потери фокуса textarea "Содержание сообщения*"
     */
    $('#site-message-form-body').blur( function() {
      var value = $(this).val();

      //Скрытие панели оповещения, если она отображается
      if (!($($(this).data('targetResult')).css('none'))) {
        $($(this).data('targetResult')).slideUp().html('');
      }

      //Удаление "пробельных" символов
      if(/[\s]+/.test(value)){
        value = value.replace(/[\s]+/g,'');
      }

      //Валидация (не пустое поле, без пробельных символов)
      if (value.length) {
        $(this).removeAttr('aria-invalid');
        $(this).removeClass('is-invalid').addClass('is-valid');
      } else {
        $(this).attr('aria-invalid', 'true');
        $(this).removeClass('is-valid').addClass('is-invalid');
        $($(this).data('targetError')).text('Пожалуйста, заполните поле');
      }
    });


    /**
     *  Назначение обработчика события клика кнопки "Отправить сообщение"
     */
    $('#site-message-form-button_submit').click( function() {
      var form = $(this).parents('form'),
//           form_params = {},
          valid_all = true;

      //Скрытие панели оповещения, если она отображается
      if (!($($(this).data('targetResult')).css('none'))) {
        $($(this).data('targetResult')).slideUp().html('');
      }

      //Проверка выполнена ли валидация полей формы
      form.find('input[aria-required], textarea[aria-required]').each(function(i,elem) {
        if ($(this).attr('aria-invalid')) {
          valid_all = false;
          // $(this).removeClass('is-valid').addClass('is-invalid').blur();
          $(this).blur();
        }
      });

      //Проверка заполнения Капчи
      var parent_captcha = form.find('div.g-recaptcha').parent();
      if (!grecaptcha.getResponse()) {
        valid_all = false;
        parent_captcha.removeClass('is-valid').addClass('is-invalid');
        $($(parent_captcha).data('targetError')).text('Пожалуйста, пройдите проверку');
      } else {
        parent_captcha.removeClass('is-invalid').addClass('is-valid');
      }
      if (!valid_all) return;

      //Отправка формы с перезагрузкой страницы
      // form.submit();
      // return;

      //Отправка формы POST запросом
      var str_url = window.location.protocol + '//' + window.location.hostname + '/index.php/site/ajax/';
      $.post(
        str_url,
        // form_params,
        form.serialize(),//Сериализация формы
        function(response) {
          var result = $.parseJSON(response),
              target_result = '#site-message-result_submit',
              target_form = '#site-message-form',
              message = (result.message) ? result.message : 'Ошибка',
              errors = (result.errors) ? result.errors : '';
          if (result.status == 'ok') {
            $(target_result).removeClass('alert-danger').addClass('alert-success').html(message);
            $(target_form).find('input[aria-required], textarea[aria-required]').each(function(i,elem) {
              $(this).removeClass('is-valid');
              $(this).attr('aria-invalid', 'true');
              $(this).val('');
            });
            $(target_form).find('input[type="file"]').val('');
            grecaptcha.reset();
          } else {
            $(target_result).removeClass('alert-success').addClass('alert-danger').html("<h5>" + message + "</h5>\n" + errors);
            $(target_form).find('input[aria-required], textarea[aria-required]').each(function(i,elem) {
              if ($(this).attr('aria-invalid')) {
                $(this).blur();
              }
            });
          }
          $(target_result).slideDown();
        },
        'text'
      );
    });
  });
});
