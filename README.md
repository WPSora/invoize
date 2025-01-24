# Wordpress Plugin Boilerplate

A solution for creating a Wordpress plugin with a modern development environment.

## Features

- [Composer](https://getcomposer.org/) as dependencies management.
- [Smarty](https://smarty-php.github.io/smarty/4.x/) as templating engine.
- [Laravel Database](https://github.com/illuminate/database/tree/8.x) as database abstraction layer.
- [Tailwind CSS](https://tailwindcss.com/) as CSS framework.
- [AlpineJS](https://alpinejs.dev/) as Javascript framework.

## Using template

- Create new repository using this repository as template.
- Clone the repository to your local dev environment.
- Open repo using vscode.
- Rename `ojt-wp-plugin-boilerplate` folder and file inside with the same name to your plugin name.
- Inside the file you renamed, change all necessary data like :

```
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       OJT WP Plugin Boilerplate
 * Plugin URI:        https://openjournaltheme.com
 * Description:       Description Here
 * Version:           1.0.0
 * Author:            Rahman Ramsi
 * Author URI:        https://github.com/rahmanramsi
 * Domain Path:       /languages
 */

$pluginName         = 'ojt-wp-plugin-boilerplate';
$pluginVersion      = '1.0.0';
$pluginDirectory    = __DIR__;
$pluginUrl          = plugin_dir_url(__FILE__);

```

- Open `composer.json` and change ` "OJT\\Boilerplate"` to your plugin namespace.
- Open Search tab in vscode and then replace all namespace `OJT\WPInvoice` to your plugin namespace.
- run `composer install`
- run `npm install`
- run `npm run dev`

## Documentation

Access documentation [here](docs/index.md).
