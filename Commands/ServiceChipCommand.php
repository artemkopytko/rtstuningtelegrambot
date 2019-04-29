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

		$text = "Здесь нужно расскзать что такое чип тюнинг, зачем он делается. Предлагаю добавить кнопки 'Как это происходит?' и 'Чип-тюнинг для моего авто'. Нажав на 'чип-тюнинг для моего авто'- начнется сценарий с выбором марки и модели";

		$data = [                                  // Set up the new message data
			'chat_id' => $chat_id,                 // Set Chat ID to send the message to
			'parse_mode' => 'HTML',
			'text'    => $text,
			'reply_markup' => array(
				'keyboard' => array(array('🔙 к услугам', 'Как это происходит?', 'Просчитать')),
				'one_time_keyboard' => true,
				'resize_keyboard' => true)
		];


		return Request::sendMessage($data);

	}
}