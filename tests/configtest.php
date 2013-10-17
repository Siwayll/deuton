<?php
/**
 * Tests unitaires sur Config
 *
 * @package    Deuton
 * @subpackage Tests
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */

namespace Deuton;

define('TMP_DIR', realpath(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/tmp/') . '/');
define('DS', DIRECTORY_SEPARATOR);
set_include_path(
    get_include_path()
    . PATH_SEPARATOR . realpath(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/../../')
);
require_once 'deuton/deuton.php';

Deuton::prepare();


/**
 * Tests unitaires sur Config
 *
 * @package    Deuton
 * @subpackage Tests
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Config
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $data = <<<END
[section1]
key1 = toto

[section2]
key1 = toto

END;
        file_put_contents(TMP_DIR . 'test1.ini', $data);

        $data = <<<END
[section1]
key2 = tata

[section2]
key1 = tata

[section3]
key1 = tata

END;
        file_put_contents(TMP_DIR . 'test2.ini', $data);

        $data = <<<END
[section1]
var1 = {%var3}toto
var3 = result3

[section2]
var1 = {%section1:var3}suite

END;
        file_put_contents(TMP_DIR . 'testVar.ini', $data);
        $data = <<<END
[section1]
var1 = {%varrr3}toto
var3 = result3

[section2]
var1 = {%section1:var3}suite

END;
        file_put_contents(TMP_DIR . 'testVarError.ini', $data);
        $dir = TMP_DIR . 'test2.ini';
        $data = <<<END
[__config]
extends = $dir
[section1]
key1 = toto

[section2]
key1 = toto
END;
        file_put_contents(TMP_DIR . 'testExtend.ini', $data);

        $dirAlt = TMP_DIR . 'testVar.ini';
        $data = <<<END
[__config]
extends[] = $dir
extends[] = $dirAlt
[section1]
key1 = toto

[section2]
key1 = toto
END;
        file_put_contents(TMP_DIR . 'testExtendMulti.ini', $data);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown()
    {
        unlink(TMP_DIR . 'test1.ini');
        unlink(TMP_DIR . 'test2.ini');
        unlink(TMP_DIR . 'testVar.ini');
        unlink(TMP_DIR . 'testVarError.ini');
        unlink(TMP_DIR . 'testExtend.ini');
        unlink(TMP_DIR . 'testExtendMulti.ini');
    }

    /**
     * Contrôle de l'erreur sur mauvais fichier
     *
     * @return void
     * @expectedException \Deuton\Exception
     */
    public function testConstruct()
    {
        $foo = new Config('sdfkmjgdfjkmqgssdklmjg.ini');
    }

    /**
     * Contrôle du get
     *
     * @return void
     * @covers Deuton\Config::get
     */
    public function testGet()
    {
        $conf = new Config(TMP_DIR . 'test1.ini');
        $this->assertEquals($conf->get('section1', 'key1'), 'toto');
        $this->assertEquals($conf->get('section1'), array('key1' => 'toto'));
    }


    /**
     * Contrôle de la bonne gestion des variables dans les .ini
     *
     * @return void
     */
    public function testVar()
    {
        $conf = new Config(TMP_DIR . 'testVar.ini');
        $this->assertEquals($conf->get('section1', 'var1'), 'result3toto');
        $this->assertEquals($conf->get('section2', 'var1'), 'result3suite');
    }

    /**
     * Contrôle de la bonne gestion des variables dans les .ini
     *
     * @return void
     * @expectedException \Deuton\Exception
     * @expectedExceptionMessage Aucune correspondance pour varrr3
     */
    public function testVarError()
    {
        $conf = new Config(TMP_DIR . 'testVarError.ini');
    }

    /**
     * Contrôle du fonctionnement des extends
     *
     * @return void
     * @covers Deuton\Config::setExtends
     */
    public function testSetExtends()
    {
        $conf = new Config(TMP_DIR . 'test1.ini');
        $conf->setExtends(TMP_DIR . 'test2.ini');

        $this->assertEquals($conf->get('section1', 'key1'), 'toto');
        $this->assertEquals($conf->get('section1', 'key2'), 'tata');
        $this->assertEquals($conf->get('section2', 'key1'), 'toto');
        $this->assertEquals($conf->get('section3', 'key1'), 'tata');
    }

    /**
     * Contrôle du fonctionnement des extends prédéfini
     *
     * @return void
     * @covers Deuton\Config::setExtends
     */
    public function testConfigSetExtends()
    {
        $conf = new Config(TMP_DIR . 'testExtend.ini');

        $this->assertEquals($conf->get('section1', 'key1'), 'toto');
        $this->assertEquals($conf->get('section1', 'key2'), 'tata');
        $this->assertEquals($conf->get('section2', 'key1'), 'toto');
        $this->assertEquals($conf->get('section3', 'key1'), 'tata');
    }

    /**
     * Contrôle du fonctionnement des extends Multiple prédéfini
     *
     * @return void
     * @covers Deuton\Config::setExtends
     */
    public function testConfigSetExtendsMultiple()
    {
        $conf = new Config(TMP_DIR . 'testExtendMulti.ini');

        $this->assertEquals($conf->get('section1', 'key1'), 'toto');
        $this->assertEquals($conf->get('section1', 'key2'), 'tata');
        $this->assertEquals($conf->get('section2', 'key1'), 'toto');
        $this->assertEquals($conf->get('section3', 'key1'), 'tata');
    }
}

