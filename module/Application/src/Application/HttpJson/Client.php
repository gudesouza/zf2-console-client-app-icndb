<?php
namespace Application\HttpJson;

use Zend\Http\Client as ZendHttpClient;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Exception;


class Client
{
    protected $httpClient;
    
    /**
     * 
     * @param ZendHttpClient $httpClient
     */
    public function __construct(ZendHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;        
    }
    
    /**
     * Get Json from remote URL
     * @param string $url
     * @return \Application\HttpJson\mixed
     */
    public function get($url)
    {
        return $this->dispatchRequestAndDecorateResponse($url, 'GET');
    }
    
    /**
     * Dispatch the Request and Decode Json Response
     * @param string $url
     * @param string $method
     * @param string $data
     * @return mixed
     */
    protected function dispatchRequestAndDecorateResponse ($url, $method, $data = null)
    {
        $request = new Request();
        $request->getHeaders()->addHeaders(array(
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
        ));
        $request->setUri($url);
        $request->setMethod($method);
        if ($data) {
            $request->setPost(new Parameters($data));
        }
        $response = $this->httpClient->dispatch($request);
        if (!$response) {
            throw new Exception('Failed to get Response from the API');
        }
        return json_decode($response->getContent(), true);
    }
}