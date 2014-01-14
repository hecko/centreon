<?php

namespace Test\Centreon\Centreon;

use \Centreon\Core\Config;

class ConfigTest extends \PHPUnit_Extensions_Database_TestCase
{
    private $conn = null;

    public function setUp()
    {
    parent::setUp();
    }

    public function getConnection()
    {
        if (is_null($this->conn)) {
            $dbconn = new \Centreon\Core\Db('sqlite::memory:');
            $dbconn->exec("CREATE TABLE `options` (
                `group` VARCHAR(255) NOT NULL DEFAULT 'default',
                `key` VARCHAR(255) NULL,
                `value` VARCHAR(255) NULL
            )");
            $di = new \Centreon\Core\Di();
            \Centreon\Core\Di::getDefault()->setShared('db_centreon', $dbconn);
            $this->conn = $this->createDefaultDBConnection($dbconn, 'memory');
        }
        return $this->conn;
    }

    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(DATA_DIR . '/test-config.xml');
    }

    public function testFileGet()
    {
        $filename = DATA_DIR . '/test-config.ini';
        $config = new Config($filename);
        $this->assertEquals('user1', $config->get('db_centreon', 'username'));
        $this->assertEquals(null, $config->get('db_centreon', 'novar'));
        $this->assertEquals('default', $config->get('nosection', 'novar', 'default'));
    }

    public function testGetGroup()
    {
        $filename = DATA_DIR . '/test-config.ini';
        $config = new Config($filename);
        $values = array(
            'dsn' => 'sqlite::memory:',
            'username' => 'user1'
        );
        $this->assertEquals($values, $config->getGroup('db_centreon'));
        $this->assertEquals(array(), $config->getGroup('no_section'));
    }

    public function testDbGet()
    {
        $filename = DATA_DIR . '/test-config.ini';
        $config = new Config($filename);
        $config->loadFromDb();
        $this->assertEquals('value1', $config->get('default', 'variable1'));
        $this->assertEquals('value2', $config->get('default', 'variable2'));
    }
}
