<?php
/**
 * Joomlatools Pages
 *
 * @copyright   Copyright (C) 2018 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/joomlatools/joomlatools-pages for the canonical source repository
 */

class ComPagesObjectConfigFrontmatter extends KObjectConfigYaml
{
    private $__content  = '';
    private $__filename = '';
    private $__hash     = '';

    public function fromFile($filename, $object = true)
    {
        //Store the filename
        $this->__filename = $filename;

        return parent::fromFile($filename, $object);
    }

    public function fromString($string, $object = true)
    {
        // Calculate the hash
        $this->__hash = hash("crc32b", $string);

        // Normalize line endings to Unix style.
        $string = preg_replace("/(\r\n|\r)/", "\n", $string);

        if (strpos($string, "---") !== false)
        {
            if(preg_match('#^\s*---(.*|[\s\S]*)\s*---#siU', $string, $matches))
            {
                $data = parent::fromString($matches[1], false);

                //Handle dynamic data
                array_walk_recursive ($data, function(&$value, $key)
                {
                    if(is_string($value) && strpos($value, 'data://') === 0)
                    {
                        $matches = array();
                        preg_match('#data\:\/\/([^\[]+)(?:\[(.*)\])*#si', $value, $matches);

                        if(!empty($matches[0]))
                        {
                            $data = KObjectManager::getInstance()
                                ->getObject('data.registry')
                                ->fromPath($matches[1]);

                            if($data && !empty($matches[2])) {
                                $data = $data->get($matches[2]);
                            }

                            $value = $data;
                        }
                    }
                });

                $this->merge($data);
            }

            $this->setContent(str_replace($matches[0], '', $string));
        }
        else $this->setContent($string);

        return $object ? $this : $this->toArray();
    }

    public function toString()
    {
        $string = $this->getContent();

        //Add frontmatter
        if($this->count()) {
            $string = "---\n" . trim(parent::toString()) . "\n---\n\n" . $string;
        }

        // Normalize line endings to Unix style.
        $string = preg_replace("/(\r\n|\r)/", "\n", $string);

        return $string;
    }

    public function getContent()
    {
        return $this->__content;
    }

    public function setContent($content)
    {
        $this->__content = trim($content);
        return $this;
    }

    public function getFilename()
    {
        return $this->__filename;
    }

    public function getFiletype()
    {
        return pathinfo($this->getFilename(), PATHINFO_EXTENSION);
    }

    public function getHash()
    {
        return $this->__hash;
    }
}