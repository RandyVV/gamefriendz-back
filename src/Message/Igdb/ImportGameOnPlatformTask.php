<?php

namespace App\Message\Igdb;

class ImportGameOnPlatformTask
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = [
            'release_date' => $data['release_date'] ?? null,
            'game_igdb_id' => $data['game_igdb_id'] ?? null,
            'platform_igdb_id' => $data['platform_igdb_id'] ?? null
        ];
    }

    public function getData(): array
    {
        return $this->data;
    }

}
