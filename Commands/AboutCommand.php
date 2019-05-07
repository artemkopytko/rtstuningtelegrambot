<?php
/**
 * Created by PhpStorm.
 * User: artemkopytko
 * Date: 4/28/19
 * Time: 10:01 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class AboutCommand extends UserCommand
{
	protected $name = 'about';                      // Your command's name
	protected $description = 'A command for About'; // Your command description
	protected $usage = '/about';                    // Usage of your command
	protected $version = '1.0.0';                  // Version of your command


	public function execute()
	{
		$message = $this->getMessage();            // Get Message object

		$chat_id = $message->getChat()->getId();   // Get the current Chat ID

		$data = [                                  // Set up the new message data
			'chat_id' => $chat_id,                 // Set Chat ID to send the message to
			'text'    => 'С 2007 года мы успешно занимаемся автоспортом. Соединив любовь, знания и опыт мы создали автомобильную студию, которая предоставляет полный комплекс услуг по улучшению и увеличению мощности вашего транспорта и техники вашего вождения.', // Set message to send
		];


		$result = Request::sendMessage($data);

		$data = [                                  // Set up the new message data
			'chat_id' => $chat_id,                 // Set Chat ID to send the message to
			'parse_mode' => 'HTML',
			'text'    => 'Мы находимся по адресу: <b>г. Одесса, ул. Академика Глушко, 31А</b>', // Set message to send
			'reply_markup' => array(
				'keyboard' => array(array('🔙 Назад','📝 Записаться')),
				'resize_keyboard' => true,
				'one_time_keyboard' => false
			)

		];

		sleep(1);

		$result = Request::sendMessage($data);

		$data = [                                  // Set up the new message data
			'chat_id' => $chat_id,                 // Set Chat ID to send the message to
			'latitude' => 46.3966091,
			'longitude'    => 30.7066074,
			'reply_markup' => array(
				'inline_keyboard' => array(array(array(
					'text' => 'Открыть в Google Maps',
					'url' => 'https://goo.gl/maps/Wp5aBEf4NgzxNwCB7'))))
		];

		return Request::sendLocation($data);        // Send message!

	}
}