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
	<?= form_open('/site/index/'.(($content['params']['current_page'] - 1) * $content['params']['page_size']), ['id' => 'siteMessageAction-form', 'enctype' => 'multipart/form-data', 'novalidate' => true]); ?>

		<div class="row mb-1">
			<div class="col">
				Показаны сообщения <b><?= $content['params']['begin'] ?>-<?= $content['params']['end'] ?></b> из <b><?= $content['params']['total_count'] ?></b>.
			</div>
		</div>
		<div class="row mb-1">
			<div class="col">
				<table id="site-message-table" class="table table-striped text-center">
					<thead class="thead-light">
						<tr>
							<th scope="col" class="sticky-top">#</th>
							<th scope="col" class="sticky-top">Имя</th>
							<th scope="col" class="sticky-top">Email</th>
							<th scope="col" class="sticky-top">Вложения</th>
							<th scope="col" class="sticky-top">Создано</th>
							<th scope="col" class="sticky-top">Изменено</th>
							<th scope="col" class="sticky-top">Удалено</th>
							<th scope="col" class="sticky-top">Статус</th>
							<th scope="col" class="sticky-top">Удалить</th>
						</tr>
					</thead>
					<tbody><?php
					if (!empty($content['data']) && is_array($content['data'])):
						$nameStatus = TabMessage::nameStatus();
						for ($j = 0; $j < count($content['data']); $j++): ?>

						<tr data-key="<?= $content['data'][$j]->id ?>">
							<td scope="row"><?= $content['params']['begin'] + $j ?></td>
							<td class="sticky-left"><?= $content['data'][$j]->name ?></td>
							<td><?= $content['data'][$j]->email ?></td>
							<td class="text-center"><?= isset($content['data'][$j]->number_attachment) ? $content['data'][$j]->number_attachment : 'нет' ?></td>
							<td><?= isset($content['data'][$j]->created_at) ? $content['data'][$j]->getCreatedDate() : '&nbsp;' ?></td>
							<td><?= isset($content['data'][$j]->updated_at) ? $content['data'][$j]->getUpdatedDate() : '&nbsp;' ?></td>
							<td><?= isset($content['data'][$j]->delete_at) ? $content['data'][$j]->getDeleteDate() : '&nbsp;' ?></td><?php
							if (isset($content['data'][$j]->status) && $content['data'][$j]->status == 0): ?>

							<td><?= isset($content['data'][$j]->status) ? $content['data'][$j]->getStatusText() : '&nbsp;' ?></td><?php
							else: ?>

							<td class="p-1">
								<select id="siteMessageAction-status-<?= $content['data'][$j]->id ?>" class="custom-select" name="SiteMessageAction[status][<?= $content['data'][$j]->id ?>]" data-msg-id="<?= $content['data'][$j]->id ?>" data-value="<?= $content['data'][$j]->id ?>"><?php
									for ($i = 0; $i < count($nameStatus); $i++): ?>

									<option value="<?= $i ?>"<?= ($i == $content['data'][$j]->status) ? ' Selected' : '' ?>><?= $nameStatus[$i] ?></option><?php
									endfor; ?>

								</select>
							</td><?php
							endif; ?>

							<td class="p-1">
								<div class="btn-group-toggle" data-toggle="buttons">
									<label class="btn btn-outline-danger text-font-glyphicons-halflings" for="SiteMessageAction-delete-<?= $content['data'][$j]->id ?>">
										<!-- <input type="hidden" name="SiteMessageAction[delete][<?= ''//$content['data'][$j]->id ?>]" value="0"> -->
										<input type="checkbox" id="SiteMessageAction-delete-<?= $content['data'][$j]->id ?>" name="SiteMessageAction[delete][<?= $content['data'][$j]->id ?>]" value="1" autocomplete="off" data-msg-id="<?= $content['data'][$j]->id ?>">
										&#xE014;
									</label>
								</div>
							</td>
						</tr><?php
						endfor;
						unset($nameStatus);
					else: ?>

						<tr>
							<td scope="row" class="sticky-left">&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr><?php
					endif; ?>

					</tbody>
				</table>
			</div>
		</div>
		<div class="row justify-content-center mb-2">
			<div class="col-8">
				<nav aria-label="Page navigation">
					<ul class="pagination justify-content-center mb-0"><?= $content['pagination'] ?></ul>
				</nav>
			</div>
		</div>
		<div class="row justify-content-center mb-2">
			<div class="col-8">
				<button type="submit" id="siteMessageAction-button_submit" class="btn btn-info btn-block">Сохранить изменения статусов сообщений и удалить выбранные сообщения</button>
			</div>
		</div>
	</form>
