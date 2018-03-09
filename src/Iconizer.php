<?php

namespace Iconizer;

use Iconizer\Command\AddIconCommand;
use Iconizer\Command\UseIconCommand;
use Symfony\Component\Console\Application;


class Iconizer extends Application
{

    public function __construct()
    {
        parent::__construct();
        $this->add(new AddIconCommand());
        $this->add(new UseIconCommand());
        Config::setVar('base_dir', dirname(__DIR__));
    }
}