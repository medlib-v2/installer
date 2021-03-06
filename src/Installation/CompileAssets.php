<?php

namespace Medlib\Installer\Installation;

use Medlib\Installer\NewCommand;
use Symfony\Component\Process\Process;

class CompileAssets
{
    protected $command;
    protected $name;

    /**
     * Create a new installation helper instance.
     *
     * @param NewCommand $command
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
        if (! $this->command->output->confirm('Would you like to compile your assets?', true)) {
            return;
        }
        $this->command->output->writeln('<info>Running Build Script...</info>');
        $process = (new Process('yarn run dev', $this->command->path))->setTimeout(null);
        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) {
            $this->command->output->write($line);
        });
    }
}