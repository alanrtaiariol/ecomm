<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Log extends BaseConfig
{
    // Nivel de logging permitido
    public $threshold = 1;

    // Directorio donde se almacenarán los archivos de log
    public $path = WRITEPATH . 'logs';

    // Permisos para los archivos de log
    public $filePermission = 0644;

    // Formato de fecha y hora para los mensajes de log
    public $dateFormat = 'Y-m-d H:i:s';

    // Configuración de los tipos de log (opcional)
    public $logLevels = [
        'emergency' => 'emergency',
        'alert'     => 'alert',
        'critical'  => 'critical',
        'error'     => 'error',
        'warning'   => 'warning',
        'notice'    => 'notice',
        'info'      => 'info',
        'debug'     => 'debug',
        'emergency' => 'emergency',
    ];
}
