<?php
/**
 * Interface pour les modules
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */

namespace Deuton;

/**
 * Interface pour les modules
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */
interface iModule
{
    /**
     * initialisation du module
     *
     * @return void
     */
    public static function init();

    /**
     * Execution du module
     *
     * @param \Deuton\Opt $opt Paramètres de l'utilisateur
     *
     * @return void
     */
    public static function run(\Deuton\Opt $opt);

    /**
     * Affichage de l'aide
     *
     * @return void
     */
    public static function help();
}
