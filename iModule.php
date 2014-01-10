<?php
/**
 * Interface pour les modules
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */

namespace Siwayll\Deuton;

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
     * @param Opt $opt Paramètres de l'utilisateur
     *
     * @return void
     */
    public static function run(Opt $opt);

    /**
     * Affichage de l'aide
     *
     * @return void
     */
    public static function help();
}
