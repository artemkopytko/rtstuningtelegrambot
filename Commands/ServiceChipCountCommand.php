<?php
/**
 * Created by PhpStorm.
 * User: artemkopytko
 * Date: 4/29/19
 * Time: 1:09 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\PhotoSize;


include ('simplehtmldom/simple_html_dom.php');

class Mark {
	public $name = "";
	public $href = "";

	public function __construct ( $name, $href ) {
		$this->name = $name;
		$this->href = $href;
	}
}

class Model {
	public $name = "";
	public $lowerName = "";
	public $href = "";

	public function __construct ( $name, $lowerName, $href ) {
		$this->name = $name;
		$this->lowerName = $lowerName;
		$this->href = $href;
	}
}

class ModelType {
	public $name = "";
	public $href = "";
	public $gen = "";

	public function __construct ( $name, $href, $gen ) {
		$this->name = $name;
		$this->href = $href;
		$this->gen = $gen;
	}
}

class Generation {
	public $name = "";
	public $href = "";

	public function __construct ( $name, $href ) {
		$this->name = $name;
		$this->href = $href;
	}
}

class Car {
	public $mark = "";
	public $model = "";
	public $modification = "";
	public $fuelType = "";
	public $horsePower = "";
	public $fuelConsumption = "";
	public $acceleration = "";

	public function __toString() {
		$string = $this->mark.PHP_EOL;
		$string .= $this->model.PHP_EOL;
		$string .= $this->fuelType.PHP_EOL;
		$string .= $this->horsePower.PHP_EOL;
		$string .= $this->fuelConsumption.PHP_EOL;
		$string .= $this->acceleration.PHP_EOL;

		return $string;
	}
}

class ServiceChipCountCommand extends UserCommand
{
	protected $name = 'serviceChipCount';                      // Your command's name
	protected $description = 'A command for serviceChipCount'; // Your command description
	protected $usage = '/serviceChipCount';                    // Usage of your command
	protected $version = '1.0.0';                  // Version of your command
	protected $need_mysql = true;
	protected $conversation;
	protected $private_only = true;


	public function execute()
	{
		$message = $this->getMessage();

		$chat    = $message->getChat();
		$user    = $message->getFrom();
		$text    = trim($message->getText(true));
		$chat_id = $chat->getId();
		$user_id = $user->getId();
//		$text = '';

		//Preparing Response
		$data = [
			'chat_id' => $chat_id,
		];

		if ($chat->isGroupChat() || $chat->isSuperGroup()) {
			//reply to message id is applied by default
			//Force reply is applied by default so it can work with privacy on
			$data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
		}

		//Conversation start
		$this->conversation = new Conversation($user_id, $chat_id, $this->getName());

		$notes = &$this->conversation->notes;
		!is_array($notes) && $notes = [];

		//cache data from the tracking session if any
		$state = 0;
		if (isset($notes['state'])) {
			$state = $notes['state'];
		}

		$result = Request::emptyResponse();

		$html = file_get_html('https://avtomarket.ru/catalog/');
		$marks = [];
		$marksNames = [];
		$excludedMarks = ['AC Cars','ARO','Alpina','Asia', 'Aurus','BYD','Beijing','Brilliance','Bristol','Bugatti','Buick','Caterham','Chance','Changan','Changfeng','Chery','DS','DW Hower','Dacia','Dadi','Daewoo','Daihatsu','Daimler','Datsun','Derways','Dong Feng','FAW','FSO','Foton','Geely','Gillet','Great Wall','Hafei','Haima','Haval','Hawtai','Holden','Huanghai','Hurtan','Innocenti','Iran Khodro','Isuzu','JAC','JMC','LDV','Lancia','Liebao','Lifan','Luxgen','Mahindra','Marcos','Maruti','Mercury','Mitsuoka','Morgan','Noble','Packard','Plymouth','Proton','Ravon','Rover','Saab','Santana','Saturn','ShuangHuan','Ssang Yong','TVR','Talbot','Tata','Tesla','Tianma','Tianye','Trabant','Venturi','Vortex','Wartburg','Xinkai','ZX','Zotye','ВАЗ','ВИС','ГАЗ','Донинвест','ЗАЗ','ЗИЛ','ИЖ','Москвич','СеАЗ','ТагАЗ','УАЗ'];

		foreach($html->find('#name-list li a') as $e) {

			$name = mb_convert_encoding($e->innertext,'utf-8','windows-1251');
			$href = mb_convert_encoding(str_replace("/catalog/", "", $e->href),'UTF-8','Windows-1251');

			$mark = new Mark($name, $href);

			array_push($marks,$mark);
		}

		foreach($marks as $key => $mark) {
			if (in_array($mark->name, $excludedMarks)) {
				unset($marks[$key]);
			}
		}

		$marksList = '';
		foreach ($marks as $key => $mark) {
			array_push($marksNames, strtolower($mark->name));
			$marksList .= $mark->name . PHP_EOL;
		}


		//State machine
		//Entrypoint of the machine state if given by the track
		//Every time a step is achieved the track is updated
		switch ($state) {
			case 0:

//					$data['text']         = 'Пользователь ввел: '.$text;
//					$result = Request::sendMessage($data);

				if ($text === '' || $text === 'Просчитать' || !in_array(strtolower($text), $marksNames, true)) {


					$notes['state'] = 0;
					$this->conversation->update();

					$data['text']         = 'Вот список поддерживаемых марок автомобилей:';
					$data['reply_markup'] = (new Keyboard(['Отмена ❌']))
						->setResizeKeyboard(true)
						->setOneTimeKeyboard(false)
						->setSelective(true);

					$result = Request::sendMessage($data);

					$data['text'] = $marksList;

					$result = Request::sendMessage($data);

					$data['text']         = 'Введите марку вашего автомобиля в соответсвии с названием из списка:';
					if ($text !== '') {
						$data['text'] = 'Пожалуйста, выберите марку авто <b>в соответсвии</b> с названием из списка';
						$data['parse_mode'] = 'HTML';
					}

					$result = Request::sendMessage($data);
					break;
				}

				$notes['mark'] = ucfirst($text);

				foreach ($marks as $key => $mark) {
					if($mark->name == $notes['mark']) {
						$notes['mark_link'] = $mark->href;
					}
				}

				$text          = '';

			// no break
			case 1:
				$html = file_get_html('https://avtomarket.ru/catalog/'.$notes['mark_link']);

//				$data['text'] = 'Сделал запрос по адресу '.'https://avtomarket.ru/catalog/'.$notes['mark_link'];
//				$result = Request::sendMessage($data);

				$models = [];
				$modelsNames = [];

				foreach($html->find('#name-list li a') as $e) {

					$name = mb_convert_encoding($e->innertext,'utf-8','windows-1251');
					$lowerName = strtolower(mb_convert_encoding($e->innertext, 'utf-8', 'windows-1251'));
					$href = mb_convert_encoding(str_replace("/catalog/", "", $e->href),'UTF-8','Windows-1251');

					$name = str_replace($notes['mark'].' ','',$name);
					$model = new Model($name, $lowerName, $href);

					$models[] = $model;
					$modelsNames[] = strtolower($name);
				}

				$modelsList = '';
				foreach ($models as $key => $model) {
					$modelsList .= str_replace($notes['mark'].' ','', $model->name) . PHP_EOL;
				}

//				$data['text'] = 'Получена модель: '.$text;
//				$result = Request::sendMessage($data);

				if ($text === '' || $text==='Просчитать' || !in_array(strtolower($text), $modelsNames)) {
					$notes['state'] = 1;
					$this->conversation->update();

					$data['text'] = 'Вот список поддерживаемых моделей '.$notes['mark'];

					$result = Request::sendMessage($data);

					$data['text'] = $modelsList;

					$result = Request::sendMessage($data);

					$data['text'] = 'Выберите модель '.$notes['mark'].' в соответсвии с названием из списка';
					if ($text !== '') {
						$data['text'] = 'Пожалуйста, выберите модель '.$notes['mark'].' <b>в соответсвии</b> с названием из списка';
						$data['parse_mode'] = 'HTML';
					}

					$result = Request::sendMessage($data);
					break;
				}

				$tmpModelName = '';

				foreach($models as $model) {
					if($model->lowerName == $text) {
						$notes['model'] = $model->name;
					}
				}

//				$notes['model'] = ucfirst($text);

//				$data['text'] = 'модель ОК - '.$notes['model'];
//				$result = Request::sendMessage($data);


				foreach ($models as $key => $model) {
					if(ucfirst($text) == $model->name) {

//						$data['text'] = 'НАЙДЕНА МОДЕЛЬ, ССЫЛКА: '.$model->href;
//						$result = Request::sendMessage($data);

						$notes['model_link'] = $model->href;
					}
				}

				$text         = '';

			// no break
			case 2:

//				$data['text'] = 'Сделал запрос по адресу '.'https://avtomarket.ru/catalog/'.$notes['model_link'];
//				$result = Request::sendMessage($data);

				$html = file_get_html('https://avtomarket.ru/catalog/'.$notes['model_link']);
				$modelTypes = [];
				$modelTypesNames = [];
				$modelTypesGens = [];

				foreach($html->find('ul.groupx') as $ul) {
					foreach($ul->find('li p') as $p) {


						$a = $p->find('a',0);
						if(strpos($a->href, '/sale') === false && ($a->href != '')) {
							$name = mb_convert_encoding($a->innertext,'utf-8','windows-1251');
							$href = mb_convert_encoding($a->href,'utf-8','windows-1251');
						}

						if(strpos($p, '<a')) {
							continue;
						}

						$gen = mb_convert_encoding($p,'utf-8','windows-1251');
						$gen = str_replace('<p>','', $gen);
						$gen = str_replace('</p>','',$gen);

						$modelTypes[] = new ModelType($name, $href, $gen);
						$modelTypesNames[] = $name;
						$modelTypesGens[] = $gen;
					}

				}

				$modelsTypesList = '';
				foreach ($modelTypes as $key => $model_type) {
					$modelsTypesList .= $key+1 . '. ' . $model_type->name . ' (' . $model_type->gen . ')'. PHP_EOL;
				}

				if ($text === '' || !is_numeric($text) || $text < 1 || $text > count($modelTypes)) {
					$notes['state'] = 2;
					$this->conversation->update();

					$data['text'] = 'Вот список поколений '.$notes['mark'].' '.$notes['model'];

					$result = Request::sendMessage($data);

					$data['text'] = $modelsTypesList;

					$result = Request::sendMessage($data);

					$data['text'] = 'Введите <b>индекс</b> поколения '.$notes['mark'].' '.$notes['model'].'.'.PHP_EOL.'К примеру: <em>1</em>';
					if ($text !== '') {
						$data['text'] = 'Введите <b>индекс</b> поколения '.$notes['mark'].' '.$notes['model'].'.'.PHP_EOL.'К примеру: <em>1</em>';
					}

					$data['parse_mode'] = 'HTML';

					$result = Request::sendMessage($data);
					break;
				}

				$notes['genNum'] = $text;

				$notes['genLink'] = $modelTypes[intval($text)-1]->href;
				$notes['gen'] = $modelTypes[intval($text)-1]->name;

//				foreach ($modelTypes as $key => $model_type) {
//					$tmpKey = $key + 1;
//					if($tmpKey == $text) {
//						$notes['genLink'] = $model_type->href;
//						$notes['gen'] = $model_type->name;
//					}
////					$modelsTypesList .= $key+1 . '. ' . $model_type->name . ' (' . $model_type->gen . ')'. PHP_EOL;
//				}

				$text         = '';
//				$data['text'] = 'Конечная единица: '.$notes['gen'].PHP_EOL;
//				$data['text'] .= 'Ссылка на конечную единицу: '.$notes['genLink'];

//				$result = Request::sendMessage($data);


			// no break
			case 3:

//				$data['text'] = 'Сделал запрос по адресу '.'https://avtomarket.ru/'.$notes['genLink'];
//				$result = Request::sendMessage($data);

				$html = file_get_html('https://avtomarket.ru/'.$notes['genLink']);
				$gens = [];
				$gensString = '';

				foreach($html->find('ul.#mod-list') as $ul) {
					foreach($ul->find('li div a') as $a) {



						$name = mb_convert_encoding($a->innertext,'utf-8','windows-1251');
						$href = mb_convert_encoding($a->href,'utf-8','windows-1251');

						$gens[] = new Generation($name, $href);

					}

				}

				foreach ($gens as $key => $gen) {
					$gensString .= $key+1 . '. ' . $gen->name . PHP_EOL;
				}

				if ($text === '' || !is_numeric($text) || $text <1 || $text > count($gens)) {
					$notes['state'] = 3;
					$this->conversation->update();

					$data['text'] = 'Вот список модификаций '.$notes['gen'];

					$result = Request::sendMessage($data);

					$data['text'] = $gensString;

					$result = Request::sendMessage($data);

					$data['text'] = 'Введите <b>индекс</b> модификации '.$notes['gen'].PHP_EOL.'К примеру: <em>1</em>';
					if ($text !== '') {
						$data['text'] = 'Введите <b>индекс</b> модификации '.$notes['gen'].PHP_EOL.'К примеру: <em>1</em>';
					}

					$data['parse_mode'] = 'HTML';

					$result = Request::sendMessage($data);
					break;
				}

				$notes['modNum'] = $text;


				$notes['modHref'] = $gens[intval($text)-1]->href;
				$notes['modName'] = $gens[intval($text)-1]->name;


				$text          = '';
			// no break

			case 4:

//				$data['text'] = 'Модификация: '.$notes['modName'].PHP_EOL.'Сделал запрос по адресу '.'https://avtomarket.ru'.$notes['modHref'];
//				$result = Request::sendMessage($data);
				$html = file_get_html('https://avtomarket.ru'.$notes['modHref']);

				$car = new Car;

				$car->mark = 'Марка авто: '.$notes['mark'];
				$car->model = 'Модель авто: '.$notes['model'];
				$car->modification = $notes['modName'];

				foreach($html->find('ul.techd') as $ul) {
					foreach($ul->find('li') as $li) {

						$div = $li->find('div',0);

						if(strpos(mb_convert_encoding($div,'utf-8','windows-1251'), 'Мощность') !== false) {
							$car->horsePower = $div->next_sibling()->innertext;
						} elseif (strpos(mb_convert_encoding($div,'utf-8','windows-1251'), 'Марка топлива') !== false) {
							$car->fuelType = mb_convert_encoding($div->next_sibling()->innertext,'utf-8','windows-1251');
						} elseif (strpos(mb_convert_encoding($div,'utf-8','windows-1251'), 'Расход топлива (смешанный цикл)') !== false) {
							$car->fuelConsumption = $div->next_sibling()->innertext;
						} elseif (strpos(mb_convert_encoding($div,'utf-8','windows-1251'), 'Время разгона до 100 км/ч') !== false) {
							$car->acceleration = $div->next_sibling()->innertext;
						}
					}
				}

//$consumption = floatval($car->fuelConsumption)* 1.1;
//				$acceleration = floatval($car->acceleration)+ 0.2;

				$data['text'] = 'Исходные характеристики '.$car->modification.PHP_EOL;
				if(isset($car->horsePower))$data['text'] .= 'Мощность, л.с.: '.$car->horsePower.PHP_EOL;
				if(isset($car->fuelConsumption))$data['text'] .= 'Расход топлива (смешанный цикл), л. на 100 км.: '.$car->fuelConsumption.PHP_EOL;
				if(isset($car->acceleration))$data['text'] .= 'Время разгона до 100 км/ч, сек.: '.$car->acceleration.PHP_EOL;
				$data['parse_mode'] = 'HTML';

				$result = Request::sendMessage($data);

$horseNewLeast = intval($car->horsePower*1.08);
$horseNewGreatest = intval($car->horsePower*1.3);
$consumptionNew = $car->fuelConsumption*0.9;
$accelerationNewLeast = $car->acceleration-0.5;
$accelerationNewGreatest = $car->acceleration-0.7;

				$data['text'] = 'Чип-тюнинг позволят увеличить мощность двигателя на 8-12% в случае атомосферного мотора, и на <b>20-30%</b> в случае наличия турбины или компрессора. Вот примерные харакетристики вашего авто после чип-тюнинга:'.PHP_EOL.PHP_EOL;
				if(isset($car->horsePower))$data['text'] .= 'Мощность, л.с.: <b>'.$horseNewLeast.'-'.$horseNewGreatest.'</b>'.PHP_EOL;
				if(isset($car->fuelConsumption))$data['text'] .= 'Расход топлива (смешанный цикл), л. на 100 км.: <b>'.$consumptionNew.'</b>'.PHP_EOL;
				if(isset($car->acceleration))$data['text'] .= 'Время разгона до 100 км/ч, сек.: <b>'.$accelerationNewLeast.'-'.$accelerationNewGreatest.'</b>'.PHP_EOL;
				$data['parse_mode'] = 'HTML';
				$data['reply_markup'] = (new Keyboard(['Посмотреть еще','📝 Записаться','Понятно, спасибо 😊' ]))
					->setResizeKeyboard(true)
					->setOneTimeKeyboard(false)
					->setSelective(true);


				$result = Request::sendMessage($data);


			//case 5:


			//	$this->conversation->update();
			//	$out_text = 'Новая заявка с телеграм бота:' . PHP_EOL;
			//	unset($notes['state']);
			//	foreach ($notes as $k => $v) {
			//		$out_text .= PHP_EOL . ucfirst($k) . ': ' . $v;
			//	}

//				$data = [
//					'chat_id' => '-1001150647628',
//				];

			//	$data['text']        = $out_text;
			//	$data['disable_notification'] = true;
			//	$data['reply_markup'] = Keyboard::remove(['selective' => true]);
			//	$data['caption']      = $out_text;


				$this->conversation->stop();

			//	$res = Request::sendMessage($data);

			//	$data = [
			//		'chat_id' => $chat_id,
			//	];

			//	$data['text']        = '🔧 Спасибо! Ваша заявка успешно отправлена';
			//	$data['reply_markup'] = (new Keyboard(['О нас', 'Услуги', 'Связаться']))
			//		->setResizeKeyboard(true)
			//		->setOneTimeKeyboard(true)
			//		->setSelective(true);


				//$res = Request::sendMessage($data);

				break;
		}

		return $result;

	}
}