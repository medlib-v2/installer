<?php

namespace Medlib\Installer;

use Medlib\Installer\Interacts\InteractsWithMedlibConfiguration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TokenCommand extends BaseCommand
{
    use InteractsWithMedlibConfiguration;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display the currently registered Github API token.';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('token')
            ->setDescription('Display the currently registered Github API token');
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Github API Token:</info> '.$this->readToken());
    }
}