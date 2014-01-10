<?php
/**
 * Classe de contrôle des chemins de fichiers
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */

namespace Siwayll\Deuton;

/**
 * Classe de contrôle des chemins de fichiers
 *
 * @package    Deuton
 * @subpackage Core
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */
class Path
{
    /**
     * Chemin absolu vers le fichier
     *
     * @var string
     */
    protected $_path = '';

    /**
     * Mode silencieux
     * À mettre dans $option du construct pour annuler les envois d'exception
     */
    const SILENT = 18;

    /**
     * Test le chemin relatif $filePath
     *
     * @param string $filePath Chemin relatif à tester
     * @param mixed  $option   Constante à mettre pour changer le comportement (voir SILENT)
     *
     * @throws Exception Fichier introuvable.
     * @uses Path::test()
     * @uses Path::$_slientMode
     */
    public function __construct($filePath, $option = 0)
    {
        $this->_path = $this->test($filePath);

        if ($this->_path == false) {
            if (!$option == self::SILENT) {
                throw new Exception('Fichier introuvable : ' . $filePath);
            }
        }
    }

    /**
     * Donne le chemin absolue vers le fichier
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * Renvois le chemin du fichier ou du dossier
     *
     * @return string
     */
    public function get()
    {
        if (is_dir($this->_path)) {
            return $this->_path . DIRECTORY_SEPARATOR;
        } else {
            return $this->_path;
        }
    }

    /**
     * Permet d'ajouter des dossiers dans lesquelles chercher les fichiers
     *
     * @param string $path Dossier à ajouter
     *
     * @return boolean True si l'opération c'est bien déroulée.
     * @static
     */
    static public function addPath($path)
    {
        $path = realpath($path);
        if (!$path) {
            return false;
        }

        $usePaths = explode(PATH_SEPARATOR, get_include_path());
        foreach ($usePaths as $usePath) {
            if ($usePath == $path) {
                return true;
            }
        }

        set_include_path(get_include_path()
            . PATH_SEPARATOR . $path
        );

        return true;
    }

    /**
     * Test le chemin
     *
     * @param string $filePath Chemin vers le fichier
     *
     * @return mixed le chemin du fichier ou FALSE si il n'existe aucun fichier
     */
    private function test($filePath)
    {
        $usePaths = explode(PATH_SEPARATOR, get_include_path());
        foreach ($usePaths as $usePath) {
            if ($usePath != '.') {
                $testFilePath = $usePath . DIRECTORY_SEPARATOR . $filePath;
            } else {
                $testFilePath = $filePath;
            }
            if (file_exists($testFilePath)) {
                return realpath($testFilePath);
            }
        }

        return false;
    }
}

