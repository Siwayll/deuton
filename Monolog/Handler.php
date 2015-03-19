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
        switch ($record['level_name']) {

            default:
                $message = $record['message'];
                break;
        }

        Display::line($message);
    }
}

