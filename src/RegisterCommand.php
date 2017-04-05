<?php

namespace Medlib\Installer;

use Exception;
use GuzzleHttp\Client as HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Medlib\Installer\Interacts\InteractsWithGitHubAPI;
use Medlib\Installer\Interacts\InteractsWithMedlibConfiguration;

class RegisterCommand extends BaseCommand
{
    use InteractsWithGitHubAPI,
        InteractsWithMedlibConfiguration;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register an API token with the installer.';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('register')
            ->setDescription('Register an API token with the installer')
            ->addArgument('token', InputArgument::REQUIRED, 'The API token');
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
        if (! $this->valid($input->getArgument('token'))) {
            $this->tokenIsInvalid($output);
        }
        if (! $this->configExists()) {
            mkdir($this->homePath().'/.medlib');
        }
        $this->storeToken($input->getArgument('token'));
        $this->tokenIsValid($output);
    }

    /**
     * Determine if the given token is valid.
     *
     * @param  string  $token
     * @return bool
     */
    protected function valid($token)
    {
        try {
            (new HttpClient)->get($this->gitHubUrl.'/user', ['Authorization' => 'Bearer ' .$token, 'accept' => 'application/vnd.github.v3+json']);
            return true;
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * Inform the user that the token is valid.
     *
     * @param  OutputInterface  $output
     * @return void
     */
    protected function tokenIsValid(OutputInterface $output)
    {
        $output->writeln('Validating Token: <info>✔</info>');
        $output->writeln('<info>Thanks for registering Medlib!</info>');
    }

    /**
     * Inform the user that the token is invalid.
     *
     * @param  OutputInterface  $output
     * @return void
     */
    protected function tokenIsInvalid(OutputInterface $output)
    {
        $output->writeln('Validating Token: <fg=red>✘</>');
        $output->writeln('<comment>This API token is invalid.</comment>');
    }
}