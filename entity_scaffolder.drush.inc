<?php

/**
 * @file
 * Provide Drush integration for entity schaffolding.
 */

require_once __DIR__ . '/vendor/autoload.php';
spl_autoload_register('drush_entity_scaffolder_autoload');

use Drush\EntityScaffolder\ScaffolderBase;
use Drush\EntityScaffolder\BoilerPlate;
use Drush\EntityScaffolder\d7\Scaffolder as Scaffolder_d7;
use Drush\EntityScaffolder\d7_1\Scaffolder as Scaffolder_d7_1;
use Drush\EntityScaffolder\Utils;
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
      'fire-bazooka' => '@todo: Deletes all previous entries and force rebuild.',
      'debug' => 'Runs entity_scaffolder in debug mode.',
      'version' => 'Use a specific version of Entity Scaffolder',
    ),
    'examples' => array(
      'drush es --config-dir="_tools/es"' => 'Looks for config file/s in _tools/es and runs scaffolding job.',
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
      'version' => 'Use a specific version of Entity Scaffolder',
    ),
    'examples' => array(
      'drush esb --debug' => 'This will provide an extensive details about boilderplate that would be generated.',
    ),
    'aliases' => array('esb'),
  );

  $items['entity-scaffold-helper'] = array(
    'description' => '@todo: Helper command to interact with entity-scaffolder to know more.',
    'callback' => 'drush_entity_scaffolder_helper',
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
    'arguments' => array(
      'type' => 'entity or field',
      'name' => 'Name of the Entity or field. Eg. fpp, text, list, etc...',
    ),
    'options' => array(
      'debug' => 'Runs entity_scaffolder in debug mode.',
      'version' => 'Use a specific version of Entity Scaffolder',
    ),
    'examples' => array(
      'drush esh --debug' => 'This will provide an extensive details about entity that could be scaffolded.',
    ),
    'aliases' => array('esh'),
  );

  return $items;
}

/**
 * Run entity scaffolder.
 */
function drush_entity_scaffolder() {
  Logger::log("DRUPAL_ROOT is " . DRUPAL_ROOT, 'status');
  $scaffolder = drush_entity_scaffolder_get_scaffolder();
  $config = $scaffolder->getConfig();
  if (isset($config['locked']) && $config['locked']) {
    Logger::log("Configuration is locked.", 'warning');
    if ($config['lock message']) {
      Logger::log("LOCK MESSAGE : " . $config['lock message'], 'warning');
    }
    Logger::log("Scaffolder quits without scaffolding.", 'error');
    return ;
  }
  // Check if current scaffolder version is outdated.
  $log = $scaffolder->getLoggedScaffolderInfo();
  if (isset($log['version'])) {
    $version_compare = version_compare($scaffolder::VERSION, $log['version']);
    if ($version_compare < 0) {
      Logger::log("Entity Scaffolder found in your syste is outdated. Scaffolding will exit.", 'error');
      return;
    }
    elseif ($version_compare > 0) {
      Logger::log("Entity Scaffolder found in your system is of newer version than the one that was used to scaffold earlier.", 'warning');
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
  $scaffolder->scaffold();
  $scaffolder->exportCode();
  $scaffolder->logScaffolderInfo();
  Logger::log("Scaffolding finished.", 'success');
}

/**
 * Run entity scaffolder.
 */
function drush_entity_scaffolder_helper() {
  spl_autoload_register('drush_entity_scaffolder_autoload');
  list($type, $name) = func_get_args();
  drush_entity_scaffolder_get_scaffolder()->help($type, $name);
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
 * Helper function to load the correct scaffolder based on context;
 */
function drush_entity_scaffolder_get_scaffolder() {
  $scaffolder_base = new ScaffolderBase();
  $config = $scaffolder_base->getConfig();
  $version = isset($config['version']) ? $config['version'] : '';
  Logger::log("Scaffolder version " . $version, 'status');
  if ($v = drush_get_option('version')) {
    $version = $v;
  }
  switch ($version) {
    case 'd7':
      return new Scaffolder_d7();

    case 'd7_1':
      return new Scaffolder_d7_1();

    default:

      Logger::log("Scaffolder version " . $version . " not found. Loaded version d7_1.", 'error');
      return new Scaffolder_d7_1();
  }
}

/**
 * Implements the autoloader.
 */
function drush_entity_scaffolder_autoload($class) {
  if (0 !== strpos($class, 'Drush\EntityScaffolder')) {
    return;
  }
  if (is_file($file = dirname(__FILE__) . '/' .str_replace( '\\', '/', $class).'.php')) {
    require $file;
  }
  else {
    Logger::log("FILE NOT FOUND: $file", 'error');
  }
}

/**
 * Find the drupal root directory by looking in parent directories.
 * If unable to discover it, fail and exit.
 */
function drush_entity_scaffolder_define_drupal_root() {
  // This is the smallest number of directories to go up: from /sites/all/modules/registry_rebuild.
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
