<?php

declare(strict_types=1);

namespace app\models;

use RuntimeException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\httpclient\Client;

use function abs;
use function array_key_exists;
use function array_values;
use function is_array;
use function sqrt;

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
                && array_key_exists('k', $position)
                && $this->isInRadius($position)
            ) {
                $currentPositions[$position['k']] = [
                    'lat' => $position['x'],
                    'lng' => $position['y'],
                    'nr' => $position['name'],
                    'type' => $position['type'],
                    'moving' => false,
                ];
            }
        }

        $previousPositions = Yii::$app->cache->get('MPKPositions');
        if ($previousPositions === false) {
            Yii::$app->cache->set('MPKPositions', $currentPositions, self::CACHE_DURATION);
        } else {
            foreach ($previousPositions as $k => $data) {
                if (
                    array_key_exists($k, $currentPositions)
                    && (
                        $data['lat'] !== $currentPositions[$k]['lat']
                        || $data['lng'] !== $currentPositions[$k]['lng']
                    )
                ) {
                    $currentPositions[$k]['moving'] = true;
                }
            }
        }

        $normalizedPositions = array_values($currentPositions);
        usort($normalizedPositions, static fn ($a, $b) => $b['nr'] <=> $a['nr']);

        return $normalizedPositions;
    }

    private function isInRadius(array $position): bool
    {
        if (!array_key_exists('x', $position) || !array_key_exists('y', $position)) {
            return false;
        }

        $lat = $position['x'] ?? 0;
        $lng = $position['y'] ?? 0;

        return sqrt(
            abs(Yii::$app->params['latitude'] - $lat) ** 2 + abs(Yii::$app->params['longitude'] - $lng) ** 2
        ) <= (Yii::$app->params['radius'] ?? 0);
    }
}
