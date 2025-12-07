<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_fiats', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16)->unique();
            $table->json('pay_types')->nullable();
            $table->unsignedInteger('rows')->default(5);
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('polling_interval_seconds')->default(30);
            $table->timestamp('last_polled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_fiats');
    }
};


