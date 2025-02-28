<?php
/**
 * Joomlatools Pages
 *
 * @copyright   Copyright (C) 2018 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/joomlatools/joomlatools-pages for the canonical source repository
 */

class ComPagesEventSubscriberErrorhandler extends ComPagesEventSubscriberAbstract
{
    public function isEnabled()
    {
        $result = parent::isEnabled();

        //Disable error handler if directly routing to a component
        if(isset($_REQUEST['option']) && substr($_REQUEST['option'], 0, 4) == 'com_') {
            $result = false;
        }

        return $result;
    }

    public function onAfterKoowaBootstrap(KEventInterface $event)
    {
        //Catch all Joomla exceptions
        if(!defined('JOOMLATOOLS_PLATFORM')) {
            JError::setErrorHandling(E_ERROR, 'callback', array($this, 'handleException'));
        }
    }

    public function onException(KEventException $event)
    {
        $dispatcher = $this->getObject('com://site/pages.dispatcher.http');

        //Purge cache
        if($event->getCode() == KHttpResponse::NOT_FOUND)
        {
            if($dispatcher->isCacheable()) {
                $dispatcher->purge();
            }
        }

        //Handle exception
        if($dispatcher->fail($event)) {
            return true;
        }

        return false;
    }

    public function handleException(\Exception $exception)
    {
        $this->getObject('exception.handler')->handleException($exception);
    }
}