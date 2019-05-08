<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Command execute method if MySQL is required but not available
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function executeNoDb()
    {
        // Do nothing
        return Request::emptyResponse();
    }

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {

	    $text   = trim($this->getMessage()->getText(true));

	    if ($text === 'О нас') {
		    return $this->getTelegram()->executeCommand('about');
	    } elseif ($text === 'Связаться') {
		    return $this->getTelegram()->executeCommand('contact');
	    } elseif ($text === 'Услуги') {
		    return $this->getTelegram()->executeCommand('services');
	    } elseif ($text === 'Чип-тюнинг') {
		    return $this->getTelegram()->executeCommand('serviceChip');
	    }  elseif ($text === 'Просчитать') {
		    return $this->getTelegram()->executeCommand('serviceChipCount');
	    } elseif ($text === 'Тюнинг авто') {
		    return $this->getTelegram()->executeCommand('serviceTuning');
	    } elseif ($text === 'Дополнительные услуги') {
		    return $this->getTelegram()->executeCommand('serviceComplex');
	    } elseif ($text === 'Ещё') {
		    return $this->getTelegram()->executeCommand('serviceComplexMore');
	    } elseif ($text === '📝 Записаться' || $text === 'Нет, не мой') {
		    return $this->getTelegram()->executeCommand('booking');
	    } elseif ($text === 'Отменить ❌') {
		    return $this->getTelegram()->executeCommand('cancelBooking');
	    } elseif ($text === '🔙 Назад') {
		    return $this->getTelegram()->executeCommand('start');
	    } elseif ($text === '🔙 к услугам') {
		    return $this->getTelegram()->executeCommand('services');
	    } elseif ($text === 'Посмотреть еще') {
		    return $this->getTelegram()->executeCommand('serviceChip');
	    } elseif ($text === 'Нет, спасибо 😊') {
		    return $this->getTelegram()->executeCommand('thanks');
	    } elseif ($text === 'Отмена ❌') {
		    return $this->getTelegram()->executeCommand('cancelChip');
	    }



        //If a conversation is busy, execute the conversation command after handling the message
        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId()
        );

        //Fetch conversation command if it exists and execute it
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            return $this->telegram->executeCommand($command);
        }

        return Request::emptyResponse();
    }
}
