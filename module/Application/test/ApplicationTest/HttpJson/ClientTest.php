<?php
namespace ApplicationTest\HttpJson;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_Assert;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

use Application\HttpJson\Client as HttpJsonClient;
use Zend\Console\Response;
use Zend\Console\Request;
use Zend\Config\Reader\Json;

class ClientTest extends AbstractConsoleControllerTestCase
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
     * Test Associative Array Returns
     */
    public function testGetReturnsAssociativeArrayContent()
    {
        $url = 'http://api.icndb.com/jokes/random';
        $method = 'GET';
        $responseContent = '{ "type": "success", "value": { "id": 268, "joke": "Time waits for no man. Unless that man is Chuck Norris." } }';
        
        $mockHttpClient = $this->getMockForSuccessfulHttpClientDispatch($url, $method, $responseContent);
        
        $httpJsonClient = new HttpJsonClient($mockHttpClient);
        
        $this->assertSame(json_decode($responseContent, true), $httpJsonClient->get($url));
        
    }
    
    
    /**
     * Mockup for Successful HTTP Client Dispatch
     * @param string $url
     * @param string $method
     * @param Json $responseContent
     * @param string $postData
     * @return \Zend\Console\Response|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockForSuccessfulHttpClientDispatch ($url, $method, $responseContent, $postData = null)
    {
        
        $response = new Response();
        $response->setContent($responseContent);
        
        $mockHttpClient = $this->getMock('Zend\Http\Client', array('dispatch'));
        $mockHttpClient->expects($this->once())
                            ->method('dispatch')
                            ->will($this->returnCallback(function($request) use ($response, $method, $url, $postData) {
                                AbstractConsoleControllerTestCase::assertSame($request->getMethod(), $method);
                                AbstractConsoleControllerTestCase::assertSame($request->getUriString(), $url);
                                
                                if ($postData) {
                                    AbstractConsoleControllerTestCase::assertSame($request->getPost()->getArrayCopy(), $postData);
                                }
                                return $response;
                            }));
        return $mockHttpClient;
    }
}