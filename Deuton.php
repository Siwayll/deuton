<?php
/**
 * FrameWork en lignes de commande
 *
 * @author  Siwaÿll <sanath.labs@gmail.com>
 * @license beerware http://wikipedia.org/wiki/Beerware
 */

namespace Siwayll\Deuton;

require __DIR__ . DS . 'Path.php';

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
    const INTERFACE_NAME = 'Deuton\iModule';

    /**
     * Configuration de Deuton
     *
     * @var Config
     */
    protected static $config = false;

    /**
     * Arguments de la commande
     *
     * @var \Deuton\Opt
     */
    protected static $arg;

    /**
     * Lancement de Deuton
     *
     * @param string $default Nom du module à utiliser par défaut
     *
     * @return void
     */
    public static function run($default = '')
    {
        self::prepare();

        $className = self::$arg->getCmd();
        if (empty($className)) {
            self::interact($default);
            return;
        }
        try {
            if (isset(self::$arg->h)) {
                $className::help();
            } else {
                $className::run(self::$arg);
            }
        } catch (\Exception $exc) {
            Display::line('{.c:white b:red} ' . $exc->getMessage() . ' {.reset}');
        }
    }

    /**
     * Fonctionnement par mode intéractif
     *
     * @param string $default Nom du module à utiliser par défaut
     *
     * @return void
     */
    public static function interact($default = '')
    {
        self::prepare();

        /** initialisation **/
        $defaultName = null;
        if (!empty($default)) {
            $defaultName = '\\Modules\\' . $default;
            self::validateModule($defaultName);
            $defaultName::init();
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
                $className = self::$arg->getCmd();
                try {
                    self::validateModule($className);
                    $className::run(self::$arg);
                } catch (\Exception $exc) {
                    self::displayError($exc->getMessage());
                    unset($exc);
                }

                continue;
            }

            if ($default === false) {
                continue;
            }
            $param = array($taskName);
            if ($defaultName !== null) {
                $defaultName::interact($param);
            }
        } while ($taskName != $stopCmd);
    }

    /**
     * Initialisation de Deuton
     *
     * @return void
     */
    public static function prepare()
    {
        /** On empêche la répétition de la préparation **/
        if (self::$config !== false) {
            return;
        }

        /** Configuration de l'autload **/
        spl_autoload_register('\Siwayll\Deuton\Deuton::autoload');

        try {
            self::$config = new Config(__DIR__ . DS . 'deuton.ini');
        } catch (Exception $exc) {
            self::displayError($exc->getMessage());
            self::stop();
        }

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
     * Contrôle la validité d'un module
     *
     * @param string $className Nom de la classe à contrôler
     *
     * @return boolean
     * @throws \Siwayll\Deuton\Exception si la classe n'existe pas
     */
    protected static function validateModule($className)
    {
        if (class_exists($className)) {
            $interfaces = class_implements($className, true);
            if (in_array(self::INTERFACE_NAME, $interfaces)) {
                return true;
            }
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
        die("\r\n");
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

    /**
     * Chargement dynamique des classes
     *
     * @param string $name Nom du fichier à inclure
     *
     * @return boolean
     * @uses Deuton\Path
     * @throws
     */
    public static function autoload($name)
    {
        $fileName = $name . '.php';
        $fileName = str_replace('_', '\\', $fileName);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $fileName);

        $path = new Path($fileName, Path::SILENT);
        if ($path->get()) {
            include_once $path->get();
            return true;
        }

        if (self::$config->get('error', 'exception') == true) {
            throw new Exception('Classe ' . $name . ' inexistante');
        }

        return false;
    }
}
