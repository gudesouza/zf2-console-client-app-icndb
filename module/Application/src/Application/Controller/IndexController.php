<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;
use Exception;

class IndexController extends AbstractActionController
{
    protected $httpJsonClient;

    public function indexAction()
    {
        return new ViewModel();
    }
    
    
    public function icndbJokeAction()
    {
        $request = $this->getRequest();
        // make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }
        
        // read mode, there is only one mode 'random' agument in route at the moment,
        // but this could be altered in module.config.php for future i.e. random|all
        $mode = $request->getParam('mode', 'random'); //defaults to random
        // read parameters form console input
        $firstName = $request->getParam('firstName', null); //default null
        $lastName = $request->getParam('lastName', null);
        $limitTo = $request->getParam('limitTo', null); //i.e category name "nerdy" "explicit"
        // building query data array from the console input
        $queryData = array();
        if ($firstName !== null) {
            $queryData['firstName'] = $firstName;
        }
        if ($lastName !== null) {
            $queryData['lastName'] = $lastName;
        }
        // only implent limitTo if both firstName and lastName are not declared, 
        // API doesn't seem to retrive correctly when category are mixed with firstName and lastName
        if ($limitTo !== null && (!isset($queryData['firstName']) && !isset($queryData['lastName']))) {
            $queryData['limitTo'] = $limitTo;
        }
        // build parameters from the console input with argument seperator &amp; conforming with ICNDB API
        $urlParams = http_build_query($queryData, '', '&amp;');
        // load config file
        $config = $this->getServiceLocator()->get('config');
        // construct ICNDB URL
        $url = $config['icndburl'] . '/' . $mode . (($urlParams) ? '?' . $urlParams : '');
        // perform JSON request
        try {
            $response = $this->getHttpJsonClient()->get($url);
            // start constructing the output
            $output = '';
            if ($response['type'] == 'success') {
                $output .= 'ID: ' . $response['value']['id'] . "\n";
                $output .= 'JOKE: ' . $response['value']['joke'] . "\n";
                if (count($response['value']['categories']) > 0) {
                    $output .= 'CATEGORY: ' . implode(',', $response['value']['categories']) . "\n";
                }
            } else {
                $output .= "There are no jokes available\n";
            }
            // send output to console
            return html_entity_decode($output);
        } catch (Exception $e) {
            return 'Caught exception: ' . $e->getMessage() . "\n";
        }
        
    }
    
    /**
     * Get the Http Json Client
     * @return object \Application\HttpJson\Client
     */
    protected function getHttpJsonClient()
    {
        if (!$this->httpJsonClient) {
            $this->httpJsonClient = $this->getServiceLocator()->get('Application\HttpJson\Client');
        }
        return $this->httpJsonClient;
    }


}

