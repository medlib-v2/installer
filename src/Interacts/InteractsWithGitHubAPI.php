<?php

namespace Medlib\Installer\Interacts;

use GuzzleHttp\Client as HttpClient;

trait InteractsWithGitHubAPI
{
    /**
     * The Spark base URL.
     *
     * @var string
     */
    protected $gitHubUrl = 'https://api.github.com';

    /**
     * Get the latest Spark release version.
     * json_decode((string) (new HttpClient)->get($this->gitHubUrl, ['access_token' => __DIR__.'/token.key'])->getBody())[0]->name;
     * @return string
     */
    protected function latestMedlibRelease()
    {
        return json_decode((string) (new HttpClient)->get(
            $this->gitHubUrl.'/repos/medlib-v2/medlib/tags'
        )->getBody())[0]->name;
    }
}