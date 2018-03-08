<?php

namespace Iconizer\Command;

use Iconizer\Verification\FileChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class AddIconCommand extends Command
{
    /**
     * @var string
     */
    private $fileChecker;

    /**
     * AddIconCommand constructor.
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->fileChecker = new FileChecker();
        parent::__construct($name);
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('add-icon')
            ->setDescription('Adds a new icon to the library.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the icon and file.')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Should an existing folder be overwritten?'
            )
            ->setHelp(
                'This command allows you to add a new icon to the library. ' .
                'The icon to be added should be placed in the images/png folder. ' .
                'It should be in .png format and measure 30x30, with transparent background and the icon in grey'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Attempting to add an icon: ');

        if (!$this->checkFile($input, $output)) {
            return;
        }

        // do stuff
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    private function checkFile(InputInterface $input, OutputInterface $output)
    {
        if ($this->fileChecker->check($input)) {
            $output->writeln($input->getArgument('name'));
            return true;
        }

        $output->writeln('--- ERROR: ' . $this->fileChecker->lastError());

        return true;
    }
}