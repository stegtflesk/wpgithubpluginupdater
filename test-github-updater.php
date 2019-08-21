<?php
/**
 * Plugin Name:       Test github updater
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Prove of concept. Testing how to implement github release controlled updates.
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Jesper Ager
 * Author URI:        https://github.com/stegtflesk
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       test-github-updater
 * Domain Path:       /languages
 */

use GithubUpdater\Controller\GithubPluginUpdateController;

require __DIR__ . '/vendor/autoload.php';

$updateController = new GithubPluginUpdateController(__FILE__);