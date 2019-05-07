<?php
/**
 * Created by PhpStorm.
 * User: artemkopytko
 * Date: 4/28/19
 * Time: 11:02 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class ServiceChipCommand extends UserCommand
{
	protected $name = 'serviceChip';                      // Your command's name
	protected $description = 'A command for serviceChip'; // Your command description
	protected $usage = '/serviceChip';                    // Usage of your command
	protected $version = '1.0.0';                  // Version of your command


	public function execute()
	{
		$message = $this->getMessage();            // Get Message object

		$chat_id = $message->getChat()->getId();   // Get the current Chat ID

		$text = "Чип-тюнинг - это программное изменение настроек в блоке управления двигателя (ЭБУ). Это самый простой, недорогой и высокотехнологичный способ улучшения динамических характеристик Вашего автомобиля - эластичности двигателя, улучшения реакции на управление, повышение мощности и крутящего момента. Улучшения производятся за счет настройки и оптимизации заводских установок управления двигателем автомобиля. ";

		$data = [                                  // Set up the new message data
			'chat_id' => $chat_id,                 // Set Chat ID to send the message to
			'parse_mode' => 'HTML',
			'text'    => $text,
			'reply_markup' => array(
				'keyboard' => array(array('🔙 к услугам', 'Просчитать')),
				'one_time_keyboard' => false,
				'resize_keyboard' => true)
		];

		$result = Request::sendMessage($data);

		$text = "При чип-тюнинге все изменения относятся только к программной части бортового компьютера автомобиля, без внесения изменений в конструкцию двигателя, систем впуска, впрыска топлива и выхлопа. Никаких механических переделок не производится.";

		$data = [                                  // Set up the new message data
			'chat_id' => $chat_id,                 // Set Chat ID to send the message to
			'parse_mode' => 'HTML',
			'text'    => $text,
		];

		$result = Request::sendMessage($data);

		$text = "В результате чип-тюнинга автомобиль становится более отзывчивым на нажатие педали газа, получает дополнительную мощность и запас тяги, что сказывается на безопасности движения, например, при обгоне.";

		$data = [                                  // Set up the new message data
			'chat_id' => $chat_id,                 // Set Chat ID to send the message to
			'parse_mode' => 'HTML',
			'text'    => $text,
		];



		return Request::sendMessage($data);

	}
}