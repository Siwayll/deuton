<?php
/**
 * Récupération d'une chaine
 *
 * @package    Clock
 * @subpackage Modules
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Deuton\Param;

/**
 * Récupération d'une chaine
 *
 * @package    Clock
 * @subpackage Modules
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
class Str extends \Deuton\Param
{
    protected $maxLength = null;

    protected $onlyCaps = false;

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
        if ($this->maxLength !== null) {
            if (strlen($foo) > $this->maxLength) {
                return false;
            }
        }

        if ($this->onlyCaps === true) {
            if ($foo != strtoupper($foo)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Ajoute une longueur maximale à la chaine demandée
     *
     * @param int $length longueur maximal
     *
     * @return self
     */
    public function maxLength($length)
    {
        $this->maxLength = (int) $length;
        return $this;
    }

    /**
     * La chaine saisie ne doit contenir que des majuscules
     *
     * @return self
     */
    public function onlyCaps()
    {
        $this->onlyCaps = true;
        return $this;
    }
}
