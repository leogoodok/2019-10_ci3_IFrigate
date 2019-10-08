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
  });
});
