<?php

namespace Tests\Feature;

use App\Notifications\TestNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DiscordTestCommandTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDiscordTestCommand()
    {
        Notification::fake();

        $this->artisan('discord:test')
             ->assertExitCode(0);

        Notification::assertSentTo(
            new AnonymousNotifiable(), TestNotification::class
        );
    }
}
