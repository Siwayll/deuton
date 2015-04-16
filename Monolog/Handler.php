<?php

namespace Siwayll\Deuton\Monolog;

use Siwayll\Deuton\Display;

/**
 * Affichage monolog
 *
 * @author  Siwaÿll <sanath.labs@gmail.com>
 * @license beerware http://wikipedia.org/wiki/Beerware
 */
class Handler extends \Monolog\Handler\AbstractProcessingHandler
{
    protected function write(array $record)
    {
        $message = $record['message'];

        Display::line($message);
    }
}
