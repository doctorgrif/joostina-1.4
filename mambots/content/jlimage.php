<?php
/**
 * @package   JLImage - Замена изображений в контенте "всплывающими" изображениями, увеличивающимися при нажатии
 * @copyright Авторские права (C) 2000-2013 Gold Dragon.
 * @license   http://www.gnu.org/licenses/gpl.htm GNU/GPL
 *            GDNLotos - Главные новости - модуль позволяет выводить основные материалы по определённым критериям для Joostina 1.4.1.x
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл view/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

$tmp = mosCommonHTML::loadJqueryPlugins('fancybox/jquery.fancybox', true, true);
echo $tmp;

$_MAMBOTS->registerFunction('onPrepareContent', 'botJLImage');

function botJLImage($published, &$row, &$params)
{
	static $bb = 0;

	// Включен ли плагин
	if ($published) {

		// Есть ли TEXT и нет ли заглушки {{jlimage}}
		if (isset($row->text) and stripos($row->text, '{{jlimage}}') === false) {

			// Получаем каталог
			$directory = JCore::getParam($_REQUEST, 'directory', 0, 'i');
			if(!$directory){
                $mainframe = mosMainFrame::getInstance();
                require_once ($mainframe->getPath('class', 'com_frontpage'));
                $configObject = new frontpageConfig();

                $database = database::getInstance();
				$sql = "SELECT `value` FROM `#__config` WHERE `name` = 'directory' AND `group` = 'com_frontpage' AND `subgroup` = 'default'";
				$database->setQuery($sql);
				$directory = $configObject->_parseValue($database->loadResult());
			}

			// Получаем параметры
			$_MAMBOTS = mosMambotHandler::getInstance();
			$mambot = $_MAMBOTS->_content_mambot_params['jlimage'];

			$botParams = new mosParameters($mambot->params);
			$param['directory'] = intval($botParams->def('directory', 0));
			$param['catid'] = $botParams->def('catid', '');
			$catids = ($param['catid']) ? $catids = explode(',', $param['catid']) : array();
			$param['width'] = intval($botParams->def('size', 200));
			$param['quality'] = intval($botParams->def('quality', 75));
			$param['style'] = $botParams->def('style', 0);
			$param['style_default'] = $botParams->def('style_default', 'left');
			$param['correct'] = $botParams->def('correct', 1);
			$param['ignor_small'] = $botParams->def('ignor_small', 1);

            $param['overlay_opacity'] = $botParams->def('overlay_opacity', '0.3');
            $param['overlay_color'] = trim($botParams->def('overlay_color', '#000000'));
            $param['overlay_color'] = (preg_match('/^#([0-9a-f]{6}|[0-9a-f]{3})$/', $param['overlay_color'])) ? $param['overlay_color'] : '#000000';
            $param['transition_in'] = $botParams->def('transition_in', 'elastic');
            $param['transition_out'] = $botParams->def('transition_out', 'elastic');
            $param['speed_in'] = intval($botParams->def('speed_in', 600));
            $param['speed_out'] = intval($botParams->def('speed_out', 600));

			if (($param['directory'] == $directory or $param['directory'] == 0) and (in_array($row->catid, $catids) or $param['catid'] == '')) {
				// получаем все картинки
				$b = preg_match_all('#<img[^>]*src=(["\'])([^"\']*)\1[^>]*>#is', $row->text, $preg_result, PREG_SET_ORDER);

				// Есть ли картинки
				if ($b) {
					$image_old = array();
					$image_new = array();
					$script = array();

					// перебираем картинки
					foreach ($preg_result as $value) {

						// Проверяем где находится картинка (внешняя или с сайта
						$parse_url = parse_url($value[2]);
						$parse_site = parse_url(_JLPATH_SITE);

						// Если картинка с этого сайта
						if ((isset($parse_url['host']) and $parse_url['host'] == $parse_site['host']) or !isset($parse_url['host'])) {
							if (is_readable(_JLPATH_ROOT . $parse_url['path'])) {
								$value[2] = _JLPATH_SITE . $parse_url['path'];
								$info_image = getimagesize($value[2]);
							} else {
								$info_image = false;
							}
						} else {
							$info_image = getimagesize($value[2]);
						}

						// Готовим картинки
						if ($info_image) {

							// Готовим размеры
							if ($info_image[0] > $param['width']) {
								$img_width = $param['width'];
								$img_height = intval($img_width * $info_image[1] / $info_image[0]);
							} else {
								$img_width = $info_image[0];
								$img_height = $info_image[1];
							}
							// готовим выравнивание
                            if($param['style']){
                                $img_float = 'float:' . $param['style'] . ';';
                            }else{
                                // берём данные из стиля
                                $img_float = (preg_match('#style=["\'].*?float\s*:\s*(left|right|none|inherit)#si', $value[0], $temp)) ? 'float:' . trim($temp[1]) . ';' : '';
                                // При пустом значении пытаемся достать данные из align
                                if($img_float == ''){
                                    $img_float = (preg_match('#align=["\']\s*(left|right|none|inherit)#si', $value[0], $temp)) ? 'float:' . trim($temp[1]) . ';' : '';
                                }
                                // Попытка применить стиль по умолчанию
                                if($img_float == '' and $param['style_default']){
                                    $img_float = 'float:' . $param['style_default'] . ';';
                                }
                            }

                            // коррекция окончания файла изображения
                            if ($param['correct']) {
                                // прооверяем а есть ли окончание у изображение (например, ссылки с fotki.yandex.ru не  имеют окончания)
                                $b = preg_match('#(\.jpeg|\.jpg|\.gif|\.png)$#i', $value[2]);
                                if (!$b) {
                                    if ($info_image['mime'] == 'image/gif') {
                                        $value[2] = $value[2] . '.gif';
                                    } elseif ($info_image['mime'] == 'image/png') {
                                        $value[2] = $value[2] . '.png';
                                    } else {
                                        $value[2] = $value[2] . '.jpg';
                                    }
                                }
                            }

                            // Готовим картинку
                            if ($info_image[0] > $param['width'] or !$param['ignor_small']) {
                                $src = _JLPATH_SITE . '/mambots/content/plugin_jlimage/imgsketch.php?' . 'src=' . $value[2] . '&w=' . $img_width . '&h=' . $img_height . '&q=' . $param['quality'];

                                // Формируем картинку
                                $image_new[]
                                    = '
                            <a id="plgjl-' . $row->catid . '-' . $row->id . '-' . $bb . '" href="' . $value[2] . '">
                            <img src="' . $src . '" width="' . $img_width . '" height="' . $img_height . '" style="' . $img_float . '" />
                            </a>
                            ';
                                $script[] = '$("#plgjl-' . $row->catid . '-' . $row->id . '-' . $bb . '")
							    .fancybox({
							        "overlayShow":true,
							        "overlayOpacity":' . $param['overlay_opacity'] . ',
							        "transitionIn":"' . $param['transition_in'] . '",
							        "transitionOut":"' . $param['transition_out'] . '",
							        "overlayColor":"' . $param['overlay_color'] . '",
							        "speedIn":"' . $param['speed_in'] . '",
							        "speedOut":"' . $param['speed_out'] . '"
							    });';
                            } else {
                                $image_new[] = ' <img src="' . $value[2] . '" width="' . $img_width . '" height="' . $img_height . '" style="' . $img_float . '" />';
                            }
                            $bb++;
						} else {
							// если нет картинки или в ней ошибка то просто очищаем
							$image_new[] = '';
						}
						// Запоминаем оригинальный тэг IMG
						$image_old[] = $value[0];
					}
					// Формируем окончательно скрипт
					$script = (count($script)) ? '<script>' . implode($script) . '</script>' : '';

					// Заменяем оригиналы картинок на превьюшки
					$row->text = str_replace($image_old, $image_new, $row->text) . $script;
				}
			}
		}
	}
	// удаляем из контента {{jlimage}}
	$row->text = str_replace('{{jlimage}}', '', $row->text);
}





















