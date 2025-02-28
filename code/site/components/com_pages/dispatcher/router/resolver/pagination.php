<?php
/**
 * Joomlatools Pages
 *
 * @copyright   Copyright (C) 2018 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/joomlatools/joomlatools-pages for the canonical source repository
 */

/**
 * Pagination Dispatcher Route Resolver
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher\Router\Resolver
 */
class ComPagesDispatcherRouterResolverPagination extends ComPagesDispatcherRouterResolverAbstract
{
    public function resolve(ComPagesDispatcherRouterRouteInterface $route)
    {
        $state = $this->_getState($route);

        //Get the state
        if($page = $route->getPage())
        {
            if(($collection = $page->isCollection()) && isset($collection['state'])) {
                $state = $collection['state'];
            }
        }

        if($route->getFormat() == 'json')
        {
            if(isset($route->query['page']))
            {
                $page = $route->query['page'];

                if(isset($page['number']) && $state['limit']) {
                    $route->query['offset'] = ($page['number'] - 1) * (int) $state['limit'];
                }

                if(isset($page['limit'])) {
                    $route->query['limit'] = (int) $page['limit'];
                }

                if(isset($page['offset'])) {
                    $route->query['offset'] = (int) $page['offset'];
                }

                if(isset($page['total'])) {
                    $route->query['total'] = (int) $page['total'];
                }

                unset($route->query['page']);
            }
        }
        else
        {
            if(isset($route->query['page']))
            {
                $page = $route->query['page'];

                if($page && $state['limit']) {
                    $route->query['offset'] = ($page - 1) * (int) $state['limit'];
                }

                unset($route->query['page']);
            }
        }
    }

    public function generate(ComPagesDispatcherRouterRouteInterface $route)
    {
        $page = array();
        $state = $this->_getState($route);

        if($route->getFormat() == 'json')
        {
            if (isset($route->query['offset'])) {
                $page['offset'] = $route->query['offset'];
                unset($route->query['offset']);
            }

            if (isset($route->query['limit'])) {
                $page['limit'] = $route->query['limit'];
                unset($route->query['offset']);
            }

            if (isset($route->query['total'])) {
                $page['total'] = $route->query['total'];
                unset($route->query['total']);
            }

            if (isset($state['limit']) && isset($page['offset']))
            {
                if($state['limit']) {
                    $page['number'] = ceil($page['offset'] / $state['limit']) + 1;
                }

                unset($page['offset']);
            }

            $route->query['page'] = $page;
        }
        else
        {
            if (isset($state['limit']) && isset($route->query['offset']))
            {
                $page = ceil($route->query['offset'] / $state['limit']) + 1;

                if($page > 1) {
                    $route->query['page'] = $page;
                }

                unset($route->query['offset']);
            }
        }
    }

    protected function _getState(ComPagesDispatcherRouterRoutePage $route)
    {
        $state = array();

        if($page = $route->getPage())
        {
            if(($collection = $page->isCollection()) && isset($collection['state'])) {
                $state = $collection['state'];
            }
        }

        return $state;
    }
}
