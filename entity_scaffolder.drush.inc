<?php
/**
 * @file
 * Provide Drush integration for entity schaffolding.
 */

require_once "spyc.php";
require_once "d7/ESEntityFPP.php";

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
    $config_dir = '_tools/es';
  }

  if (!drush_entity_scaffolder_check_requirements()) {
    return drush_log(dt('ERROR: Scaffolder borks.'), 'error');
  }

  $entity_types = drush_entity_scaffolder_get_folders($config_dir);

  $code = array();
  foreach ($entity_types as $entity_type) {
    switch ($entity_type) {
      case 'fpp':
        ESEntityFPP::scaffold($config_dir, $code);
        break;

      default:
        drush_log(dt('Entity Scaffolder does not support %type', array('%type' => $entity_type)), 'error');
        break;
    }
  }

  $files = array();

  foreach ($code as $key => $content) {
    switch ($key) {
      case 'fpp':
        ESEntityFPP::addFeatureHeaderFooter($content, array());
        $files['sites/all/modules/features/fe_es/fe_es.fieldable_panels_pane_type.inc'] = implode("\n", $content);
        break;

      case 'fe_es.info':
        array_unshift($content, file_get_contents(__DIR__ . '/d7/templates/feature/fe_es/fe_es.info'));
        $content[] = 'project path = sites/all/modules/features';
        $content[] = "";
        $files['sites/all/modules/features/fe_es/fe_es.info'] = implode("\n", $content);
      default:
        # code...
        break;
    }
  }
  drush_entity_scaffolder_export_code($files);
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
function drush_entity_scaffolder_copy_folder_contents($source, $destination) {
  if (!file_exists($destination) && !is_dir($destination)) {
    mkdir($destination);
  }
  $files = array();
  foreach(glob($source . '/*.*') as $file) {
    $file_name = basename($file);
    copy($file, $destination . '/' . $file_name);
  }
}

/**
 * Helper function to exit scaffolding if something is suscpecious.
 */
function drush_entity_scaffolder_check_requirements() {

  // @todo try to find a better way to figure out if the current directory is
  // Drupal root.
  $folders = drush_entity_scaffolder_get_folders('.');
  if (!in_array('sites', $folders)) {
    return FALSE;
  }

  return TRUE;
}


/**
 * Helper function to export files.
 */
function drush_entity_scaffolder_export_code($files) {
  // Prepare module directories.
  drush_entity_scaffolder_copy_folder_contents(__DIR__ . '/d7/templates/feature/fe_es', 'sites/all/modules/features/fe_es');
  drush_entity_scaffolder_copy_folder_contents(__DIR__ . '/d7/templates/preprocess/es_helper', 'sites/all/modules/custom/es_helper');
  foreach ($files as $extention => $file_contents) {
    file_put_contents($extention, $file_contents);
  }
}
