<?php

declare(strict_types=1);

use App\Enums\MarketEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('market_fiats', function (Blueprint $table) {
            $table->string('market', 32)
                ->default(MarketEnum::BINANCE->value)
                ->after('id');
        });

        DB::table('market_fiats')->update([
            'market' => MarketEnum::BINANCE->value,
        ]);

        Schema::table('market_fiats', function (Blueprint $table) {
            $table->dropUnique('market_fiats_code_unique');
            $table->unique(['market', 'code']);
        });
    }

    public function down(): void
    {
        Schema::table('market_fiats', function (Blueprint $table) {
            $table->dropUnique('market_fiats_market_code_unique');
            $table->unique('code');
            $table->dropColumn('market');
        });
    }
};


