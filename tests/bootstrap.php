<?php

use Symfony\Component\Dotenv\Dotenv;
use App\Doctrine\TinyintType;
use Doctrine\DBAL\Types\Type;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Enregistrement du type tinyint
if (!Type::hasType('tinyint')) {
    Type::addType('tinyint', TinyintType::class);
}