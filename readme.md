## Medlib Installer


#### Installation

Medlib utilizes [Composer](https://getcomposer.org) to manage its dependencies. So, before using Laravel, make sure you have Composer installed on your machine.

```bash
composer global require "medlib/installer"
```
Make sure to place the `$HOME/.composer/vendor/bin` directory (or the equivalent directory for your OS) in your $PATH so the `medlib` executable can be located by your system.

#### Manually

You should clone this repository to any location on your system, then run the `composer install` command within the cloned directory so the installer's dependencies will be installed. Finally add that location to your system's $PATH so that the `medlib` executable can be run from anywhere on your system.


#### Running
After purchasing a Github token, run the `medlib register` command with your API token generated from the [Github](https://github.com) website.

    medlib register token-value

#### Creating Projects

Once your Github token has been registered, you can run the `new` command to create new projects:

    medlib new project-name

After the project has been created, don't forget to run your database migrations!
