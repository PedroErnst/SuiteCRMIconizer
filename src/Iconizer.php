<?php

namespace Iconizer;

use Iconizer\Command\AddIconCommand;
use Symfony\Component\Console\Application;


class Iconizer extends Application
{

    public function __construct()
    {
        parent::__construct();
        $this->add(new AddIconCommand());
        Config::setVar('base_dir', dirname(__DIR__));
    }
}