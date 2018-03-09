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

class UseIconCommand extends Command
{
    private const FILES_TO_COPY = [
        '.gif',
        '.png',
    ];

    /**
     * @var string
     */
    private $iconName;

    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var string
     */
    private $instancePath;

    /**
     * @var string
     */
    private $copyPath;

    /**
     * @var string
     */
    private $originDir;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * AddIconCommand constructor.
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('use-icon')
            ->setDescription('Use an icon for a module.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the icon to use.')
            ->addArgument('module-name', InputArgument::REQUIRED, 'The name of the module.')
            ->addArgument('instance-path', InputArgument::REQUIRED, 'The path of your SuiteCRM instance.')
            ->setHelp(
                'This command allows you to export an icon into a SuiteCRM instance module. '
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->iconName = $input->getArgument('name');
        $this->moduleName = $input->getArgument('module-name');
        $this->instancePath = $input->getArgument('instance-path');
        $this->copyPath = $this->instancePath . '/custom/themes/default/images';
        $this->originDir = Config::getVar('base_dir') . '/images/library/' . $this->iconName;
        $this->output = $output;

        $this->output->writeln('Attempting to use icon: ' . $this->iconName);

        if ($this->tryToUseIcon()) {
            $this->output->writeln($this->iconName . ' copied successfully to SuiteCRM!');
        }
    }

    private function tryToUseIcon()
    {
        if (!is_dir($this->originDir)) {
            $this->output->writeln('--- ERROR: Icon not found in library!');
            return false;
        }

        if (!is_dir($this->instancePath)) {
            $this->output->writeln('--- ERROR: SuiteCRM instance not found at: ' . $this->instancePath);
            return false;
        }

        if (!is_writable($this->copyPath)) {
            $this->output->writeln('--- ERROR: Destination folder or subfolders are not writable...');
            return false;
        }

        try {
            $this->copyFiles();
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());
            return false;
        }

        return true;
    }

    private function copyFiles()
    {
        foreach (
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->originDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            ) as $item
        ) {

            if (!$item->isDir()) {
                $replacedSubPath = str_replace($this->iconName, $this->moduleName,  $iterator->getSubPathName());
                $path = $path = $this->copyPath . DIRECTORY_SEPARATOR . $replacedSubPath;
                copy($item, $path);
                $this->output->writeln('Copied: ' . $replacedSubPath);
                continue;
            }

            $path = $this->copyPath . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if (!is_dir($path)) {
                mkdir($path);
            }
        }
    }
}