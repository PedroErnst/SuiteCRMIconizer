<?php

namespace Iconizer;

use Iconizer\Command\AddIconCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;


class Iconizer extends Application
{

    public function __construct()
    {
        parent::__construct();
        $this->add(new AddIconCommand());
    }
}