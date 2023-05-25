<?php

namespace App\Message\Igdb;

class ImportGameTask
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = [
            'title' => $data['title'] ?? '',
            'description' => $data['descritpion'] ?? '',
            'has_multiplayer_mode' => $data['has_multiplayer_mode'] ?? false,
            'picture' => $data['picture'] ?? '',
            'igdb_id' => $data['igdb_id'] ?? null,
            'releases' => (is_array($data['releases']) ? $data['releases'] : [])
        ];
    }

    public function getData(): array
    {
        return $this->data;
    }

}
