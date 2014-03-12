<?php
/**
 * Affichage d'une question à l'utilisateur et récupération de la réponse
 *
 * @author  Siwaÿll <sanath.labs@gmail.com>
 * @license beerware http://wikipedia.org/wiki/Beerware
 */

namespace Siwayll\Deuton\Display;

/**
 * Affichage d'une question à l'utilisateur et récupération de la réponse
 *
 * @author  Siwaÿll <sanath.labs@gmail.com>
 * @license beerware http://wikipedia.org/wiki/Beerware
 */
class Get
{
    /**
     * Utilisation de la fonction trim sur le
     *
     * @var boolean
     */
    private $trim = false;

    /**
     *
     * @var callable
     */
    private $ctrl = null;

    /**
     * Accepte un retour vide
     *
     * @var boolean
     */
    private $acceptEmpty = true;

    /**
     * Message affiché avant la zone de saisie
     *
     * @var string
     */
    private $message = '';

    public function __construct()
    {

    }

    /**
     * Active / désactive l'utilisation d'un trim sur la valeur récupérée
     *
     * @param boolean $value activation oui / non
     *
     * @return self
     */
    public function useTrim($value)
    {
        $this->trim = (boolean) $value;
        return $this;
    }

    /**
     * Active / désactive un contrôle pour empecher que la valeur soit vide
     *
     * @param boolean $value activation oui / non
     *
     * @return self
     */
    public function notEmpty($value)
    {
        $this->acceptEmpty = (boolean) $value;
        return $this;
    }

    /**
     * Ajoute une fonction callback de contrôle de la valeur
     *
     * @param callable $func fonction de callback
     *
     * @return self
     * @throws \Siwayll\Deuton\Exception si $func n'est pas un callback
     */
    public function setControl($func)
    {
        if (!is_callable($func)) {
            throw new \Siwayll\Deuton\Exception('le paramètre de setControl doit être une function');
        }

        $this->ctrl = $func;
        return $this;
    }

    /**
     * Paramètre le message affiché pour l'utilisateur
     *
     * @param string $message message qui sera affiché à coté du prompt
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Pose la question et récupère la réponse
     *
     * @return string
     */
    public function run()
    {
        $ok = false;
        do {
            \Siwayll\Deuton\Display::write($this->message);
            \Siwayll\Deuton\Display::write(DEUTON_MIN_PROMPT);
            $foo = fgets(STDIN);
            if ($this->trim === true) {
                $foo = trim($foo);
            }

            if ($this->acceptEmpty !== true) {
                if (empty($foo)) {
                    continue;
                }
            }

            $ok = true;

            if (is_callable($this->ctrl)) {
                $ok = call_user_func($this->ctrl, $foo);
            }

        } while (!$ok);

        return $foo;
    }
}

