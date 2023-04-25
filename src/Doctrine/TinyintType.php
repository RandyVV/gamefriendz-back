<?php

// src/Doctrine/TinyintType.php

namespace App\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class TinyintType extends Type
{
    const TINYINT = 'tinyint';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'TINYINT';
    }

    public function getName()
    {
        return self::TINYINT;
    }
}
