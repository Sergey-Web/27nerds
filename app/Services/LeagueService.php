<?php

declare(strict_types=1);

namespace App\Services;

use DateTime;
use GuzzleHttp\Client as HttpClient;
use Predis\Client as RedisClient;

final class LeagueService
{
    private const BASE_URI = 'https://www.dota2.com/';
    private const URI = 'webapi/IDOTA2League/GetLeagueInfoList/v001';
    private const STORAGE_PATH = '../storage/framework/dota2/';

    private array $data;

    public function __construct()
    {
        $this->data = $this->getContent();
    }

    public function getList(?int $startTimestamp): array
    {
        if ($startTimestamp === null) {
            $leagueIds = array_column($this->data, 'league_id');
        } else {
            $leagueIds = $this->findStartLeagueDate($startTimestamp);
        }

        return $leagueIds;
    }

    public function getLeagueForId(int $id): array
    {
        $league = [];
        foreach ($this->data as $item) {
            if ($item['league_id'] === $id) {
                $league = $item;
                break;
            }
        }

        return $league;
    }

    private function findStartLeagueDate(int $startTimestamp): array
    {
        $leagues = [];
        foreach ($this->data as $item) {
            if ($startTimestamp >= $item['start_timestamp']) {
                $leagues[] = $item;
            }
        }

        return $leagues;
    }

    private function getContent(): array
    {
        $client = new HttpClient(['base_uri' => static::BASE_URI]);
        $lastModified = $client
            ->head(static::URI)
            ->getHeader('Last-Modified')[0] ?? '';

        $filePath = static::STORAGE_PATH . (new DateTime($lastModified))->getTimestamp();

        if (is_file($filePath)) {
            $data = file_get_contents($filePath);
        } else {
            $data = $client->get(static::URI)
                ->getBody()
                ->getContents();

            file_put_contents($filePath, $data);
        }

        $dataDecode = json_decode($data, true);

        return !empty($dataDecode['infos']) ? $dataDecode['infos'] : [];
    }
}
