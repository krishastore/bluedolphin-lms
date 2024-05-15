
![Logo](https://getbluedolphin.com/wp-content/uploads/2022/10/Asset-5.svg)


# BlueDolphin(LMS)

Guiding organizations to transform their learning culture with digital learning experiences.

# Setup Guide

This document describes how to set up your development environment, so that it is ready to run, develop and test this WordPress Plugin or Theme.

Suggestions are provided for the LAMP/LEMP stack and Git client are for those who prefer the UI over a command line and/or are less familiar with
WordPress, PHP, MySQL and Git - but you're free to use your preferred software.

## Setup

### LAMP/LEMP stack

Any Apache/nginx, PHP 7.x+ and MySQL 5.8+ stack running WordPress.  For example, but not limited to:
- Valet (recommended)
- Local by Flywheel
- Docker
- MAMP
- WAMP


### Composer

If [Composer](https://getcomposer.org) is not installed on your local environment, enter the following commands at the command line to install it:

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

Confirm that installation was successful by entering the `composer --version` command at the command line


### Clone Repository

Using your preferred Git client or command line, clone this repository into the `wp-content/plugins/` folder of your local WordPress installation.

If you prefer to clone the repository elsewhere, and them symlink it to your local WordPress installation, that will work as well.

If you're new to this, use [GitHub Desktop](https://desktop.github.com/) or [Tower](https://www.git-tower.com/mac)

For Plugins the cloned folder should be under `wp-content/plugins/` and for Themes under `wp-content/themes/`.

### Install Dependencies for PHP and JS

In the cloned repository's directory, at the command line, run `composer install`.
This will install the development dependencies. If you want to install just the production dependencies, run `composer install --no-dev`.

The development dependencies include:
- PHPStan
- PHPUnit
- PHP_CodeSniffer
- WordPress Coding Standards
- WordPress PHPUnit Polyfills

For the JS dependencies, run `npm install`.
To watch for changes in the JS files, run `npm run dev` or `npm run build` if present or `npm run dist` to build a new version.

### PHP_CodeSniffer

To run PHP_CodeSniffer, run `composer lint`. This will run the WordPress Coding Standards checks.
To fix automatically fixable issues, run `composer format`.


### PHPUnit

To run PHPUnit, run `phpunit` or `./vendor/bin/phpunit` if it is not configured globally.


### Next Steps

With your development environment setup, you'll probably want to start development, which is covered bellow in the **Development Guide**.


# Development Guide

This document describes the high level workflow used when working on a WordPress Plugin or Theme.

You're free to use your preferred IDE and Git client. We recommend PHPStorm or Visual Studio Code, and GitHub CLI.


## Prerequisites

If you haven't yet set up your local development environment with a WordPress Plugin repository installed, refer to the [Setup Guide](#setup-guide).

his is for a new feature that does not have a GitHub Issue number, enter a short descriptive name for the branch, relative to what you're working on
- If this is for a feature/bug that has a GitHub Issue number, enter feat/issue_name or fix/issue_name, where issue_name is a descriptive name for the issue

Once done, make sure you've switched to your new branch, and begin making the necessary code additions/changes/deletions.


## Coding Standards

Code must follow [WordPress Coding standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/), which is checked
when running tests (more on this below).


## Security and Sanitization

When [outputting data](https://developer.wordpress.org/plugins/security/securing-output/), escape it using WordPress' escaping functions such as `esc_html()`, `esc_attr__()`, `wp_kses()`, `wp_kses_post()`.

When reading [user input](https://developer.wordpress.org/plugins/security/securing-input/), sanitize it using WordPress' sanitization functions such as `sanitize_text_field()`, `sanitize_textarea_field()`.

When writing to the database, prepare database queries using ``$wpdb->prepare()``

Never trust user input. Sanitize it.

Make use of [WordPress nonces](https://codex.wordpress.org/WordPress_Nonces) for saving form submitted data.

Coding standards will catch any sanitization, escaping or database queries that aren't prepared.


## Composer Packages

We use Composer for package management.  A package can be added to one of two sections of the `composer.json` file: `require` or `require-dev`.

### "require"

Packages listed in the "require" directive are packages that the Plugin needs in order to function for end users.

These packages are included when the Plugin is deployed to WordPress.org

Typically, packages listed in this section would be libraries that the Plugin uses.

### "require-dev"

Packages listed in the "require-dev" directive are packages that the Plugin **does not** need in order to function for end users.

These packages are **not** included when the Plugin is deployed to wordpress.org

Typically, packages listed in this section would be internal development tools for testing, such as:
- Coding Standards
- PHPStan
- PHPUnit


## Committing Work

Remember to commit your changes to your branch relatively frequently, with a meaningful, short summary that explains what the change(s) do.
This helps anyone looking at the commit history in the future to find what they might be looking for.

If it's a particularly large commit, be sure to include more information in the commit description.
