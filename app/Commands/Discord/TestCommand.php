<?php

namespace App\Commands\Discord;

use App\Notifications\TestNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class TestCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'discord:test {--body=test}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Send Test Message';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $body = $this->option('body');

        Notification::route('discord', config('services.discord.channel'))
                    ->notify(new TestNotification($body));

        //Storage::put('test.txt', $body);
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
