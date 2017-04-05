<?php

namespace Medlib\Installer\Installation;

use Medlib\Installer\NewCommand;
use Symfony\Component\Process\Process;

class RunYarnInstall
{
    protected $command;
    protected $name;

    /**
     * Create a new installation helper instance.
     *
     * @param NewCommand  $command
     * @param string $name
     */
    public function __construct(NewCommand $command, $name)
    {
        $this->command = $command;
        $this->name = $name;
    }

    /**
     * Run the installation helper.
     *
     * @return void
     */
    public function install()
    {
        if (! $this->command->output->confirm('Would you like to install the NPM dependencies?', true)) {
            return;
        }
        $this->command->output->writeln('<info>Installing NPM Dependencies...</info>');
        $process = (new Process('yarn install --no-progress', $this->command->path))->setTimeout(null);
        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }
        $process->run(function ($type, $line) {
            $this->command->output->write($line);
        });
    }
}