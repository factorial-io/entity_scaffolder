<?php

/**
 * @file
 * Provide Drush integration for entity schaffolding.
 */

require_once __DIR__ . '/vendor/autoload.php';
spl_autoload_register('drush_entity_scaffolder_autoload');

use Drush\EntityScaffolder\d7\Scaffolder;
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
    ),
    'examples' => array(
      'drush es --config-dir="_tools/es"' => 'Looks for config file/s in _tools/es and runs scaffolding job.',
    ),
    'aliases' => array('es'),
  );

  $items['entity-scaffold-buddy'] = array(
    'description' => '@todo: Helper command to interact with entity-scaffolder to know more.',
    'callback' => 'drush_entity_scaffolder_buddy',
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
    'arguments' => array(
      'type' => 'entity or field',
      'name' => 'Name of the Entity or field. Eg. fpp, text, list, etc...',
    ),
    'options' => array(
      'debug' => 'Runs entity_scaffolder in debug mode.',
    ),
    'examples' => array(
      'drush esb --debug' => 'This will provide an extensive details about entity that could be scaffolded.',
    ),
    'aliases' => array('esb'),
  );

  return $items;
}

/**
 * Run entity scaffolder.
 */
function drush_entity_scaffolder() {
  Logger::log("Scaffolding started.", 'status');
  Logger::log("DRUPAL_ROOT is " . DRUPAL_ROOT, 'status');
  // Load the D7 scaffolder by default.
  $scaffolder = new Scaffolder();
  $scaffolder->scaffold();
  $scaffolder->exportCode();
  Logger::log("Scaffolding finished.", 'success');
}

/**
 * Run entity scaffolder.
 */
function drush_entity_scaffolder_buddy() {
  spl_autoload_register('drush_entity_scaffolder_autoload');
  list($type, $name) = func_get_args();
  // Load the D7 scaffolder by default.
  $scaffolder = new Scaffolder();
  $scaffolder->help($type, $name);
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
