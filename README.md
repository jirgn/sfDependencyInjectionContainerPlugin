sfDependencyInjectionContainerPlugin
=================

The `sfDependencyInjectionContainerPlugin` packages the dependency injection container component into Symfony.


Installation
------------

  * Install the plugin

        $ symfony plugin:install sfDependencyInjectionContainerPlugin

  * Clear the cache

        $ symfony cache:clear

  * Activate the plugin in the `config/ProjectConfiguration.class.php`:

        [php]
        class ProjectConfiguration extends sfProjectConfiguration
        {
          public function setup()
          {
            $this->enablePlugins(array(
              /* ... */
              'sfDependencyInjectionContainerPlugin',
            ));
          }
        }

>**NOTE**
>This plugin must be enabled last to be able to register events and create services through the symfony event dispatcher.




Documentation
-------------

### Bind your own services

Thanks to the sfEventDispatcher bundled with Symfony, this plugin notify an event after the service container initialization.

To listen to the event, connect to that event name (in your ProjectConfiguration class for example):

    [php]
    $this->dispatcher->connect('service_container.load_configuration', array($this, 'listenToServiceContainerLoadConfiguration'));


Here is an implementation of a listener

    [php]
    public function listenToServiceContainerLoadConfiguration(sfEvent $event)
    {
      $container = $event->getSubject();
      $loader    = new sfServiceContainerLoaderFileYaml($container);
      $loader->load(dirname(__FILE__).'/services.yml');
    }



### Use services in your application

The plugin add two methods to your ProjectConfiguration and your actions, to ease the usage.

 * getServiceContainer()
 * getService()

Example:

    [php]
    public function executeIndex(sfWebRequest $request)
    {
      $sc   = $this->getServiceContainer();
      $mail = $sc->mail;
      // or
      $mail = $this->getService('mail');
    }


### Use services in your views
The plugin adds a helper method that allows you to access the services directly in your view.

Example:

    <?php use_helper('sfDiContainer'); ?>
    <div>
      print a return value from service method here: 
      <?php echo get_service('service_id')->someFunction()?>
    </div>
    


To know more about the dependency injection container component, please refer to the official documentation : (http://components.symfony-project.org/dependency-injection/).

