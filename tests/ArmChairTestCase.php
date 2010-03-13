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
    protected $armchair, $config;

    public function setUp()
    {
        $this->config = @parse_ini_file(dirname(__FILE__) . '/test.ini');
        if ($this->config === false) {
            $this->markTestSkipped(
                'This test requires a test.ini file.'
            );
        }

        $server = $this->config['server'] . $this->config['database'];

        $this->armchair = new ArmChair($server);
    }

    public function testAddDocument()
    {
        $data = array('foo' => 'bar');
        $this->assertTrue($this->armchair->addDocument($data)->ok);
    }

    public function testGetDocument()
    {
        $_id = 'foobar-' . time();
        $data = array('_id' => $_id, 'foo' => 'bar');

        $this->armchair->addDocument($data);

        $document = $this->armchair->get($_id);

        $this->assertEquals($_id, $document->_id);
        $this->assertEquals('bar', $document->foo);
    }

    public function testDeleteDocument()
    {
        $_id  = 'deleted-' . time();
        $data = array('_id' => $_id, 'foo' => 'bar');

        $document = $this->armchair->addDocument($data);

        $response = $this->armchair->deleteDocument($_id, $document->rev);
        $this->assertTrue($response->ok);
    }

    public function testUpdateDocument()
    {
        $_id  = 'update-' . time();
        $data = array('_id' => $_id, 'foo' => 'bar');

        $document = $this->armchair->addDocument($data);
        //var_dump($document);

        $update = array(
            'foo'   => 'foobar',
            '_rev'  => $document->rev,
            'hello' => 'world',
        );

        $response = $this->armchair->updateDocument(
            $document->id,
            $update
        );

        $this->assertTrue($response->ok);
    }

    public function testGetView()
    {
        $_id  = 'view-' . time();
        $data = array(
            '_id'      => '_design/' . $_id,
            'language' => 'javascript',
            'views'    => array(
                'count' => array(
                   'map' =>  'function(doc){emit(1,1);}',
                )
            )
        );

        $document = $this->armchair->addDocument($data);
        
        // Fetch the view.
        $response = $this->armchair->getView($_id, 'count');
        
        $this->assertTrue($response->ok);
    }    
}