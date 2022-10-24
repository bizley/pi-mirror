<?php

declare(strict_types=1);

namespace app\models;

use RuntimeException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\httpclient\Client;

use function array_key_exists;
use function is_array;

/**
 * MPK
 * @author Bizley
 */
class MPK extends Component
{
    private const CACHE_DURATION = 600;

    /**
     * @var string API weather URL
     */
    private string $url = 'https://mpk.wroc.pl/bus_position';

    /**
     * @var Client|null HTTP client
     */
    private ?Client $client = null;

    /**
     * Returns HTTP client object.
     * @return Client
     */
    public function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * Checks current positions.
     * @return array|bool
     */
    public function checkPositions()
    {
        try {
            $response = $this->getClient()->createRequest()
                ->setMethod('post')
                ->setUrl($this->url)
                ->setData(
                    [
                        'busList' => [
                            'bus' => Yii::$app->params['buses'] ?? [],
                            'tram' => Yii::$app->params['trams'] ?? []
                        ],
                    ]
                )
                ->send();

            if (!$response->isOk) {
                throw new RuntimeException($response->data);
            }

            return $response->data;
        } catch (Throwable $e) {
            Yii::error($e->getMessage());
        }

        return false;
    }

    /**
     * Returns buses and trams.
     */
    public function getPositions(): array
    {
        if (!(Yii::$app->params['checkMPK'] ?? false)) {
            return [];
        }

        $positions = $this->checkPositions();
        if (!is_array($positions)) {
            return [];
        }

        $currentPositions = [];
        foreach ($positions as $position) {
            if (
                array_key_exists('name', $position)
                && array_key_exists('type', $position)
                && array_key_exists('x', $position)
                && array_key_exists('y', $position)
                && $this->isInRadius($position['x'], $position['y'])
            ) {
                $currentPositions[] = [
                    'position' => $this->positionToPixels($position['x'], $position['y']),
                    'nr' => $position['name'],
                    'type' => $position['type'],
                ];
            }
        }

        return $currentPositions;
    }

    private function isInRadius(float $lat, float $lng): bool
    {
        $mapBounds = Yii::$app->params['map'];
        return $lat <= $mapBounds['top']
            && $lat >= $mapBounds['bottom']
            && $lng >= $mapBounds['left']
            && $lng <= $mapBounds['right'];
    }

    private function positionToPixels(float $lat, float $lng): array
    {
        $mapBounds = Yii::$app->params['map'];
        return [
            718 - round(718 * ($lat - $mapBounds['bottom']) / ($mapBounds['top'] - $mapBounds['bottom'])),
            round(641 * ($lng - $mapBounds['left']) / ($mapBounds['right'] - $mapBounds['left'])),
        ];
    }
}
