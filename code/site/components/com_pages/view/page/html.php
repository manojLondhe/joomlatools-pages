<?php
/**
 * Joomlatools Pages
 *
 * @copyright   Copyright (C) 2018 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/joomlatools/joomlatools-pages for the canonical source repository
 */

class ComPagesViewPageHtml extends ComKoowaViewPageHtml
{
    protected $_base_path;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_base_path = rtrim($config->base_path, '/');

        $this->addCommandCallback('after.render' , '_processPlugins');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'base_path' => 'page://layouts',
        ));

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        parent::_fetchData($context);

        //Find the layout
        if($layout = $context->data->page->layout)
        {
            if(!parse_url($layout, PHP_URL_SCHEME)) {
                $path = $this->_base_path.'/'.$layout;
            } else {
                $path = $layout;
            }
        }
        else $path = 'com://site/pages.page.default.html';

        $context->layout = $path;
    }

    protected function _actionRender(KViewContext $context)
    {
        $data   = $context->data;
        $layout = $context->layout;

        $renderLayout = function($layout, $data) use(&$renderLayout)
        {
            //Locate the layout
            if(!parse_url($layout, PHP_URL_SCHEME)) {
                $url = 'page://layouts/'.$layout;
            } else {
                $url = $layout;
            }

            $file = $this->getObject('template.locator.factory')->locate($url);

            //Load the layout
            $layout = (new ComPagesObjectConfigPage())->fromFile($file);

            if(isset($layout->page)) {
                throw new RuntimeException('Using "page" in layout frontmatter in not allowed');
            }

            //Render the layout
            $data->append($layout);

            $this->_content = $this->getTemplate()
                ->loadString($layout->getContent(), pathinfo($file, PATHINFO_EXTENSION), $url)
                ->render(KObjectConfig::unbox($data));

            //Handle recursive layout
            if($layout->layout) {
                $renderLayout($layout->layout, $data);
            }
        };

        Closure::bind($renderLayout, $this, get_class());

        //Render the layout
        $renderLayout($layout, $data);

        return KViewAbstract::_actionRender($context);
    }


    protected function _processPlugins(KViewContextInterface $context)
    {
        $page = $context->data->page;

        if($page->process->plugins)
        {
            $content = new stdClass;
            $content->text = $context->result;

            $params = (object)$page->getProperties();

            //Trigger onContentBeforeDisplay
            $results = array();
            $results[] = $this->getTemplate()->createHelper('event')->trigger(array(
                'name'         => 'onContentBeforeDisplay',
                'import_group' => 'content',
                'attributes'   => array('com_pages.page', &$content, &$params)
            ));

            //Trigger onContentPrepare
            $results[] = $this->getTemplate()->createHelper('event')->trigger(array(
                'name'         => 'onContentPrepare',
                'import_group' => 'content',
                'attributes'   => array('com_pages.page', &$content, &$params)
            ));

            //Trigger onContentAfterDisplay
            $results[] = $this->getTemplate()->createHelper('event')->trigger(array(
                'name'         => 'onContentAfterDisplay',
                'import_group' => 'content',
                'attributes'   => array('com_pages.page', &$content, &$params)
            ));

            $context->result = trim(implode("\n", $results));;
        }
    }
}