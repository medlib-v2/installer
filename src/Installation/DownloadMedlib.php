<?php

namespace Medlib\Installer\Installation;

use ZipArchive;
use Medlib\Installer\NewCommand;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Filesystem\Filesystem;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Console\Input\InputInterface;
use Medlib\Installer\Interacts\InteractsWithGitHubAPI;
use Medlib\Installer\Interacts\InteractsWithMedlibConfiguration;

class DownloadMedlib
{
    use InteractsWithGitHubAPI,
        InteractsWithMedlibConfiguration;

    protected $command;

    /**
     * Create a new installation helper instance.
     *
     * @param  NewCommand  $command
     */
    public function __construct(NewCommand $command)
    {
        $this->command = $command;
    }

    /**
     * Run the installation helper.
     *
     * @return void
     */
    public function install()
    {
        $this->extractZip($this->downloadZip());
        rename($this->medlibPath(), $this->command->path);
        (new Filesystem)->deleteDirectory('/tmp/medlib-tmp');
    }

    /**
     * Download the latest Medlib release.
     *
     * @return string
     */
    protected function downloadZip()
    {
        $this->command->output->writeln(
            '<info>Downloading Medlib...</info>'
        );
        file_put_contents(
            $zipPath = '/tmp/medlib-archive.zip', $this->getVersion($this->command->input)
        );

        return $zipPath;
    }

    /**
     * Get the raw Zip response for a Medlib download.
     * Get the version that should be downloaded.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @return string
     */
    protected function getVersion(InputInterface $input)
    {
        if ($input->getOption('dev')) {

            return (string) (new HttpClient)->get(
                'https://github.com/medlib-v2/medlib/archive/develop.zip',
                ['Authorization' => 'Bearer ' .$this->readToken(), 'accept' => 'application/vnd.github.v3+json']
            )->getBody();

        } elseif ($input->getOption('master')) {

            return (string) (new HttpClient)->get(
                'https://github.com/medlib-v2/medlib/archive/master.zip',
                ['Authorization' => 'Bearer ' .$this->readToken(), 'accept' => 'application/vnd.github.v3+json']
            )->getBody();

        } else {

            $release = $this->latestMedlibRelease();
            try {
                return (string) (new HttpClient)->get(
                    $this->gitHubUrl.'/repos/medlib-v2/medlib/zipball/'.$release,
                    ['Authorization' => 'Bearer ' .$this->readToken(), 'accept' => 'application/vnd.github.v3+json']
                )->getBody();
            } catch (ClientException $e) {
                if ($e->getResponse()->getStatusCode() === 401) {
                    $this->invalidToken($release);
                }
                throw $e;
            }
        }
    }

    /**
     * Extract the Medlib Zip archive.
     *
     * @param  string  $zipPath
     * @return void
     */
    protected function extractZip($zipPath)
    {
        $archive = new ZipArchive;
        $archive->open($zipPath);
        $archive->extractTo('/tmp/medlib-tmp');
        $archive->close();
        @unlink($zipPath);
    }

    /**
     * Get the release directory.
     *
     * @return string
     */
    protected function medlibPath()
    {
        return '/tmp/medlib-tmp/'.basename(
                (new Filesystem)->directories('/tmp/medlib-tmp')[0]
            );
    }

    /**
     * Inform the user that their registered Medlib token is invalid.
     * @param string $release
     * @return void
     */
    protected function invalidToken($release)
    {
        $this->command->output->writeln(
            '<fg=red>You do not own any licenses for release ['.$release.'].</>'
        );
        exit(1);
    }
}