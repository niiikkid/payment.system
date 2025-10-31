<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\Blockchain\BlockchainServiceContract;
use Illuminate\Console\Command;

class TronScanUsdtCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example: php artisan tron:scan-usdt TJ... --limit=30
     */
    protected $signature = 'tron:scan-usdt {address : Tron адрес} {--limit=30 : Кол-во транзакций} {--outgoing : Показать исходящие вместо входящих} {--network=tron : Сеть (например: tron)}';

    /**
      * The console command description.
      */
    protected $description = 'Сканировать входящие/исходящие USDT по адресу через выбранную сеть и вывести результат';

    public function handle(BlockchainServiceContract $blockchain): int
    {
        $address = (string) $this->argument('address');
        $limit = (int) $this->option('limit');
        $network = (string) $this->option('network');

        $isOutgoing = (bool) $this->option('outgoing');
        $this->info("Запрос сети={$network} для адреса: {$address}, limit={$limit}, тип=" . ($isOutgoing ? 'исходящие' : 'входящие'));

        $items = $isOutgoing
            ? $blockchain->getUsdtOutgoingTransfers($network, $address, $limit)
            : $blockchain->getUsdtIncomingTransfers($network, $address, $limit);

        if (empty($items)) {
            $this->warn(($isOutgoing ? 'Исходящих' : 'Входящих') . ' транзакций не найдено.');
            return self::SUCCESS;
        }

        foreach ($items as $item) {
            $txid = $item['txid'] ?? '';
            $amount = $item['amount'] ?? '0.000000';
            $timestamp = $item['timestamp'] ?? null;
            $tsHuman = $timestamp ? date('Y-m-d H:i:s', (int) ($timestamp / 1000)) : 'n/a';
            $this->line(($isOutgoing ? 'Найдена исходящая' : 'Найдена входящая') . ": {$txid}, amount: {$amount}, at: {$tsHuman}");
        }

        return self::SUCCESS;
    }
}


