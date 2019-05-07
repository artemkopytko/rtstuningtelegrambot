<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class ServiceComplexMoreCommand extends UserCommand
{
	protected $name = 'serviceComplexMore';                      // Your command's name
	protected $description = 'A command for serviceComplexMore'; // Your command description
	protected $usage = '/serviceComplexMore';                    // Usage of your command
	protected $version = '1.0.0';                  // Version of your command


	public function execute()
	{
		$message = $this->getMessage();            // Get Message object

		$chat_id = $message->getChat()->getId();   // Get the current Chat ID

		$text = "Мы можем решить следующие задачи:  
Изменение или отключение ограничителя скорости 
Изменение отсечки максимальных оборотов 
Уменьшение расхода топлива 
Настройка работы АКПП 
Программное удаление лямбда зондов для корректного удаления катализатора Программное отключение сажевого фильтра и отключение аварийных режимов для Физического удаления систем DPF/FAP
Отключение систем добавки присадок (мочевины adblue, эолис и т.п.) 
Отключение клапана ЕГР для его заглушки или удаления - и т.д. ";

		$data = [                                  // Set up the new message data
			'chat_id' => $chat_id,                 // Set Chat ID to send the message to
			'parse_mode' => 'HTML',
			'text'    => $text,
			'reply_markup' => array(
				'keyboard' => array(array('🔙 к услугам', '📝 Записаться')),
				'one_time_keyboard' => true,
				'resize_keyboard' => true)
		];


		return Request::sendMessage($data);

	}
}