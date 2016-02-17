<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;

use Zend\Http\Client as HttpClient;
use Application\HttpJson\Client as HttpJsonClient;

class Module implements ConsoleBannerProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\HttpJson\Client' => function($serviceManager) {
                    $httpClient = $serviceManager->get('HttpClient');
                    $httpJsonClient = new HttpJsonClient($httpClient);
                    return $httpJsonClient;
                },
                'HttpClient' => function($serviceManager) {
                    $httpClient = new HttpClient();
                    # use curl adapter as standard has problems with ssl
                    $httpClient->setAdapter('Zend\Http\Client\Adapter\Curl');
                    return $httpClient;
                },
            ),
        );
    }
    
    public function getConsoleBanner(Console $console)
    {
        return 'ICNDb 1.0.0 App by Gustavo De Souza';
    }
    
    public function getConsoleUsage(Console $console)
    {
        return array(
            'Console commands to retrive and display a joke from ICNDb API',
            'show random joke'                              => 'Show a random joke',
            'show random joke [--firstName=] [--lastName=]' => 'Show a random joke with main character specified',
            'show random joke [--limitTo=[]]'               => 'Show a random joke and limit to certain category',
            
            'Optional Argumens for the query',
            array( '--firstName='   , 'First Name'          , 'First Name can be specified i.e. --firstName="Gustavo"' ),
            array( '--lastName='    , 'Last Name'           , 'Last Name can be specified i.e. --lastName="De Souza"' ),
            array( '--limitTo=[]'   , 'Limit to Category'   , 'To limit the jokes to certain category, i.e. --limitTo=[nerdy]' ),
            
        );
    }
}
