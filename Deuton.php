<?php
/**
 * FrameWork en lignes de commande
 *
 * @author  Siwaÿll <sanath.labs@gmail.com>
 * @license beerware http://wikipedia.org/wiki/Beerware
 */

namespace Siwayll\Deuton;

/**
 * FrameWork en lignes de commande
 *
 * @author  Siwaÿll <sanath.labs@gmail.com>
 * @license beerware http://wikipedia.org/wiki/Beerware
 */
class Deuton
{
    /**
     * Numéro de version de Deuton
     */
    const VERSION = '3.2';

    /**
     * Nom de l'interface des modules Deuton
     */
    const INTERFACE_NAME = 'Siwayll\Deuton\iModule';

    /**
     * Configuration de Deuton
     *
     * @var Config
     */
    protected static $config = false;

    /**
     * Arguments de la commande
     *
     * @var \Siwayll\Deuton\Opt
     */
    protected static $arg;

    /**
     *
     * @var \Siwayll\Deuton\Display
     */
    protected static $display;

    /**
     * Lancement de Deuton
     *
     * @param Config $conf    Configuration de base de Deuton
     *
     * @return void
     */
    public static function run(Config $conf)
    {
        self::prepare($conf);

        $className = self::$arg->getCmd();
        if (!empty($className)) {
            self::exec($className);
            self::stop();
        }

        $stopCmd = self::$config->get('core', 'stopCmd');

        do {
            Display::write(DEUTON_PROMPT);
            $taskName = trim(fgets(STDIN));
            if ($taskName == $stopCmd) {
                break;
            }
            if (strpos($taskName, ':') === 0) {
                self::$arg->parseCmd(substr($taskName, 1));
                self::exec(self::$arg->getCmd());
                continue;
            }
        } while ($taskName != $stopCmd);
    }

    protected static function exec($className)
    {
        try {
            self::validateModule($className);
            $className::run(self::$arg);
        } catch (\Exception $exc) {
            self::displayError($exc->getMessage());
            unset($exc);
        }
    }

    /**
     * Initialisation de Deuton
     *
     * @return void
     */
    public static function prepare($conf = null)
    {
        /** On empêche la répétition de la préparation **/
        if (self::$config !== false) {
            return;
        }

        self::$config = $conf;

        define('DEUTON_PROMPT', self::$config->get('display', 'prompt'));
        define('DEUTON_MIN_PROMPT', self::$config->get('display', 'minPrompt'));
        self::$arg = new Opt();

        $stopCmd = self::$config->get('core', 'stopCmd');
        if (empty($stopCmd)) {
            self::displayError('information stopCmd vide');
            self::stop();
        }
    }

    /**
     * Renvoie la configuration Deuton
     *
     * @return Config
     */
    public static function getConf()
    {
        return self::$config;
    }

    /**
     * Contrôle la validité d'un module
     *
     * @param string $className Nom de la classe à contrôler
     *
     * @return boolean
     * @throws \Siwayll\Deuton\Exception si la classe n'existe pas
     */
    protected static function validateModule($className)
    {
        if (!class_exists($className)) {
            throw new Exception('Module ' . $className . ' introuvable');
        }
        $interfaces = class_implements($className, true);
        if (isset($interfaces[self::INTERFACE_NAME])) {
            return true;
        }
        throw new Exception('Module ' . $className . ' incorrecte');
    }

    /**
     * Execute un module
     *
     * @param string              $className Nom du module à charger
     * @param \Siwayll\Deuton\Opt $arg       Options
     *
     * @return void
     */
    public static function launch($className, $arg)
    {
        self::validateModule($className);
        $className::run($arg);
    }

    /**
     * Arrêt complet du script
     *
     * @return void
     */
    public static function stop()
    {
        die("\r");
    }

    /**
     * Affiche une erreur
     *
     * @param string $message Message d'erreur
     *
     * @return void
     */
    public static function displayError($message)
    {
        $line = '{%color:red}ERROR{%color:reset} {%background:red}'
              . $message . '{%color:reset}';
        Display::line($line);
    }
}
