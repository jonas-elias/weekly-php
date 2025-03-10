<?php

declare(strict_types=1);

namespace App\Config;

use Hyperf\Qdrant\ConfigInterface;

class ConfigQdrant implements ConfigInterface
{
    public function getScheme(): string
    {
        return  'http';
    }

    public function getHost(): string
    {
        return 'qdrant';
    }

    public function getPort(): int
    {
        return 6333;
    }
}
