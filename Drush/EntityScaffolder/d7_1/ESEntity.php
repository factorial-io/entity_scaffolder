<?php

namespace Drush\EntityScaffolder\d7_1;

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
  public function getConfig(...$params) {
    list($file) = $params;
    $config = parent::getConfig($file);
    if ($config) {
      $config['entity_type'] = $config['local_config']['entity_type'];
      $config['bundle'] = $config['machine_name'];
      $config['field_prefix'] = "{$config['local_config']['short_name']}_{$config['machine_name']}";
      $config['type'] = $config['local_config']['type'];
    }
    return $this->processConfigData($config);
  }

  /**
   * {@inheritdoc}
   */
  public function processConfigData($config) {
    if ($config) {
      $config = parent::processConfigData($config);

      // Since field can be attached to entity, lets process field defintions
      // to have weight, if needed.
      $scaffolder_config = $this->scaffolder->getConfig();
      if (!isset($scaffolder_config['weight']['auto_populate']) || $scaffolder_config['weight']['auto_populate'] != FALSE) {
        $start = isset($scaffolder_config['weight']['start']) ? $scaffolder_config['weight']['start'] : 0;
        $delta = isset($scaffolder_config['weight']['delta']) ? $scaffolder_config['weight']['delta'] : 1;
        if ($config['fields']) {
          $this->populateWeights($config['fields'], $start, $delta);
        }
      }
      return $config;
    }
    return NULL;
  }

  /**
   * Helper function to populate weight of fields.
   */
  public function populateWeights(&$list, $start = 0, $delta = 1) {
    if (empty($list) || !is_array($list)) {
      return;
    }
    $weight = $start;
    foreach ($list as &$item) {
      if (!isset($item['weight'])) {
        $item['weight'] = $weight;
      }
      $weight = $weight + $delta;
    }
  }

}
