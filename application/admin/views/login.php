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
	<div class="row mb-2">
		<div class="col">
			<h1><?= $title ?></h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-5">
			<?= form_open('/site/login/', ['id' => 'siteLogin-form', 'novalidate' => true]); ?>

				<div class="form-group">
					<label for="site-login-form-username">Ваш Логин*</label>
					<input type="text" id="site-login-form-username" name="SiteLoginForm[username]" class="form-control<?= !empty(form_error("SiteLoginForm[username]")) ? ' is-invalid' : (!empty(set_value("SiteLoginForm[username]")) ? ' is-valid' : '') ?>" value="<?= set_value("SiteLoginForm[username]"); ?>" placeholder="Логин" autofocus="" aria-required="true" aria-invalid="true" data-target-error="#error-site-login-form-username">
					<div class="invalid-feedback" id="error-site-login-form-username"><?= form_error("SiteLoginForm[username]") ?></div>
				</div>
				<div class="form-group">
					<label for="site-login-form-password">Пароль*</label>
					<input type="password" id="site-login-form-password" name="SiteLoginForm[password]" class="form-control<?= !empty(form_error("SiteLoginForm[password]")) ? ' is-invalid' : (!empty(set_value("SiteLoginForm[password]")) ? ' is-valid' : '') ?>" value="<?= set_value("SiteLoginForm[password]"); ?>" placeholder="********" aria-required="true" aria-invalid="true" data-target-error="#error-site-login-form-password">
					<div class="invalid-feedback" id="error-site-login-form-password"><?= form_error("SiteLoginForm[password]") ?></div>
				</div>
				<div class="form-group">
					<div class="custom-control custom-checkbox">
						<input type="hidden" name="SiteLoginForm[rememberMe]" value="0">
						<input type="checkbox" id="site-login-form-remember-me" name="SiteLoginForm[rememberMe]" class="custom-control-input<?= !empty(form_error("SiteLoginForm[rememberMe]")) ? ' is-invalid' : (!empty(set_value("SiteLoginForm[rememberMe]")) ? ' is-valid' : '') ?>" value="1" aria-invalid="true" data-target-error="#error-site-login-form-remember-me">
						<label class="custom-control-label" for="site-login-form-remember-me">Запомни меня</label>
						<div class="invalid-feedback" id="error-site-login-form-remember-me"><?= form_error("SiteLoginForm[rememberMe]") ?></div>
					</div>
				</div>
				<!-- <div class="form-group">
					<input type="hidden" name="g-recaptcha-user-ip" value="<?= ''// $_SERVER['REMOTE_ADDR'] ?>">
					<div id="site-login-form-captcha" name="SiteLoginForm[captcha]" class="form-control-file<?= ''// !empty(form_error("g-recaptcha-response")) ? ' is-invalid' : (isset($content['captcha']['is_valid']) ? ($content['captcha']['is_valid'] ? ' is-valid' : ' is-invalid') : '') ?>" data-target-error="#error-site-login-form-captcha">
						<div class="g-recaptcha" data-sitekey="<?= ''// $content['captcha']['public_key'] ?>"></div>
					</div>
					<div class="invalid-feedback" id="error-site-login-form-captcha"><?= ''// !empty(form_error("g-recaptcha-response")) ? form_error("g-recaptcha-response") : $content['captcha']['error_msg'] ?></div>
				</div> -->
				<div class="form-group">
					<button type="submit" id="site-login-form-button_submit" class="btn btn-primary">Войти</button>
				</div>
			</form>
		</div>
	</div>
