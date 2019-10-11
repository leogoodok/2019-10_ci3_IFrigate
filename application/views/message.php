<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Представление страницы "Отправить сообщение администратору"
 *
 * @var $this - Приложение
 * @var string $title - заголовок страницы
 * @var array $header - массив настроек header-а
 * @var array $breadcrumbs - массив настроек навигационной иерархии страницы
 * @var array $content - массив настроек контента страницы
 * @var array $footer - массив настроек footer-а
 */

?>
	<h1><?= $title ?></h1>
	<p>Если у вас есть вопросы пожалуйста заполните следующую форму чтобы связаться с нами.  Спасибо.</p>
	<div class="row">
		<div class="col-12 col-md-8 col-lg-5">
			<?= form_open_multipart('/', ['id' => 'site-message-form', 'novalidate' => true]); ?>
				<div class="form-group">
					<label for="site-message-form-name">Ваше Имя*</label>
					<input type="text" id="site-message-form-name" name="SiteMessageForm[name]" class="form-control text-capitalize<?= !empty(form_error("SiteMessageForm[name]")) ? ' is-invalid' : (!empty(set_value("SiteMessageForm[name]")) ? ' is-valid' : '') ?>" value="<?= set_value("SiteMessageForm[name]"); ?>" placeholder="Иван Иванов" autofocus="" aria-required="true" aria-invalid="true" data-target-error="#error-site-message-form-name" data-target-result="#site-message-result_submit" data-min_length = "<?= $content['params']['min_length'] ?>" data-max_length = "<?= $content['params']['max_length'] ?>">
					<div class="invalid-feedback" id="error-site-message-form-name"><?= form_error("SiteMessageForm[name]") ?></div>
				</div>
				<div class="form-group">
					<label for="site-message-form-email">Email*</label>
					<input type="text" id="site-message-form-email" name="SiteMessageForm[email]" class="form-control<?= !empty(form_error("SiteMessageForm[email]")) ? ' is-invalid' : (!empty(set_value("SiteMessageForm[email]")) ? ' is-valid' : '') ?>" value="<?= set_value("SiteMessageForm[email]"); ?>" placeholder="example@example.com" aria-required="true" aria-invalid="true" data-target-error="#error-site-message-form-email" data-target-result="#site-message-result_submit">
					<div class="invalid-feedback" id="error-site-message-form-email"><?= form_error("SiteMessageForm[email]") ?></div>
				</div>
				<div class="form-group">
					<label for="site-message-form-subject">Тема сообщения*</label>
					<input type="text" id="site-message-form-subject" name="SiteMessageForm[subject]" class="form-control<?= !empty(form_error("SiteMessageForm[subject]")) ? ' is-invalid' : (!empty(set_value("SiteMessageForm[subject]")) ? ' is-valid' : '') ?>" value="<?= set_value("SiteMessageForm[subject]"); ?>" placeholder="Тема сообщения" aria-required="true" aria-invalid="true" data-target-error="#error-site-message-form-subject" data-target-result="#site-message-result_submit" data-min_length = "<?= $content['params']['min_length'] ?>">
					<div class="invalid-feedback" id="error-site-message-form-subject"><?= form_error("SiteMessageForm[subject]") ?></div>
				</div>
				<div class="form-group">
					<label for="site-message-form-body">Содержание сообщения*</label>
					<textarea type="text" id="site-message-form-body" name="SiteMessageForm[body]" class="form-control<?= !empty(form_error("SiteMessageForm[body]")) ? ' is-invalid' : (!empty(set_value("SiteMessageForm[body]")) ? ' is-valid' : '') ?>" rows="6" placeholder="Содержание сообщения" aria-required="true" aria-invalid="true" data-target-error="#error-site-message-form-body" data-target-result="#site-message-result_submit"><?= set_value("SiteMessageForm[body]"); ?></textarea>
					<div class="invalid-feedback" id="error-site-message-form-body"><?= form_error("SiteMessageForm[body]") ?></div>
				</div>
				<div class="form-group">
					<input type="hidden" name="g-recaptcha-user-ip" value="<?= $_SERVER['REMOTE_ADDR'] ?>">
					<div id="site-message-form-captcha" name="SiteMessageForm[captcha]" class="form-control-file<?= !empty(form_error("g-recaptcha-response")) ? ' is-invalid' : (isset($content['captcha']['is_valid']) ? ($content['captcha']['is_valid'] ? ' is-valid' : ' is-invalid') : '') ?>" data-target-error="#error-site-message-form-captcha" data-target-result="#site-message-result_submit">
						<div class="g-recaptcha" data-sitekey="<?= $content['captcha']['public_key'] ?>"></div>
					</div>
					<div class="invalid-feedback" id="error-site-message-form-captcha"><?= !empty(form_error("g-recaptcha-response")) ? form_error("g-recaptcha-response") : $content['captcha']['error_msg'] ?></div>
				</div>
				<div class="form-group">
					<button type="button" id="site-message-form-button_submit" class="btn btn-primary" data-target-result="#site-message-result_submit">Отправить сообщение</button>
				</div>
				<div class="form-group">
					<button type="submit" id="site-message-form-button_submit_2" class="btn btn-primary" data-target-result="#site-message-result_submit">Отправить сообщение (с перезагрузкой страницы)</button>
				</div>
			</form>
		</div>
	</div>
