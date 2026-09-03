<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.api_key', 'sandbox_key');
        $this->baseUrl = config('services.rajaongkir.base_url', 'https://api.rajaongkir.com/starter');
    }

    /**
     * Get list of provinces.
     */
    public function getProvinces(): array
    {
        try {
            $response = Http::withHeaders(['key' => $this->apiKey])->get("{$this->baseUrl}/province");
            if ($response->successful()) {
                return $response->json('rajaongkir.results') ?? [];
            }
        } catch (\Throwable $e) {
            Log::error('RajaOngkir Provinces Error: ' . $e->getMessage());
        }

        // Fallback default Indonesian provinces list for development/demo
        return [
            ['province_id' => '1', 'province' => 'DKI Jakarta'],
            ['province_id' => '2', 'province' => 'Jawa Barat'],
            ['province_id' => '3', 'province' => 'Jawa Tengah'],
            ['province_id' => '4', 'province' => 'Jawa Timur'],
            ['province_id' => '5', 'province' => 'DI Yogyakarta'],
            ['province_id' => '6', 'province' => 'Banten'],
            ['province_id' => '7', 'province' => 'Bali'],
        ];
    }

    /**
     * Get list of cities by province ID.
     */
    public function getCities(string $provinceId = '1'): array
    {
        try {
            $response = Http::withHeaders(['key' => $this->apiKey])->get("{$this->baseUrl}/city", [
                'province' => $provinceId
            ]);
            if ($response->successful()) {
                return $response->json('rajaongkir.results') ?? [];
            }
        } catch (\Throwable $e) {
            Log::error('RajaOngkir Cities Error: ' . $e->getMessage());
        }

        // Fallback cities
        return [
            ['city_id' => '152', 'city_name' => 'Jakarta Selatan', 'type' => 'Kota', 'postal_code' => '12110'],
            ['city_id' => '153', 'city_name' => 'Jakarta Barat', 'type' => 'Kota', 'postal_code' => '11210'],
            ['city_id' => '23', 'city_name' => 'Bandung', 'type' => 'Kota', 'postal_code' => '40111'],
            ['city_id' => '444', 'city_name' => 'Surabaya', 'type' => 'Kota', 'postal_code' => '60111'],
            ['city_id' => '501', 'city_name' => 'Yogyakarta', 'type' => 'Kota', 'postal_code' => '55111'],
        ];
    }

    /**
     * Calculate shipping costs.
     */
    public function calculateCost(string $destinationCityId, int $weightGrams = 1000, string $courier = 'jne'): array
    {
        $originCityId = config('services.rajaongkir.origin_city', '152'); // Default Jakarta Selatan

        try {
            $response = Http::withHeaders(['key' => $this->apiKey])->post("{$this->baseUrl}/cost", [
                'origin' => $originCityId,
                'destination' => $destinationCityId,
                'weight' => max(100, $weightGrams),
                'courier' => strtolower($courier),
            ]);

            if ($response->successful()) {
                return $response->json('rajaongkir.results.0.costs') ?? [];
            }
        } catch (\Throwable $e) {
            Log::error('RajaOngkir Cost Error: ' . $e->getMessage());
        }

        // Fallback shipping rates calculation for production resilience
        $baseFee = match (strtolower($courier)) {
            'pos' => 15000,
            'tiki' => 18000,
            default => 20000,
        };

        $weightKg = ceil($weightGrams / 1000);
        $totalFee = $baseFee * $weightKg;

        return [
            [
                'service' => 'REG',
                'description' => 'Layanan Reguler',
                'cost' => [
                    [
                        'value' => $totalFee,
                        'etd' => '2-3',
                        'note' => ''
                    ]
                ]
            ],
            [
                'service' => 'EXPRESS',
                'description' => 'Layanan Express / Kilat',
                'cost' => [
                    [
                        'value' => $totalFee + 12000,
                        'etd' => '1-1',
                        'note' => ''
                    ]
                ]
            ]
        ];
    }
}
