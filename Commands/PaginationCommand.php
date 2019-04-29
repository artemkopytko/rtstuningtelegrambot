<?php
/**
 * Created by PhpStorm.
 * User: artemkopytko
 * Date: 4/29/19
 * Time: 5:28 PM
 */


namespace Longman\TelegramBot\Commands\UserCommands;

use Pagination;
use TelegramBot\InlineKeyboardPagination\InlineKeyboardPagination;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class PaginationCommand extends UserCommand
{
	protected $name = 'pagination';                      // Your command's name
	protected $description = 'A command for pagination'; // Your command description
	protected $usage = '/pagination';                    // Usage of your command
	protected $version = '1.0.0';                  // Version of your command




	public function execute()
	{
		$message = $this->getMessage();            // Get Message object

		$chat_id = $message->getChat()->getId();   // Get the current Chat ID



		$items         = range(1, 100); // required.
		$command       = 'pag'; // optional. Default: pagination
		$selected_page = 10;            // optional. Default: 1
		$labels        = [              // optional. Change button labels (showing defaults)
			'default'  => '%d',
			'first'    => '« %d',
			'previous' => '‹ %d',
			'current'  => '· %d ·',
			'next'     => '%d ›',
			'last'     => '%d »',
		];

// optional. Change the callback_data format, adding placeholders for data (showing default)
		$callback_data_format = 'command={COMMAND}&oldPage={OLD_PAGE}&newPage={NEW_PAGE}';


		$ikp = new Pagination($items, $command);
		$ikp->setMaxButtons(7, true); // Second parameter set to always show 7 buttons if possible.
		$ikp->setLabels($labels);
		$ikp->setCallbackDataFormat($callback_data_format);

// Get pagination.
		$pagination = $ikp->getPagination($selected_page);

// or, in 2 steps.
		$ikp->setSelectedPage($selected_page);
		$pagination = $ikp->getPagination();

		$text = "Здесь нужно расскзать что такое чип тюнинг, зачем он делается. Предлагаю добавить кнопки 'Как это происходит?' и 'Чип-тюнинг для моего авто'. Нажав на 'чип-тюнинг для моего авто'- начнется сценарий с выбором марки и модели";



		if (!empty($pagination['keyboard'])) {
			//$pagination['keyboard'][0]['callback_data']; // command=testCommand&oldPage=10&newPage=1
			//$pagination['keyboard'][1]['callback_data']; // command=testCommand&oldPage=10&newPage=7


			$data = [                                  // Set up the new message data
				'chat_id' => $chat_id,                 // Set Chat ID to send the message to
				'parse_mode' => 'HTML',
				'text'    => $text
			];


				$inline_keyboard = [
//					[['text' => 'Contact', 'callback_data' => 'contact']],
//					[['text' => 'Review', 'callback_data' => 'review_1220']],
					$pagination['keyboard'],
				];

			$reply_markup = $inline_keyboard;

			$data['reply_markup'] = $reply_markup;
}






		return Request::sendMessage($data);

	}
}