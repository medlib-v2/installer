<?php

namespace Medlib\Installer;

use ZipArchive;
use RuntimeException;
use GuzzleHttp\Client;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Laravel application.';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Laravel application.')
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addOption('dev', null, InputOption::VALUE_NONE, 'Installs the latest "development" release')
            ->addOption('master', null, InputOption::VALUE_NONE, 'Installs the "master" release');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = new SymfonyStyle($input, $output);
        $this->output->writeln('<info>Installing application...</info>');

        if (! class_exists('ZipArchive')) {
            throw new RuntimeException('The Zip PHP extension is not installed. Please install it and try again.');
        }
        $this->verifyApplicationDoesntExist(
            $this->path = ($input->getArgument('name')) ? getcwd() .'/'. $input->getArgument('name') : getcwd() .'/medlib'
        );

        $installers = [
            Installation\DownloadMedlib::class,
            Installation\ComposerInstall::class,
            Installation\RunYarnInstall::class,
            Installation\CompileAssets::class
        ];

        foreach ($installers as $installer) {
            (new $installer($this, $input->getArgument('name')))->install();
        }

        $output->writeln('<comment><info>✔</info> Application ready! Build something amazing.</comment>');
    }
}