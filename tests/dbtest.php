<?php
/**
 * Tests unitaires sur DB
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
 * Tests unitaires sur DV
 *
 * @package    Deuton
 * @subpackage Tests
 * @author     Siwaÿll <sanath.labs@gmail.com>
 * @license    beerware http://wikipedia.org/wiki/Beerware
 */
class DBTest extends \PHPUnit_Framework_TestCase
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
        DB::$onlyPrepare = true;

        $data = <<<END
[database]
name = "test1"
dsn = "mysql:dbname={%dbName};host={%host};port={%port}"
host = "localhost"
dbName = "test"
port = "52236"
user = ""
password = ""
utf8 = true
error = true
profil = false
noCache = false
END;
        file_put_contents(TMP_DIR . 'test1.ini', $data);
        $data = <<<END
[database]
name = "sqlite"
dsn = "sqlite:{%path}"
path = "toto.sql"
END;
        file_put_contents(TMP_DIR . 'test2.ini', $data);
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
    }

    /**
     * Contrôle de l'erreur sur mauvais fichier
     *
     * @return void
     * @expectedException \Deuton\Exception
     */
    public function testConstructException()
    {
        $foo = DB::factory('dlkgdfgkldfg');
    }

    /**
     * Contrôle de création
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->assertEquals(
            DB::factory(TMP_DIR . 'test1.ini'),
            'mysql:dbname=test;host=localhost;port=52236'
        );
        $this->assertEquals(
            DB::factory(TMP_DIR . 'test2.ini'),
            'sqlite:toto.sql'
        );
        DB::kill('test1');
        DB::kill('sqlite');
    }

    /**
     * Contrôle de création
     *
     * @return void
     * @covers DB::get
     */
    public function testGet()
    {
        DB::factory(TMP_DIR . 'test1.ini');
        $this->assertEquals(
            DB::get('test1'),
            'mysql:dbname=test;host=localhost;port=52236'
        );
        DB::kill('test1');
    }

    /**
     * Contrôle de création
     *
     * @return void
     * @covers DB::get
     * @expectedException \Deuton\Exception
     * @expectedExceptionMessage Aucune connexion sous le nom toto
     */
    public function testGetError()
    {
        DB::get('toto');
    }

    /**
     * Destruction d'une connection
     *
     * @return void
     * @covers DB::kill
     * @expectedException \Deuton\Exception
     * @expectedExceptionMessage Aucune connexion sous le nom test1
     */
    public function testKill()
    {
        DB::factory(TMP_DIR . 'test1.ini');
        DB::kill('test1');
        DB::get('test1');
    }
}

