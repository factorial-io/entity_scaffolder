<?php

/**
 * @file
 * Provide Drush integration for entity schaffolding.
 */

require_once "libraries/Spyc/Spyc.php";
require_once 'libraries/Twig/Autoloader.php';
Twig_Autoloader::register();

require_once "Utils.php";
require_once "Scaffolder.php";

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
      'fire-bazooka' => 'Deletes all previous entries and force rebuild.',
      'debug' => 'Runs entity_scaffolder in debug mode.',
    ),
    'examples' => array(
      'drush es --config-dir="_tools/es"' => 'Looks for config file/s in _tools/es and runs scaffolding job.',
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
  drush_log("Scaffolding started.", 'success');
  define('MAINTENANCE_MODE', 'update');
  ini_set('memory_limit', -1);

  $scaffolder = new Scaffolder();

  if (!drush_entity_scaffolder_check_requirements()) {
    return drush_log(dt('ERROR: Scaffolder borks.'), 'error');
  }
  $scaffolder->scaffold();
  $scaffolder->exportCode();
  drush_log("Scaffolding finished.", 'success');
}

/**
 * Helper function to get list of entities to create.
 */
function drush_entity_scaffolder_get_config_files($config_dir) {
  $files = array();
  foreach (glob($config_dir . '/*.yaml') as $file) {
    $files[] = $file;
  }
  return $files;
}

/**
 * Helper function to exit scaffolding if something is suscpecious.
 */
function drush_entity_scaffolder_check_requirements() {

  // @todo try to find a better way to figure out if the current directory is
  // Drupal root.
  $folders = Utils::getFolders('.');
  if (!in_array('sites', $folders)) {
    return FALSE;
  }

  return TRUE;
}

/**
 * Helper function to perform text replacement.
 */
function drush_entity_scaffolder_render_template($template, $replacements) {
  $loader = new Twig_Loader_Filesystem(__DIR__ . '/d7/templates');
  $twig = new Twig_Environment($loader, array(
    'debug' => TRUE,
      // 'cache' => drush_entity_scaffolder_get_twig_cache_directory(),
  ));
  $twig->addExtension(new Twig_Extension_Debug());
  return $twig->render($template . '.twig', $replacements);
}

/**
 * Helper function to render using Twig.
 */
function drush_entity_scaffolder_twig_render($dir, $template, $replacements) {
  $loader = new Twig_Loader_Filesystem($dir);
  $twig = new Twig_Environment($loader, array(
    'debug' => TRUE,
  ));
  $twig->addExtension(new Twig_Extension_Debug());
  return $twig->render($template . '.twig', $replacements);
}
