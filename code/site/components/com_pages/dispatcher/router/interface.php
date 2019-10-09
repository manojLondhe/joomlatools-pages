<?php
/**
 * Joomlatools Pages
 *
 * @copyright   Copyright (C) 2018 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/joomlatools/joomlatools-pages for the canonical source repository
 */

/**
 * Dispatcher Router Interface
 *
 * Provides route building and parsing functionality
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher\Router
 */
interface ComPagesDispatcherRouterInterface
{
    /**
     * Compile a route
     *
     * @param string|ComPagesDispatcherRouterRouteInterface $route The route to resolve
     * @param array $parameters Route parameters
     * @return ComPagesDispatcherRouterRouteInterface
     */
    public function compile($route, array $parameters = array());

    /**
     * Resolve a route
     *
     * @param string|ComPagesDispatcherRouterRouteInterface|KObjectInterface $route The route to resolve
     * @return false| ComPagesDispatcherRouterInterface Returns the matched route or false if no match was found
     */
    public function resolve($route);

    /**
     * Generate a route
     *
     * @param string|ComPagesDispatcherRouterRouteInterface|KObjectInterface $route The route to resolve
     * @param array $parameters Route parameters
     * @return false|KHttpUrlInterface Returns the generated route
     */
    public function generate($route, array $parameters = array());

    /**
     * Qualify a route
     *
     * Replace the url authority with the authority of the request url
     * @param ComPagesDispatcherRouterRouteInterface $route The route to qualify
     * @return string
     */
    public function qualify(ComPagesDispatcherRouterRouteInterface $route);

    /**
     * Get a resolver based on the route
     *
     * @param string|ComPagesDispatcherRouterRouteInterface|KObjectInterface $route The route to resolve
     * @return false|ComPagesDispatcherRouterInterface
     */
    public function getResolver($route);

    /**
     * Set the request object
     *
     * @param KControllerRequestInterface $request A request object
     * @return ComPagesDispatcherRouterInterface
     */
    public function setRequest(KControllerRequestInterface $request);

    /**
     * Get the request object
     *
     * @return KControllerRequestInterface
     */
    public function getRequest();
}
