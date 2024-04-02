<?php

use Illuminate\Foundation\Application;

return Application::configure()
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
    )
    ->withExceptions()
    ->create();
