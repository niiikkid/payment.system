<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('market_fiats', function (Blueprint $table) {
            $table->string('bybit_payment_method', 64)
                ->nullable()
                ->after('pay_types');
            $table->decimal('bybit_amount', 24, 8)
                ->nullable()
                ->after('bybit_payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('market_fiats', function (Blueprint $table) {
            $table->dropColumn(['bybit_payment_method', 'bybit_amount']);
        });
    }
};


