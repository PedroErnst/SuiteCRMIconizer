<?php

namespace Iconizer\Command;

use Iconizer\Config;
use Iconizer\Conversion\Conversion;
use Iconizer\Conversion\ConversionFactory;
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
     * @var array
     */
    private $conversions;

    private $fileName;

    private $iconName;

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
            )
            ->setConversions();
    }

    private function setConversions()
    {
        $this->conversions = [
            '/images/library/{$name}/{$name}.png' => ['Copy'],
        ];

        return $this;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->iconName = $input->getArgument('name');
        $this->fileName = $this->iconName . '.png';
        $force = $input->getOption('force');

        $output->writeln('Attempting to add an icon: ');

        try {
            $this->fileChecker->check($this->fileName, $force);
        } catch (\Exception $e) {
            $output->writeln('--- ERROR: ' . $e->getMessage());
            return;
        }

        try {
            $this->performConversions($this->fileName);
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return;
        }

        $output->writeln($this->iconName . ' added successfully!');
    }

    /**
     * @param $fileName
     * @throws \Exception
     */
    private function performConversions($fileName)
    {
        foreach ($this->conversions as $targetDir => $steps) {
            $path = Config::getVar('base_dir') . '/images/png/' . $fileName;

            $directoryPath = Config::getVar('base_dir') . str_replace('{$name}', $this->iconName, $targetDir);
            foreach ($steps as $step) {

                /** @var Conversion $conversion */

                $conversion = ConversionFactory::getConversion($step, $path, $directoryPath);
                $path = $conversion->convert();
            }
        }
    }
}