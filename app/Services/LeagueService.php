<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;

class LeagueService
{
    private array $data;

    public function __construct()
    {
        $client = new Client(['base_uri' => 'https://www.dota2.com/']);
        $data = $client->get('webapi/IDOTA2League/GetLeagueInfoList/v001')->getBody()->getContents();
        $dataDecode = json_decode($data, true);

        $this->data = !empty($dataDecode['infos']) ? $dataDecode['infos'] : [];
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
}
