<?php

namespace App\Discord\Directs;

use CharlotteDunois\Yasmin\Models\Message;

class DmTestCommand
{
    /**
     * @var string
     */
    public $command = 'test';

    /**
     * @param Message $message
     *
     * @return string
     */
    public function __invoke(Message $message)
    {
        return 'dm test! '.$message->author->username;
    }
}
