<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PredictionService
{
    public function getTradeSetup(string $symbol, $tradeType = 'long', $timeframe = '1h', $marketType = 'spot')
    {
        $marketData = $this->fetchMarketData($symbol);
        return $this->cllaPredictionService($symbol, $marketData);
    }

    public function getTradeSetupForMultipleSymbols(array $symbols, $tradeType = 'long', $timeframe = '1h', $marketType = 'spot'): array
    {
        $predictions = [];
        foreach ($symbols as $symbol) {
            $predictions[$symbol] = $this->getTradeSetup($symbol, $tradeType, $timeframe, $marketType);
        }
        return $predictions;
    }

    public function getTradeSetupForAllSymbols($tradeType = 'long', $timeframe = '1h', $marketType = 'spot'): array
    {
        // Implement logic to get all symbols and return their predictions
        $allSymbols = ['BTC', 'ETH', 'LTC']; // Example symbols
        return $this->getTradeSetupForMultipleSymbols($allSymbols, $tradeType, $timeframe, $marketType);
    }

    public function cllaPredictionService($symbol, $data) {}

    public function getTopGainers()
    {
        // 1. Fetch 24-hour rolling window data (Cache for 1 minute)
        $dailyData = Cache::remember('crypto_top_24h', 60, function () {
            return Http::get('https://api.binance.com/api/v3/ticker/24hr')->json();
        });

        // 2. Fetch 1-hour rolling window data (Cache for 1 minute)
        $hourlyData = Cache::remember('crypto_top_1h', 60, function () {
            return Http::get('https://api.binance.com/api/v3/ticker', [
                'windowSize' => '1h'
            ])->json();
        });

        // 3. Process and sort the data
        $topDaily = $this->extractTopSymbol($dailyData);
        $topHourly = $this->extractTopSymbol($hourlyData);

        // 4. Return formatted JSON for your React Native frontend
        return response()->json([
            'success' => true,
            'data' => [
                'today' => $topDaily,
                'last_hour' => $topHourly
            ]
        ]);
    }

    private function extractTopSymbol($marketData)
    {
        if (!is_array($marketData)) {
            return null;
        }

        // Filter for USDT pairs only to ensure high liquidity
        $usdtPairs = array_filter($marketData, function ($coin) {
            return str_ends_with($coin['symbol'], 'USDT');
        });

        // Sort by priceChangePercent in descending order
        usort($usdtPairs, function ($a, $b) {
            return (float)$b['priceChangePercent'] <=> (float)$a['priceChangePercent'];
        });

        // Grab the #1 top gainer
        $topGainer = array_values($usdtPairs)[0] ?? null;

        if ($topGainer) {
            return [
                'symbol' => str_replace('USDT', '', $topGainer['symbol']), // e.g., 'BTC' instead of 'BTCUSDT'
                'price' => round((float)$topGainer['lastPrice'], 4),
                'change_percent' => round((float)$topGainer['priceChangePercent'], 2),
                'volume_usdt' => round((float)$topGainer['quoteVolume'], 2)
            ];
        }

        return null;
    }

    private function fetchMarketData(string $symbol)
    {
        // We will get binance open market data here with proper cache and error handling
        $marketData = Cache::remember("market_data_{$symbol}", now()->addMinutes(5), function () use ($symbol) {
            // Fetch market data from Binance API
            // Example: return Http::get("https://api.binance.com/api/v3/ticker/price?symbol={$symbol}USDT")->json();
            return [];
        });
        return [];
    }
}
