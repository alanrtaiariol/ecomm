<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Session\Handlers\FileHandler;

class Session extends BaseConfig
{
    public string $driver = FileHandler::class;

    public string $cookieName = 'ci_session';

    public int $expiration = 3600; // Tiempo de expiración más corto para pruebas

    public string $savePath = WRITEPATH . 'session';

    public bool $matchIP = false;

    public int $timeToUpdate = 300;

    public bool $regenerateDestroy = false;

    public ?string $DBGroup = null;

    public int $lockRetryInterval = 100_000;

    public int $lockMaxRetries = 300;
}
