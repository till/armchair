<?php

require_once dirname(__FILE__) . '/../ArmChair.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category Testing
 * @package  ArmChair
 * @author   Till Klampaeckel <till@php.net>
 * @version  Release: @package_version@
 * @link     http://github.com/till/armchair
 */
class ArmChairTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->config = parse_ini_file(dirname(__FILE__) . '/test.ini');
        var_dump($this->config);
    }

    public function testAddDocument()
    {
    }

    public function testGetDocument()
    {
    }

    public function testDeleteDocument()
    {
    }

    public function testUpdateDocument()
    {

    }
}
