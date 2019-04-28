<?php
/**
 * Created by PhpStorm.
 * User: artemkopytko
 * Date: 4/28/19
 * Time: 10:29 PM
 */


namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class CancelBookingCommand extends UserCommand
{
	protected $name = 'cancelBooking';                      // Your command's name
	protected $description = 'A command for canceling booking'; // Your command description
	protected $usage = '/cancelBooking';                    // Usage of your command
	protected $version = '1.0.0';                  // Version of your command


	public function execute()
	{
		$message = $this->getMessage();            // Get Message object

		$chat_id = $message->getChat()->getId();   // Get the current Chat ID

		$text = "Запись успешно отменена";

		$data = [                                  // Set up the new message data
			'chat_id' => $chat_id,                 // Set Chat ID to send the message to
			'parse_mode' => 'HTML',
			'text'    => $text,
			'reply_markup' => array(
				'keyboard' => array(array('О нас', 'Услуги', 'Связаться')),
				'one_time_keyboard' => true,
				'resize_keyboard' => true)
		];


		return Request::sendMessage($data);

	}
}
