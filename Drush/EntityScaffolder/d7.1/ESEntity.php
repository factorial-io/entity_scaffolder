<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\ScaffolderBase;

class ESEntity extends ESBase {

  /**
   * The main scaffolding action.
   */
  public function scaffold() {
    $config_files = $this->loadScaffoldSourceConfigurations();
    if ($config_files) {
      $pattern_lab_entity_plugin = new ESPatternLabEntity($this->scaffolder);
      foreach ($config_files as $file) {
        $config = $this->getConfig($file);
        if ($config) {
          $pattern_lab_entity_plugin->scaffold($config);
          $this->generateCode($config);
          foreach ($this->plugins as $key => $plugin) {
            $plugin->scaffold($config);
          }
        }
      }
    }
  }


  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($file) {
    $config = parent::getConfig($file);
    if ($config) {
      $config['entity_type'] = $config['local_config']['entity_type'];
      $config['bundle'] = $config['machine_name'];
      $config['field_prefix'] = "{$config['local_config']['short_name']}_{$config['machine_name']}";
      $config['type'] = $config['local_config']['type'];
    }
    return $this->processConfigData($config);
  }

}
