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
     * @var bool
     */
    private $force;

    /**
     * @var string
     */
    private $fileChecker;

    /**
     * @var array
     */
    private $conversions;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $iconName;

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
        $this->fileChecker = new FileChecker();
        parent::__construct($name);
    }

    /**
     * @inheritdoc
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

    /**
     * @return $this
     */
    private function setConversions()
    {
        $this->conversions = [
            '/images/library/{$name}/Create{$name}.gif' => ['Copy', 'OverlayCreateSymbol'],
            '/images/library/{$name}/Create{$name}.svg' => ['Copy', 'OverlayCreateSymbol', 'GifToSvg'],
            '/images/library/{$name}/icon_{$name}_32.gif' => ['Copy'],
            '/images/library/{$name}/icon_{$name}_32.svg' => ['Copy', 'GifToSvg'],
            '/images/library/{$name}/icon_{uc$name}.gif' => ['Copy'],
            '/images/library/{$name}/{$name}.gif' => ['Copy'],
            '/images/library/{$name}/{$name}.svg' => ['Copy', 'GifToSvg'],
            '/images/library/{$name}/sidebar/modules/{$name}.svg' => ['Copy', 'ResizeTo20x20', 'GifToSvg'],
            '/images/library/{$name}/sub_panel/{$name}.svg' => ['Copy', 'GifToSvg'],
            '/images/library/{$name}/sub_panel/modules/{$name}.svg' => ['Copy', 'GifToSvg'],
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
        $this->fileName = $input->getArgument('name');
        $this->iconName = pathinfo($this->fileName, PATHINFO_FILENAME);
        $this->force = $input->getOption('force');
        $this->output = $output;

        $this->output->writeln('Attempting to add an icon: ');

        if ($this->tryToConvert()) {
            $this->output->writeln($this->iconName . ' added successfully!');
        }
    }

    /**
     * @param OutputInterface $output
     * @return bool
     */
    private function tryToConvert()
    {
        try {
            $this->fileChecker->check($this->fileName, $this->force);
        } catch (\Exception $e) {
            $this->output->writeln('--- ERROR: ' . $e->getMessage());
            return false;
        }

        try {
            $this->performConversions();
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    private function performConversions() : void
    {
        $path = $this->convertInputImageToGif();
        foreach ($this->conversions as $targetDir => $steps) {

            $currentPath = $path;
            $baseDir = Config::getVar('base_dir') . $targetDir;
            $directoryPath = str_replace('{$name}', $this->iconName, $baseDir);
            $directoryPath = str_replace('{uc$name}', ucfirst($this->iconName), $directoryPath);

            foreach ($steps as $step) {

                /** @var Conversion $conversion */
                $conversion = ConversionFactory::getConversion($step, $currentPath, $directoryPath);
                $currentPath = $conversion->convert();
            }
            $this->output->writeln('Created file: ' . $currentPath);
        }
    }

    /**
     *
     */
    private function convertInputImageToGif()
    {
        /** @var Conversion $conversion */
        $conversion = ConversionFactory::getToGifConversion(
            Config::getVar('base_dir') . '/images/input/' . $this->fileName
        );
        return $conversion->convert();
    }
}