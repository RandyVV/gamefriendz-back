<?php

namespace App\Message\Igdb;

class ImportPlatformTask
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = [
            'name' => $data['name'] ?? '',
            'igdb_id' => $data['igdb_id'] ?? null,
            'slug' => $data['slug'] ?? ''
        ];
    }

    public function getData(): array
    {
        return $this->data;
    }

}
