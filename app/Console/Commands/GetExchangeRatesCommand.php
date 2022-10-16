<?php

namespace App\Console\Commands;

use App\Models\ExchangeRate;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class GetExchangeRatesCommand extends Command
{
    private const EXCHANGE_RATE_XML_URL = 'http://www.bank.lv/vk/ecb.xml';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:getRates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get exchange rates from "http://www.bank.lv/vk/ecb.xml"';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Get exchange rates');

        $rates = $this->getExchangeRates();
        $this->updateExchangeRatesInDB($rates);

        return 1;
    }

    private function updateExchangeRatesInDB(Collection $rates):void
    {
        $allExchangeRates = ExchangeRate::query()->pluck('rate', 'currency');

        $rates->each(function($rate) use ($allExchangeRates){

            if ($rate['rate'] === ($allExchangeRates[$rate['currency']] ?? null)) {
                // is already stored, no need to update
                $this->info('NO UPDATE: '. $rate['currency']);
                return;
            }

            $this->info('UPDATE: '. $rate['currency']);

            ExchangeRate::query()->updateOrCreate(
                [
                    'currency' => $rate['currency']
                ],
                [
                    'rate' => $rate['rate']
                ]
            );
        });
    }

    private function getExchangeRates():Collection
    {
        $content = file_get_contents(self::EXCHANGE_RATE_XML_URL);
        $xmlObject = simplexml_load_string($content);

        $exchangeRates = collect();
        foreach ($xmlObject->Currencies->Currency as $data){
            $exchangeRates->push([
                'currency' => (string) $data->ID,
                'rate' => (string) $data->Rate
            ]);
        }
        return $exchangeRates;
    }
}
