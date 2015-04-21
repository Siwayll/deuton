<?php
/**
 * Affichage standardisé avec gestion des couleurs
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */

namespace Siwayll\Deuton;

/**
 * Affichage standardisé avec gestion des couleurs
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */
class Display
{
    /**
     * Raccourcis d'écriture
     *
     * @var array
     */
    protected static $short = [
        'c' => 'color',
        'b' => 'background',
        's' => 'style',
        't' => 'template',
    ];

    /**
     * Styles pré-formatés
     *
     * @var array
     */
    protected static $templates = [
        'focus' => [
            'color' => 'blue',
            'style' => 'underscore'
        ],
        'alert' => [
            'color' => 'white',
            'background' => 'red'
        ],
        'warning' => [
            'color' => 'red'
        ],
        'success' => [
            'color' => 'green'
        ],
        'greatSuccess' => [
            'color' => 'white',
            'background' => 'green'
        ],
    ];

    /**
     * Couleurs
     *
     * @var array
     */
    static protected $colors = array(
        'color' => array(
            'black' => 30,
            'red' => 31,
            'green' => 32,
            'yellow' => 33,
            'blue' => 34,
            'magenta' => 35,
            'cyan' => 36,
            'white' => 37,
        ),
        'style' => array(
            'bright' => 1,
            'dim' => 2,
            'underscore' => 4,
            'blink' => 5,
            'reverse' => 7,
            'hidden' => 8,
        ),
        'background' => array(
            'black' => 40,
            'red' => 41,
            'green' => 42,
            'yellow' => 43,
            'blue' => 44,
            'magenta' => 45,
            'cyan' => 46,
            'white' => 47,
        )
    );

    /**
     * Affiche un élément après s'être occupé des balises couleur
     *
     * @param string $content contenu à afficher
     *
     * @return void
     */
    public static function write($content)
    {
        echo self::parseColor($content);
    }

    /**
     * Affiche un élément sur une ligne après s'être occupé des balises couleur
     *
     * @param string $content contenu à afficher
     *
     * @return void
     */
    public static function line($content)
    {
        echo self::parseColor($content) . "\r\n";
    }


    /**
     * Parse une chaine et met les codes couleurs correspondants
     *
     * @param string $string chaine à parser
     *
     * @return string
     */
    public static function parseColor($string)
    {
        $pattern = '#{.([a-z0-9 _:]+)}#i';
        preg_match_all($pattern, $string, $matchs);

        if (!isset($matchs[1])) {
            return $string;
        }

        $max = count($matchs[1]);
        for ($i = 0; $i < $max; $i++) {
            $color = [];
            $foo = explode(' ', $matchs[1][$i]);
            foreach ($foo as $order) {
                if ($order === 'reset') {
                    $color['color'] = 'reset';
                    break;
                }
                $bar = explode(':', $order);
                if (isset(self::$short[$bar[0]])) {
                    $bar[0] = self::$short[$bar[0]];
                }

                if ($bar[0] == 'template') {
                    $color = self::$templates[$bar[1]];
                    break;
                }

                $color[$bar[0]] = $bar[1];
            }

            $string = str_replace($matchs[0][$i], self::color($color), $string);
        }

        return $string;
    }

    /**
     * Création du code couleur
     *
     * @param array $color Paramétrage couleur
     *
     * @return string
     */
    protected static function color($color)
    {
        $color += array('color' => null, 'style' => null, 'background' => null);

        if ($color['color'] == 'reset') {
            return "\033[0m";
        }

        $colors = array();
        foreach (array('color', 'style', 'background') as $type) {
            if (!isset($color[$type])) {
                continue;
            }
            $code = $color[$type];
            if (isset(self::$colors[$type][$code])) {
                    $colors[] = self::$colors[$type][$code];
            }
        }

        if (empty($colors)) {
            $colors[] = 0;
        }

        return "\033[" . join(';', $colors) . 'm';
    }
}