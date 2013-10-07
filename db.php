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
     * @param array  $ini         doit être sous la forme :
     *      dsn => ''        // chaine de connexion propre à pdo, par exemple :
     * "mysql:dbname=%s;host=%s" ou "mysql:dbname=%s;host=%s;port=%s"
     *      host => ''       // host de la connexion à la bdd
     *      dbname => ''     // Nom de la base de données
     *      user => ''       // utilisateur mysql
     *      password => ''   // mot de passe
     *      port => ''       // [facultatif], port de la connexion
     *      utf8 => true     // [facultatif], activer encodage buffer sortie
     *      error => true    // [facultatif], activer les erreurs pdo
     *      profil => false  // [facultatif], activer le profiling
     *      nocache => false // [facultatif], désactiver le cache
     *
     * @param string $otherDbName Nom de la base de données dans le cas où l'on
     * veut se connecter à une difference de celle présente dans $ini
     *
     * @return \PDO
     */
    public static function factory($ini, $otherDbName = null)
    {
        if (!is_array($ini)) {
            $iniPath = new \Deuton\Path($ini);
            $foo = parse_ini_file($iniPath->get(), true);
            $ini = $foo['database'];
            unset($foo, $iniPath);
        }

        if ($otherDbName) {
            $ini['dbname'] = $otherDbName;
        }

        if (isset(self::$_present[$ini['name']])
            && !empty(self::$_present[$ini['name']])
        ) {
            return self::$_present[$ini['name']];
        }


        $dsn = sprintf(
            $ini['dsn'], $ini['dbname'], $ini['host'], $ini['port']
        );

        self::$_present[$ini['name']] = new \PDO(
            $dsn, $ini['user'], $ini['password'], self::$_config
        );


        /* = Option d'affichage des erreurs
          | Parametrable dans le config.ini de la bdd
          `-------------------------------------------------------------------- */
        if (isset($ini['error']) && $ini['error'] == true) {
            self::$_present[$ini['name']]->setAttribute(
                \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION
            );
        }

        /* = Profiling
          `-------------------------------------------------------------------- */
        if (isset($ini['profil']) && $ini['profil'] == true) {
            self::$_present[$ini['name']]->exec('SET profiling = 1;');
        }

        /* = Cache
          `-------------------------------------------------------------------- */
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
    final static public function close($dbName)
    {
        self::$_present[$dbName] = null;
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

        throw new LibException('Aucune connexion sous le nom ' . $dbName);
    }
}

