<?php
/**
 * Récupération d'une variable
 *
 * @author  Siwaÿll <sanath.labs@gmail.com>
 * @license beerware http://wikipedia.org/wiki/Beerware
 */

namespace Siwayll\Deuton;

/**
 * Récupération d'une variable
 *
 * @author  Siwaÿll <sanath.labs@gmail.com>
 * @license beerware http://wikipedia.org/wiki/Beerware
 */
abstract class Param
{
    protected static $opt;

    protected $value = null;

    protected $useTrim = true;

    protected $notEmpty = true;

    protected $setControl = 'default';

    /**
     * Liste des codes utilisés pour la récupération des variables
     *
     * @var string[]
     */
    protected $paramCode = [];

    /**
     *
     */
    public function __construct()
    {
        if (!empty($this->paramCode)) {
            foreach ($this->paramCode as $key) {
                if (isset(self::$opt->{$key})) {
                    $this->value = self::$opt->{$key};
                }
            }
            unset($key);
        }

        if ($this->value === null) {
            $this->getViaPrompt();
        }
    }


    /**
     * Activation du prompt pour avoir la valeur de la variable
     *
     * @return self
     */
    protected function getViaPrompt()
    {
        $get = new Display\Get();
        $get->useTrim($this->useTrim)
            ->notEmpty($this->notEmpty)
            ->setMessage($this->message);

        if ($this->setControl === 'default') {
            $get->setControl([$this, 'ctrl']);
        } else {
            $get->setControl($this->setControl);
        }
        $this->value = $get->run();

        return $this;
    }

    /**
     * Récupération des options passées lors de l'execution du script
     *
     * @param Opt $opt options
     *
     * @return void
     */
    final public static function setOpt(Opt $opt)
    {
        self::$opt = $opt;
    }

    /**
     * Renvois la valeur demandée
     *
     * @return string
     */
    final public function get()
    {
        return $this->value;
    }

    /**
     * Test bidon pour avoir une valeur par défaut
     *
     * @param mixed $foo Variable à tester
     *
     * @return boolean
     * @ignore
     */
    public function ctrl($foo)
    {
        return true;
    }
}
