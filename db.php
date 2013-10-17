<?php
/**
 * Gestionnaire de connexion à la base de données
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */

namespace Deuton;

/**
 * Gestionnaire de connexion à la base de données
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */
class DB
{

    /**
     * Contient les objets PDO de connection
     *
     * @var array
     */
    static private $_present;

    /**
     * @var boolean mode de test
     * @ignore
     */
    static public $onlyPrepare = false;

    /**
     * Parametrage de base
     *
     * @var array
     */
    static private $_config = array(
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    );

    /**
     * Inutilisé
     *
     * @ignore
     */
    private function __construct()
    {

    }

    /**
     * Crée une connection à la base de données.
     *
     * @param string $iniPath     Chemin vers un fichier du type bdd.sample.ini
     * @param string $otherDbName Nom de la base de données dans le cas où l'on
     * veut se connecter à une difference de celle présente dans $ini
     *
     * @return \PDO
     */
    public static function factory($iniPath, $otherDbName = null)
    {
        $config = new \Deuton\Config($iniPath);
        $ini = $config->get('database');
        unset($config, $iniPath);

        if ($otherDbName) {
            $ini['dbName'] = $otherDbName;
        }

        if (isset(self::$_present[$ini['name']])
            && !empty(self::$_present[$ini['name']])
        ) {
            return self::$_present[$ini['name']];
        }

        $dsn = $ini['dsn'];

        /**  **/
        if (self::$onlyPrepare === true) {
            self::$_present[$ini['name']] = $dsn;
            return $dsn;
        }

        self::$_present[$ini['name']] = new \PDO(
            $dsn, $ini['user'], $ini['password'], self::$_config
        );


        /**
         * Option d'affichage des erreurs
         * Parametrable dans le config.ini de la bdd
         **/
        if (isset($ini['error']) && $ini['error'] == true) {
            self::$_present[$ini['name']]->setAttribute(
                \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION
            );
        }

        /** Profiling **/
        if (isset($ini['profil']) && $ini['profil'] == true) {
            self::$_present[$ini['name']]->exec('SET profiling = 1;');
        }

        /** Cache **/
        if (isset($ini['nocache']) && $ini['nocache'] == true) {
            self::$_present[$ini['name']]->exec('SET SESSION query_cache_type = OFF;');
        }

        return self::$_present[$ini['name']];
    }

    /**
     * Ferme la connexion à la base
     *
     * @param string $dbName Nom de la base à fermer
     *
     * @return void
     */
    final static public function kill($dbName)
    {
        unset(self::$_present[$dbName]);
    }


    /**
     * Renvois la connexion à la base déjà paramétré
     *
     * @param string $dbName Nom de la base de données
     *
     * @return \PDO
     * @throws LibExeception Si il n'y a pas de bdd répondant au nom $dbName
     */
    final static public function get($dbName)
    {
        if (isset(self::$_present[$dbName])) {
            return self::$_present[$dbName];
        }

        throw new Exception('Aucune connexion sous le nom ' . $dbName);
    }
}

