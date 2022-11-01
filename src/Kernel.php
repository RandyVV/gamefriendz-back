<?php

namespace App;

use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    
    public function registerBundles()
    {
        $bundles = [
            // ...
            new \Nelmio\CorsBundle\NelmioCorsBundle(),
            // ...
        ];
        // ...
    }
}
