<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('uniqueId', [$this, 'uniqueId']),
        ];
    }

    public function uniqueId($prefix = '')
    {
        return uniqid($prefix);
    }
}