<?php
/**
 * @file
 * Provide Drush integration for entity schaffolding.
 */

require_once "spyc.php";

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
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH, // No bootstrap.
    'options' => array(
      'config-dir' => 'Directory to read the config file/s from.',
      'fire-bazooka' => 'Deletes all previous entries and force rebuild.',
    ),
    'examples' => array(
      'drush es --config-dir="_tools/entity_schaffold"' => 'Looks for config file/s in _tools/es and runs scaffolding job.',
    ),
    'aliases' => array('es'),
  );

  return $items;
}

/**
 * Rebuild the registry.
 *
 * Before calling this we need to be bootstrapped to DRUPAL_BOOTSTRAP_DATABASE.
 */
function drush_entity_scaffolder() {
  define('MAINTENANCE_MODE', 'update');
  ini_set('memory_limit', -1);

  if (!($config_dir = drush_get_option('config-dir'))) {
    return drush_log(dt('You need to specify an "config-dir" to load configurations from.'), 'error');
  }

  $entity_types = drush_entity_scaffolder_get_folders($config_dir);

  foreach ($entity_types as $entity_type) {
    switch ($entity_type) {
      case 'fpp':
        drush_entity_scaffolder_scaffold_fpp($config_dir);
        break;

      default:
        drush_log(dt('Entity Scaffolder does not support %type', array('%type' => $entity_type)), 'error');
        break;
    }
  }


  drush_log(var_dump($entity_types), 'success');
  $config = Spyc::YAMLLoad($config_dir . '/config.yaml');
  drush_log($config['type'], 'success');

}

/**
 * Helper function to get list of entities to create.
 */
function drush_entity_scaffolder_get_folders($config_dir) {
  $entity_types = array();
  foreach(glob($config_dir . '/*', GLOB_ONLYDIR) as $dir) {
    $dirname =  basename($dir) ;
    $entity_types[] = $dirname;
  }
  return $entity_types;
}

/**
 * Helper function to get list of entities to create.
 */
function drush_entity_scaffolder_get_config_files($config_dir) {
  $files = array();
  foreach(glob($config_dir . '/*.yaml') as $file) {
    $files[] = $file;
  }
  return $files;
}


/**
 * Helper functions to create FPPS.
 */
function drush_entity_scaffolder_scaffold_fpp($config_dir) {
  $config_files = drush_entity_scaffolder_get_config_files($config_dir . '/fpp');
  foreach ($config_files as $file) {
    $config = Spyc::YAMLLoad($file);
    drush_log(var_dump($config), 'success');
  }
}
