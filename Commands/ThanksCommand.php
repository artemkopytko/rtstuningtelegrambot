<?php
/**
 * Created by PhpStorm.
 * User: artemkopytko
 * Date: 4/29/19
 * Time: 9:21 PM
 */


namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class ThanksCommand extends UserCommand
{
	protected $name = 'thanks';                      // Your command's name
	protected $description = 'A command for thanks'; // Your command description
	protected $usage = '/thanks';                    // Usage of your command
	protected $version = '1.0.0';                  // Version of your command


	public function execute()
	{
		$message = $this->getMessage();            // Get Message object

		$chat_id = $message->getChat()->getId();   // Get the current Chat ID

		$text = "И вам спасибо! До встречи в RTS Tuning 🤝".PHP_EOL;
		$data = [
			'chat_id' => $chat_id,
			'parse_mode' => 'HTML',
			'text' => $text,
			'reply_markup' => array(
				'keyboard' => array(array('О нас', 'Услуги', 'Связаться')),
				'resize_keyboard' => true,
				'one_time_keyboard' => false
			)
		];

		$result = Request::sendMessage($data);

		$text = "Мы находимся по адресу: <b>г. Одесса, ул. Академика Глушко, 31А</b>";


		$data = [                                  // Set up the new message data
			'chat_id' => $chat_id,                 // Set Chat ID to send the message to
			'parse_mode' => 'HTML',
			'text'    => $text,
			'reply_markup' => array(
				'inline_keyboard' => array(array(array(
					'text' => 'Открыть в Google Maps',
					'url' => 'https://goo.gl/maps/Wp5aBEf4NgzxNwCB7'))))
		];


		return Request::sendMessage($data);

	}
}
