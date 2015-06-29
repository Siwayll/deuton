<?php

namespace Siwayll\Deuton\Display;

use Siwayll\Deuton\Display;

/**
 *
 *
 * @author  Siwaÿll <sanath.labs@gmail.com>
 * @license beerware http://wikipedia.org/wiki/Beerware
 */
class Menu
{
    /**
     *
     * @var Get
     */
    protected $getter;

    private $defaultValue = null;

    /**
     * Affichage d'un menu de choix
     */
    public function __construct()
    {
        $this->getter = new Get();
        $this->getter->setMessage('  ');
    }

    /**
     * Paramétrage de la question
     *
     * @param string $question Question qui sera affichée avant le choix
     *
     * @return self
     */
    public function setQuestion($question)
    {
        $this->getter->setMessage($question);

        return $this;
    }

    /**
     * Enregistre la liste des choix du menu
     *
     * @param array $data Liste des éléments du menu
     * @return self
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function setDefaultValue($key)
    {
        $this->defaultValue = $key;
        return $this;
    }

    /**
     * Pose la question et récupère la réponse
     *
     * @return string
     */
    public function run()
    {
        $i = 1;
        $link = [];
        foreach ($this->data as $key => $value) {
            $link[$i] = $key;
            $line = '{.c:yellow}' . str_pad($i, 7, ' ', STR_PAD_BOTH) . '{.reset} '
                  . $value
            ;
            Display::line($line);
            $i++;
        }
        do {
            $selected = $this->getter->run();
            if ($selected == '') {
                $selected = $this->defaultValue;
            }
        } while (!isset($link[$selected]));

        return $link[$selected];
    }
}