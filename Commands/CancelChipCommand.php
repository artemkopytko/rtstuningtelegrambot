<?php
/**
 * Created by PhpStorm.
 * User: artemkopytko
 * Date: 4/29/19
 * Time: 9:34 PM
 */


namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class CancelChipCommand extends UserCommand
{
	protected $name = 'cancelChip';                      // Your command's name
	protected $description = 'A command for cancelChip'; // Your command description
	protected $usage = '/cancelChip';                    // Usage of your command
	protected $version = '1.0.0';                  // Version of your command


	public function execute()
	{
		$message = $this->getMessage();            // Get Message object

		$chat_id = $message->getChat()->getId();   // Get the current Chat ID

		$text = "Отмена просчета эффективности чип-тюнинга.".PHP_EOL;
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

		return Request::sendMessage($data);

	}
}
