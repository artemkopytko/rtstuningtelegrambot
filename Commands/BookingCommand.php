<?php
/**
 * Created by PhpStorm.
 * User: artemkopytko
 * Date: 4/28/19
 * Time: 10:27 PM
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\PhotoSize;


class BookingCommand extends UserCommand
{
	protected $name = 'booking';                      // Your command's name
	protected $description = 'A command for booking'; // Your command description
	protected $usage = '/booking';                    // Usage of your command
	protected $version = '1.0.1';                  // Version of your command
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

		//State machine
		//Entrypoint of the machine state if given by the track
		//Every time a step is achieved the track is updated
		switch ($state) {
			case 0:
				if ($text === '' || $text=='📝 Записаться') {
					$notes['state'] = 0;
					$this->conversation->update();

					$data['text']         = 'Как вас зовут?';
					$data['reply_markup'] = (new Keyboard(['Отменить ❌']))
						->setResizeKeyboard(true)
						->setOneTimeKeyboard(false)
						->setSelective(true);

					$result = Request::sendMessage($data);
					break;
				}

				$notes['name'] = $text;
				$text          = '';

			// no break
			case 1:
				if ($text === '' || !is_string($text)) {
					$notes['state'] = 1;
					$this->conversation->update();

					$data['text'] = 'Введите ваш номер телефона:';
					if ($text !== '') {
						$data['text'] = 'Введите ваш номер телефона:';
					}

					$result = Request::sendMessage($data);
					break;
				}

				$notes['phone'] = $text;
				$text         = '';

			// no break
			case 2:
				if ($text === '' || !is_string($text)) {
					$notes['state'] = 2;
					$this->conversation->update();

					$data['text'] = 'Введите марку вашего авто:';
					if ($text !== '') {
						$data['text'] = 'Введите марку вашего авто:';
					}

					$result = Request::sendMessage($data);
					break;
				}

				$notes['mark'] = $text;
				$text         = '';

			// no break
			case 3:
				if ($text === '' || !is_string($text)) {
					$notes['state'] = 3;
					$this->conversation->update();

					$data['text'] = 'Введите модель вашего авто:';
					if ($text !== '') {
						$data['text'] = 'Введите модель вашего авто:';
					}

					$result = Request::sendMessage($data);
					break;
				}

				$notes['model'] = $text;
				$text          = '';

			// no break

			case 4:
				$this->conversation->update();
				$out_text = 'Новая заявка с телеграм бота:' . PHP_EOL;
				unset($notes['state']);
				foreach ($notes as $k => $v) {
					$out_text .= PHP_EOL . ucfirst($k) . ': ' . $v;
				}

				$data = [
					'chat_id' => '-1001150647628',
				];

				$data['text']        = $out_text;
				$data['disable_notification'] = true;
				$data['reply_markup'] = Keyboard::remove(['selective' => true]);
				$data['caption']      = $out_text;


				$this->conversation->stop();

				$res = Request::sendMessage($data);

				$data = [
					'chat_id' => $chat_id,
				];

				$data['text']        = '🔧 Спасибо! Ваша заявка успешно отправлена';
				$data['reply_markup'] = (new Keyboard(['О нас', 'Услуги', 'Связаться']))
					->setResizeKeyboard(true)
					->setOneTimeKeyboard(true)
					->setSelective(true);


				$res = Request::sendMessage($data);

				break;
		}

		return $res;
	}
}
