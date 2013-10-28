<?php
/**
 * Récupération des options en ligne de commande via -
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */

namespace Deuton;

/**
 * Récupération des options en ligne de commande via -
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */
class Opt
{
    /**
     * Tableau contenant toutes les options
     *
     * @var array
     */
    private $data = array();

    /**
     * Tableau contenant toutes les commandes
     *
     * @var array
     */
    private $cmd = array();

    /**
     *
     *
     * @var Opt
     */
    static private $self = null;

    /**
     * Récupère les options passés en ligne de commande
     */
    public function __construct()
    {
        if (self::$self !== null) {
            throw new Exception('Opt ne peut être instancié deux fois');
        }

        $this->getOpts();

        foreach ($this->data as $key => $value) {
            if (strpos($key, '-') === false) {
                continue;
            }
            /** Transformation en notation camel des variables avec un - **/
            $foo = explode('-', $key);
            $foo = array_map('ucfirst', $foo);
            $foo[0] = strtolower($foo[0]);
            $name = implode('', $foo);
            $this->data[$name] = $value;
            unset($this->data[$key]);
        }

        self::$self = $this;
    }


    /**
     * Charge les paramètres passés via la ligne de commande
     *
     * @return void
     * @global array $argv
     *
     */
    protected function getOpts()
    {
        global $argv;

        $this->parse($argv);
    }

    /**
     * Lit les paramètres saisis dans l'interface
     *
     * @return void
     */
    public function parseCmd($cmd)
    {
        $args = explode(' ', $cmd);
        $this->parse($args);
    }

    /**
     * 
     * @return void
     */
    protected function parse($args)
    {
        foreach ($args as $arg) {
            if (strpos($arg, '-') === 0) {
                $this->extractOpt($arg);
                continue;
            }
            if (strpos($arg, '.php') !== false) {
                continue;
            }

            if (strpos($arg, '-') !== false) {
                $foo = explode('-', $key);
                $foo = array_map('ucfirst', $foo);
                $arg = implode('', $foo);
                unset($foo);
            }

            $this->cmd[] = $arg;
        }

    }
    /**
     * Lit un paramètre passé en ligne de commande pour en comprendre le sens
     *
     * Sont gérés pour le moment les options sous la forme : 
     * * a=value
     * * -a=value
     * * -avalue
     * * --a=value
     * * -une-variable-longue=value
     *
     * @param string $arg chaine contenant un paramètre
     *
     * @return void
     */
    protected function extractOpt($arg)
    {
        $data = true;

        if (preg_match('#^\-([a-z]{1})([^=]+)$#i', $arg, $match)) {
            $this->data[$match[1]] = $match[2];
            return;
        }


        $arg = preg_replace('#^\-{1,2}#', '', $arg);
        if (strpos($arg, '=') !== false) {
            $data = substr(strrchr($arg, '='), 1);
            $arg = str_replace('=' . $data, '', $arg);
        }

        /**
         * Récupération des options de la forme :
         * -une-variable-longue=value
         * elle sera enregistrée sous la forme :
         * unVariableLongue = value
         */
        if (strpos($arg, '-') !== false) {
            $foo = explode('-', $arg);
            $foo = array_map('ucfirst', $foo);
            $foo[0] = strtolower($foo[0]);
            $arg = implode('', $foo);
            unset($foo);
        }

        $this->data[$arg] = $data;
    }

    /**
     * Renvois un tableau avec les paramètres
     *
     * @return string
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * Renvois un tableau avec les commandes
     *
     * @return string
     */
    public function getCmd()
    {
        if (empty($this->cmd)) {
            return null;
        }
        $className = array_pop($this->cmd);
        $className = ucfirst(strtolower($className));
        $name = '\\Modules\\';
        foreach ($this->cmd as $cmd) {
            $cmd = ucfirst(strtolower($cmd));
            $name .= $cmd . '\\';
        }

        $name .= $className;

        return $name;
    }

    /**
     * Renvois l'objet Opt en cours
     *
     * @return Opt
     */
    static public function get()
    {
        return self::$self;
    }

    /**
     * Contrôle l'existance d'un paramètre
     *
     * @param string $name Nom de la variable
     *
     * @return boolean
     */
    public function __isset($name)
    {
        if (isset($this->data[$name])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Renvois la valeur du paramètre
     *
     * @param string $name Nom de la variable
     *
     * @return string
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * Enregistre la valeur du paramètre
     *
     * @param string $name  Nom de la variable
     * @param mixed  $value Valeur de la variable
     *
     * @return string
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
}

