<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class InstallProjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     */
    protected $description = 'Полная установка проекта: migrate:fresh без подтверждений и создание пользователя admin';

    public function handle(): int
    {
        // 1) Полный сброс и повторный запуск всех миграций (без подтверждений)
        $this->info('Выполняю migrate:fresh...');
        $this->call('migrate:fresh', ['--force' => true]);

        // 2) Создание единственного пользователя admin
        $this->info('Создаю пользователя admin...');
        $user = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'admin',
                'password' => 'admin', // будет захеширован через cast в модели
            ],
        );

        $this->line('Создан пользователь:');
        $this->line('  name: admin');
        $this->line('  email: admin@admin.com');
        $this->line('  password: admin');

        $this->info('Установка завершена.');
        return self::SUCCESS;
    }
}


