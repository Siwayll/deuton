<?php
/**
 * Gestionnaire des fichiers de configurations
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */

namespace Deuton;

/**
 * Gestionnaire des fichiers de configurations
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */
class Config
{
    /**
     * Nom de la section de configuration du .ini
     */
    const KEY_CONF = '__config';

    /**
     * Format des variables
     */
    const VAR_FORMAT = '#{%([a-z0-9_:]+)}#i';

    /**
     * Contenu du fichier de config
     *
     * @var array
     */
    private $config = null;

    /**
     * Charge un nouveau fichier de configuration
     *
     * @param string $iniFile Chemin vers le fichier de configuration
     *
     * @return void
     * @uses Path Contrôle du chemin du fichier
     */
    public function __construct($iniFile)
    {
        $iniPath = new Path($iniFile);
        $this->config = parse_ini_file($iniPath->get(), true);

        $this->headerConfig = $config = $this->get(self::KEY_CONF);
        unset($this->config[self::KEY_CONF]);

        /** Extends **/
        if (isset($config['extends'])) {
            $extends = $config['extends'];
            if (!is_array($extends)) {
                $extends = array($extends);
            }

            foreach ($extends as $path) {
                $this->setExtends($path);
            }
        }

        $this->parseVar();
    }

    /**
     * Renvois la section de configuration du .ini
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->headerConfig;
    }

    /**
     * Fait hériter le fichier de configuration d'un autre fichier.
     * Permet d'incorporer un fichier de configuration par défaut qui sera
     * surchargé par le fichier actuel.
     *
     * @param string $path Chemin vers le fichier de configuration "défaut"
     *
     * @return void
     */
    public function setExtends($path)
    {
        $iniPath = new Path($path);
        $configBase = parse_ini_file($iniPath->get(), true);
        $this->config = $this->arrayMerge($configBase, $this->config);
    }

    /**
     * Merge les tableaux en replaçants les clés identiques
     *
     * @param array &$array1 Tableau à merge
     * @param array &$array2 Tableau à merge
     *
     * @return array
     */
    private function arrayMerge(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key])
                && is_array ( $merged [$key] )
            ) {
                $merged [$key] = $this->arrayMerge($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Application des variables
     *
     * @return void
     */
    private function parseVar()
    {
        /** Parcour des options de configurations **/
        foreach ($this->config as $divName => $section) {

            /**
             * on test si dans la section il y a une variable
             * ça permet de passer à la suivante sans avoir à tout tester
             **/
            $testString = '';
            foreach ($section as $value) {
                $testString .= $value;
            }
            if (!preg_match(self::VAR_FORMAT, $testString)) {
                continue;
            }

            foreach ($section as $key => $value) {

                if (preg_match_all(self::VAR_FORMAT, $value, $matches)) {

                    for ($i = 0; $i < count($matches[0]); $i++) {
                        $id = $matches[1][$i];
                        /* = Si il y a un : dans le nom de la variable c'est
                        | qu'elle pointe sur un autre bloc
                        | sinon on prend le bloc en cours
                        `------------------------------------------------- */
                        if (strpos($id, ':') !== false) {
                            $opt = explode(':', $id);
                            $val = $this->get($opt[0], $opt[1]);
                        } else {
                            $val = $this->get($divName, $id);
                        }
                        /* = On replace la valeur de la variable dans le champ
                        `------------------------------------------------- */
                        $value = str_replace(
                            $matches[0][$i], $val, $value
                        );

                        $this->config[$divName][$key] = $value;
                    }
                }
            }
        }
    }

    /**
     * Renvois le contenu du fichier de configuration
     *
     * @return array Tableau de la configuration
     */
    public function getAll()
    {
        return $this->config;
    }

    /**
     * Renvois la valeur d'un parametre de configuration
     *
     * @param string $section Code de la section
     * @param string $key     Nom de la clé de configuration
     *
     * @return mixed null si aucune configuration ne répond aux critères
     */
    public function get($section, $key = null)
    {
        if (!empty($key)) {
            if (isset($this->config[$section][$key])) {
                return $this->config[$section][$key];
            }

        } else {
            if (isset($this->config[$section])) {
                return $this->config[$section];
            }
        }

        return null;
    }
}

