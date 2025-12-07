<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_fiat_id')->constrained('market_fiats')->cascadeOnDelete();
            $table->string('market', 32)->index();
            $table->string('asset', 16)->default('USDT');
            $table->string('buy_price', 64)->nullable();
            $table->string('sell_price', 64)->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->unique(['market_fiat_id', 'market']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_prices');
    }
};


