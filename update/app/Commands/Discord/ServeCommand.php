<?php

namespace App\Commands\Discord;

use CharlotteDunois\Yasmin\Interfaces\DMChannelInterface;
use CharlotteDunois\Yasmin\Interfaces\TextChannelInterface;
use CharlotteDunois\Yasmin\Models\Message;
use LaravelZero\Framework\Commands\Command;
use Revolution\DiscordManager\Facades\DiscordManager;
use Revolution\DiscordManager\Facades\Yasmin;

class ServeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Discord bot';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Yasmin::on('error', function ($error) {
            $this->error($error);
        });

        Yasmin::on('ready', function () {
            $this->info('Logged in as '.Yasmin::user()->tag.' created on '.Yasmin::user()->createdAt->format('d.m.Y H:i:s'));
        });

        Yasmin::on('message', function (Message $message) {
            $this->line('Received Message from '.$message->author->tag.' in '.($message->channel instanceof TextChannelInterface ? 'channel #'.$message->channel->name : 'DM').' with '.$message->attachments->count().' attachment(s) and '.count($message->embeds).' embed(s)');

            if ($message->author->bot) {
                return;
            }

            try {
                if ($message->channel instanceof TextChannelInterface) {
                    $this->channel($message);
                }

                if ($message->channel instanceof DMChannelInterface) {
                    $this->direct($message);
                }
            } catch (\Exception $error) {
                $this->error($error->getMessage());
            }
        });

        Yasmin::login(config('services.discord.token'));
        Yasmin::getLoop()->run();
    }

    /**
     * @param Message $message
     */
    protected function channel(Message $message)
    {
        if (! $message->mentions->members->has(config('services.discord.bot'))) {
            return;
        }

        $reply = DiscordManager::command($message);

        if (blank($reply)) {
            return;
        }

        $message->reply($reply)->done(null, function ($error) {
            $this->error($error);
        });
    }

    /**
     * @param Message $message
     */
    protected function direct(Message $message)
    {
        $reply = DiscordManager::direct($message);

        if (blank($reply)) {
            return;
        }

        $message->reply($reply)->done(null, function ($error) {
            $this->error($error);
        });
    }
}
