<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Bundle\AsseticBundle\Factory;

use Assetic\Factory\Resource\ResourceInterface;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Templating\Loader\LoaderInterface;

/**
 * A file resource.
 *
 * @author Kris Wallsmith <kris.wallsmith@symfony-project.com>
 */
class FileResource implements ResourceInterface
{
    protected $loader;
    protected $parser;
    protected $bundle;
    protected $baseDir;
    protected $path;

    protected $template;

    /**
     * Constructor.
     *
     * @param LoaderInterface $loader  The templating loader
     * @param string          $bundle  The current bundle name
     * @param string          $baseDir The directory
     * @param string          $path    The file path
     */
    public function __construct(LoaderInterface $loader, $bundle, $baseDir, $path)
    {
        $this->loader = $loader;
        $this->bundle = $bundle;
        $this->baseDir = $baseDir;
        $this->path = $path;
    }

    public function isFresh($timestamp)
    {
        return $this->loader->isFresh($this->getTemplate(), $timestamp);
    }

    public function getContent()
    {
        return $this->loader->load($this->getTemplate())->getContent();
    }

    protected function getTemplate()
    {
        if (null === $this->template) {
            $this->template = self::createTemplateReference($this->bundle, substr($this->path, strlen($this->baseDir)));
        }

        return $this->template;
    }

    static private function createTemplateReference($bundle, $file)
    {
        $parts = explode('/', strtr($file, '\\', '/'));
        $elements = explode('.', array_pop($parts));

        return new TemplateReference($bundle, implode('/', $parts), $elements[0], $elements[1], $elements[2]);
    }

    public function __sleep()
    {
        return array('path');
    }

    public function __wakeup()
    {
        throw new \Exception(__CLASS__.' cannot be unserialized.');
    }
}
