<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\Logger;

class ESResponsiveImages extends ESBase implements ESBaseInterface {

  public function help() {
    Logger::log('[responsive_images] : Responsive Images', 'status');
    Logger::log('Following are the values supported in configuration', 'status');
    $headers = array('Property', 'Variable type', 'Description');
    $data = [];
    $data[] = ['name', 'string' ,'The label of the breakpoint group, which is displayed to the admins.'];
    $data[] = ['machine_name', 'machine name (string)' , 'Internal machine name.'];
    $data[] = ['mapping', 'array' ,'Array of mapping definitions that is attached to the Responsive Images'];
    $data[] = ['breakpoint_group', 'machine name (string)' , 'Breakpoint group that is associated with the image group.'];
    Logger::table($headers, $data, 'status');
  }

  public function generateCode($info) {
    // @TODO.
    Logger::log('foo');
  }

  /**
   * Loads scaffold source files.
   */
  public function loadScaffoldSourceConfigurations() {
    return Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/responsive_images');
  }

  /**
   * The main scaffolding action.
   */
  public function scaffold() {
    $config_files = $this->loadScaffoldSourceConfigurations();
    if ($config_files) {
      foreach ($config_files as $file) {
        $config = $this->getConfig($file);
        $this->generateCode($config);
      }
    }
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig(...$params) {
    list($file) = $params;
    $config_data = parent::getConfig($file);
    return $this->processConfigData($config_data);
  }
}
