<?php

namespace App\Discord\Commands;

use CharlotteDunois\Yasmin\Models\Message;
use Illuminate\Support\Str;
use Revolution\DiscordManager\Concerns\Input;

class ArgvCommand
{
    use Input;

    /**
     * @var string
     */
    public $command = 'argv {test} {--text=}';

    /**
     * @param Message $message
     *
     * @return string
     */
    public function __invoke(Message $message)
    {
        $argv = explode(' ', Str::after($message->content, config('services.discord.prefix')));

        $input = $this->input($argv);

        return sprintf(
            'argv! argument:**%s** option:**%s**',
            $input->getArgument('test'),
            $input->getOption('text')
        );
    }
}
