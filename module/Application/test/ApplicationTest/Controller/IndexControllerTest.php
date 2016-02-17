<?php

namespace ApplicationTest\Controller;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class IndexControllerTest extends AbstractConsoleControllerTestCase
{
    protected  $traceError = true;
    
    public function setUp()
    {
        $this->setApplicationConfig(
            include 'config/application.config.php'
        );
        parent::setUp();
    }
    
    /**
     * Test if IcndbJoke Action can be accessed
     */
    public function testIcndbJokeActionCanBeAccessed()
    {
        $this->dispatch('show random joke');
        $this->assertResponseStatusCode(0);
        
        $this->assertModuleName('Application');
        $this->assertControllerName('Application\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('icndb-joke');
    }
    
}