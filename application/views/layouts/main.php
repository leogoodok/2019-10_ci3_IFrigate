<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Основной шаблон страницы
 *
 * @var $this - Приложение
 * @var string $title - заголовок страницы
 * @var array $header - массив настроек header-а
 * @var array $breadcrumbs - массив настроек навигационной иерархии страницы
 * @var array $content - массив настроек контента страницы
 * @var string $content['body'] контент страниц
 * @var array $footer - массив настроек footer-а
 */

?><!DOCTYPE html>
<html lang="<?= $this->config->config['language'] == 'russian' ? 'ru' : 'en' ?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="robots" content="index,follow" >
	<title><?= $title ?></title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<!-- <link href="<?= ''// $this->config->config['base_url'] ?>assets/bootstrap4/css/bootstrap.css" rel="stylesheet"> -->
	<link href="<?= $this->config->config['base_url'] ?>css/fonts.css" rel="stylesheet">
	<link href="<?= $this->config->config['base_url'] ?>css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<header<?= !empty($header['class']) ? " class=\"{$header['class']}\"" : '' ?>>
	<div class="container text-center text-light">
		<nav class="navbar navbar-expand-lg navbar-dark">
			<a class="navbar-brand" href="<?= $this->config->config['base_url'] ?>"><?= $header['brandLabel'] ?></a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarHeader">
				<ul class="navbar-nav ml-auto mt-2 mt-lg-0">
					<li class="nav-item<?= $header['active_item'] == 'message' ? ' active' : '' ?>">
						<a class="nav-link" href="<?= $this->config->config['base_url'] ?>site/index">Отправить сообщение администратору<?= $header['active_item'] == 'message' ? '<span class="sr-only">(current)</span>' : '' ?></a>
					</li>
				</ul>
			</div>
		</nav>
	</div>
</header>
<div class="<?= !empty($content['class']) ? $content['class'] : '' ?> content pt-2">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb mb-2"><?php /*Навигационная иерархия страницы*/
		for ($i = 0; $i < count($breadcrumbs); $i++):
			if (isset($breadcrumbs[$i]['active']) && $breadcrumbs[$i]['active']): ?>

			<li class="breadcrumb-item active" aria-current="page"><?= $title ?></li><?php
			else: ?>

			<li class="breadcrumb-item"><a href="<?= $breadcrumbs[$i]['link'] ?>"><?= $breadcrumbs[$i]['name'] ?></a></li><?php
			endif;
		endfor; ?>

		</ol>
	</nav><?php /* Панели отображения уведомлений пользователю */
	$alert = $this->session->flashdata($header['active_item']);
	if (isset($alert) && is_array($alert)):
		for ($i = 0; $i < count($alert); $i++):
			foreach ($alert[$i] as $key => $msg):
	?>

	<div class="alert alert-<?= $key ?> mb-1" role="alert">
	  <?= $msg ?>

	</div><?php
			endforeach;
		endfor;
	endif;
	unset($alert);
	?>

<?= $content['body'] ?>

</div>
<footer<?= !empty($footer['class']) ? " class=\"{$footer['class']}\"" : '' ?>>
	<div class="container text-center text-light">
		<nav class="navbar navbar-expand-lg navbar-dark">
			<span class="navbar-brand mb-0">
				&copy; 2013–<?= date("Y") ?> <?= $header['brandLabel'] ?><?php echo (ENVIRONMENT === 'development') ? ' Rendered in <strong>{elapsed_time}</strong> s.' : '' ?>
			</span>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarFooter" aria-controls="navbarFooter" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarFooter">
				<ul class="navbar-nav ml-auto mt-2 mt-lg-0">
				<li class="nav-item">
					<a class="nav-link" href="https://codeigniter.com/" target="_blank">CodeIgniter Version <?= CI_VERSION ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?= isset($footer['link_github']) ? $footer['link_github'] : '#' ?>" target="_blank">GitHub.com</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="top" title="Написать разработчикам">
					<a class="nav-link text-font-glyphicons-halflings" href="mailto:wotskill@wotskill.ru" target="_blank">&#x2709;</a>
				</li>
				</ul>
			</div>
		</nav>
	</div>
</footer>
<div id="butToTop">&#xE133;</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="<?= $this->config->config['base_url'] ?>js/main.js" type="text/javascript"></script>
</body>
</html>
