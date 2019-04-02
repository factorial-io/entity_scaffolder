<?php

/**
 * @file
 * Provide Drush integration for entity schaffolding.
 */

require_once __DIR__ . '/vendor/autoload.php';
spl_autoload_register('drush_entity_scaffolder_autoload');

use Drush\EntityScaffolder\ScaffolderBase;
use Drush\EntityScaffolder\BoilerPlate;
use Drush\EntityScaffolder\d7_1\Scaffolder as Scaffolder_d7_1;
use Drush\EntityScaffolder\Logger;

ini_set('memory_limit', -1);
define('DRUPAL_ROOT', drush_entity_scaffolder_define_drupal_root());
chdir(DRUPAL_ROOT);
define('MAINTENANCE_MODE', 'update');

/**
 * Implements hook_drush_help().
 */
function entity_scaffolder_drush_help($section) {
  switch ($section) {
    case 'drush:entity-scaffold':
      return dt('Runs Schaffolder to create entity and various preprocessing.');
  }
}

/**
 * Implements hook_drush_command().
 */
function entity_scaffolder_drush_command() {
  $items = array();

  $items['entity-scaffold'] = array(
    'description' => 'Helps create entity and fields from config files.',
    'callback' => 'drush_entity_scaffolder',
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
    'options' => array(
      'config-dir' => 'Directory to read the config file/s from.',
      'debug' => 'Runs entity_scaffolder in debug mode.',
      'info' => 'Interactive information system about supported plugins.',
      'force' => 'Do not ask questions, scaffold it',
    ),
    'examples' => array(
      'drush es --config-dir=".tools/es"' => 'Looks for config file/s in .tools/es and runs scaffolding job.',
    ),
    'aliases' => array('es'),
  );

  $items['entity-scaffold-boilerplate'] = array(
    'description' => 'Generate Boilderplate code.',
    'callback' => 'drush_entity_scaffolder_boilerplate',
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
    'arguments' => array(
      'type' => 'Boilerplate Type',
    ),
    'options' => array(
      'debug' => 'Runs entity_scaffolder in debug mode.',
    ),
    'examples' => array(
      'drush esb --debug' => 'This will provide an extensive details about boilderplate that would be generated.',
    ),
    'aliases' => array('esb'),
  );

  return $items;
}

/**
 * Run entity scaffolder.
 */
function drush_entity_scaffolder() {
  Logger::log("DRUPAL_ROOT is " . DRUPAL_ROOT, 'status');
  $scaffolder = drush_entity_scaffolder_get_scaffolder();
  if (!$scaffolder) {
    if (!($config_dir = drush_get_option('config-dir'))) {
      $config_dir = '.tools/es';
    }
    Logger::log("Make sure `" . $config_dir . "/config.yaml` exists", 'warning');
    Logger::log("Scaffolder quits without scaffolding.", 'error');
    return;
  }
  $config = $scaffolder->getConfig();
  if (isset($config['locked']) && $config['locked']) {
    Logger::log("Configuration is locked.", 'warning');
    if ($config['lock message']) {
      Logger::log("LOCK MESSAGE : " . $config['lock message'], 'warning');
    }
    Logger::log("Scaffolder quits without scaffolding.", 'error');
    return;
  }
  // Check if current scaffolder version is outdated.
  $log = $scaffolder->getLoggedScaffolderInfo();
  if (isset($log['version'])) {
    $version_compare = version_compare($scaffolder::VERSION, $log['version']);
    if ($version_compare < 0) {
      Logger::log("Entity Scaffolder found in your system is outdated. Scaffolding will exit.", 'error');
      Logger::log("
        You can upgrade the Entity Scaffolder version by going to the folder where
        it is installed and pulling the latest version from git.  You may also have
        to run composer install to install any new dependencies if needed.
        Eg.

        >> cd ~/.drush/entity_scaffolder/
        >> composer install
        >> git pull
        ", 'status');
      Logger::log("If you choose to upgrade Entity Scaffolder, the code generated henceforth could be different. Exercise caution while committing scaffolded code.", 'warning');
      return;
    }
    elseif ($version_compare > 0) {
      Logger::log("Entity Scaffolder found in your system is of newer version than the one that was used to scaffold earlier.", 'warning');
      if (drush_get_option('force', FALSE) !== TRUE) {
        $response = drush_prompt("Do you wish to continue? (y/n)", "n", TRUE);
        $response = strtolower($response);
        switch ($response) {
          case 'y':
          case 'yes':
            break;

          default:
            Logger::log("Scaffolding aborted.", 'error');
            return;
        }
      }
    }
  }
  // @todo process help here instead of scaffolding.
  if (drush_get_option('info')) {
    list($type, $name) = func_get_args();
    $scaffolder->help($type, $name);
  }
  else {
    $scaffolder->scaffold();
    $scaffolder->exportCode();
    $scaffolder->logScaffolderInfo();
    Logger::log("Scaffolding finished.", 'success');
  }
}

/**
 * Run entity scaffolder for boilderplate.
 */
function drush_entity_scaffolder_boilerplate() {
  spl_autoload_register('drush_entity_scaffolder_autoload');
  list($type) = func_get_args();
  $boilerPlate = new BoilerPlate();
  $boilerPlate->generate($type);
}

/**
 * Helper function to load the correct scaffolder based on context.
 */
function drush_entity_scaffolder_get_scaffolder() {
  // We dropped support for multiple scaffolder versions start 7.1.2 for D7.
  return new Scaffolder_d7_1();
}

/**
 * Implements the autoloader.
 */
function drush_entity_scaffolder_autoload($class) {
  if (0 !== strpos($class, 'Drush\EntityScaffolder')) {
    return;
  }
  if (is_file($file = dirname(__FILE__) . '/' . str_replace('\\', '/', $class) . '.php')) {
    require $file;
  }
  else {
    Logger::log("FILE NOT FOUND: $file", 'error');
  }
}

/**
 * Find the drupal root directory by looking in parent directories.
 *
 * If unable to discover it, fail and exit.
 */
function drush_entity_scaffolder_define_drupal_root() {
  $parent_count = 0;
  // 8 seems reasonably far to go looking up.
  while ($parent_count < 8) {
    $dir = realpath(getcwd() . str_repeat('/..', $parent_count));
    if (file_exists($dir . '/index.php')) {
      return $dir;
    }
    $parent_count++;
  }
  Logger::log("Failure: Unable to discover DRUPAL_ROOT.", 'error');
  exit(1);
}
