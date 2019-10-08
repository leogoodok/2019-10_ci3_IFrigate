/**
 * Обёртка jQuery для избежания конфликтов  при использовании
 * псевдонима $ с другими JS библиотеками
 */
jQuery(function($){
  /**
   *  Выполнить после построения страницы:
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


    $(function() {
      /**
       *  Добавление атрибута "data-name"
       *  Удаление атрибута "name"
       *  у всех элементов выбора "Статуса сообщения"
       */
      jQuery('#site-message-table').find('select').each(function(i,elem) {
        $(this).attr('data-name', $(this).attr('name'));
        $(this).removeAttr('name');
      });
    });


    /**
     *  Назначение обработчика события изменения значения
     * элементов выбора "Удалить сообщение"
     */
    jQuery('#site-message-table').find('select').change(function() {
      if ($(this).data('value') == $(this).val()) {
        $(this).removeAttr('name');
        $(this).removeClass('bg-warning');
      } else {
        $(this).attr('name', $(this).attr('data-name'));
        $(this).addClass('bg-warning');
      }
    });
  });
});
