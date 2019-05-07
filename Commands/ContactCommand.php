<?php
/**
 * Created by PhpStorm.
 * User: artemkopytko
 * Date: 4/28/19
 * Time: 10:23 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class ContactCommand extends UserCommand
{
	protected $name = 'contact';                      // Your command's name
	protected $description = 'A command for Contact'; // Your command description
	protected $usage = '/contact';                    // Usage of your command
	protected $version = '1.0.0';                  // Version of your command


	public function execute()
	{
		$message = $this->getMessage();            // Get Message object

		$chat_id = $message->getChat()->getId();   // Get the current Chat ID

		$text = "
🌐 Facebook👉 <a href='https://www.facebook.com/rtstuningcom/'>rtstuning</a>
📸 Instagram👉 <a href='https://instagram.com/rtstuning'>rtstuning</a>
📩 Email👉 support@rtstuning.com
📱 Мобильные: +380 (50) 316 39 88
📌 Telgram, Viber, Whatsapp: +380 (67) 393 74 14
💻 Сайт: https://rtstuning.com/
		";

		$data = [                                  // Set up the new message data
			'chat_id' => $chat_id,                 // Set Chat ID to send the message to
			'parse_mode' => 'HTML',
			'text'    => $text,
			'disable_web_page_preview' => true
		];


		return Request::sendMessage($data);

	}
}
