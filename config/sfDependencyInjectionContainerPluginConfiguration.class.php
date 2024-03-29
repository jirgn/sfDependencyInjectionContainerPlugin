<?php

/**
 * sfDependencyInjectionContainerPlugin configuration.
 *
 * @package     sfDependencyInjectionContainerPlugin
 * @subpackage  config
 * @author      Your name here
 * @version     SVN: $Id: PluginConfiguration.class.php 17207 2009-04-10 15:36:26Z Kris.Wallsmith $
 */
class sfDependencyInjectionContainerPluginConfiguration extends sfPluginConfiguration
{
    const VERSION = '0.5.0-DEV';
    protected
    $serviceContainer;

    /**
     * @see sfPluginConfiguration
     *
     * Initialize the service container
     * connect the listenToMethodNotFound() method to the following events:
     *  - configuration.method_not_found
     *  - controller.method_not_found
     *  - context.method_not_found
     */
    public function initialize()
    {
        $this->initializeServiceContainer();
        $this->dispatcher->connect('configuration.method_not_found', array($this, 'listenToMethodNotFound'));
        $this->dispatcher->connect('component.method_not_found', array($this, 'listenToMethodNotFound'));
        $this->dispatcher->connect('context.method_not_found', array($this, 'listenToMethodNotFound'));
    }

    /**
     * Listener method for the method_not_found event
     * Calls the getServiceContainer() method
     *
     * @return boolean
     */
    public function listenToMethodNotFound($event)
    {
        if ('getServiceContainer' == $event['method'])
        {
            $event->setReturnValue($this->getServiceContainer());

            return true;
        }

        if ('getService' == $event['method'])
        {
            $event->setReturnValue($this->getServiceContainer()->getService($event['arguments'][0]));

            return true;
        }

        return false;
    }

    /**
     * Returns the current service container instance
     *
     * @return sfServiceContainer
     */
    public function getServiceContainer()
    {
        return $this->serviceContainer;
    }

    /**
     * initialize the service container
     *
     * Notify a service_container.load_configuration event.
     * Caches container class if in production mode
     *
     */
    protected function initializeServiceContainer()
    {
        $isDebug = sfConfig::get('sf_debug', true);
        $className = 'ServiceContainer';
        $fileName = sfConfig::get('sf_app_cache_dir').'/'.$className.'.php';
        if(!$isDebug && file_exists($fileName))  {
            require_once $fileName;
            $this->serviceContainer = new $className();
        }
        else  {
            $this->serviceContainer = new sfServiceContainerBuilder();
            $this->dispatcher->notify(new sfEvent($this->serviceContainer, 'service_container.load_configuration'));
            if (!$isDebug)   {
                $dumper = new sfServiceContainerDumperPhp($this->serviceContainer);
                file_put_contents($fileName, $dumper->dump(array('class' => $className)));
            }
        }
        $this->dispatcher->notify(new sfEvent($this->serviceContainer, 'service_container.initialized'));
    }
}
