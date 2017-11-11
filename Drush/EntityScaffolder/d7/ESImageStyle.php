<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\d7\ESBaseInterface;
use Drush\EntityScaffolder\d7\ESEntity;
use Drush\EntityScaffolder\Logger;

class ESImageStyle extends ESBase implements ESBaseInterface {

  public function help() {
    Logger::log('[image_style] : Image style', 'status');
    Logger::log('Following are the values supported in configuration', 'status');
    $headers = array('Property', 'Variable type', 'Description');
    $data = [];
    $data[] = ['name', 'string' ,'The label of the Style, which is displayed to the admins.'];
    $data[] = ['machine_name', 'machine name (string)' , 'Internal machine name.'];
    $data[] = ['effects', 'array' ,'Array of effects definitions that is attached to the Image Style'];
    Logger::table($headers, $data, 'status');
  }

  public function __construct(Scaffolder $scaffolder) {
    parent::__construct($scaffolder);
    $this->setTemplateDir('/image_style');
  }

  public function generateCode($info) {
    // Add entry to info file.
    $code = "\ndependencies[] = image";
    $module = 'fe_es';
    $filename = 'fe_es.info';
    $block = Scaffolder::CONTENT;
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[features_api][] = api:2";
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[image][] = " . $info['machine_name'];
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    Logger::debug($info);
  }

  /**
   * Loads scaffold source files.
   */
  public function loadScaffoldSourceConfigurations() {
    return Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/image_style');
  }

  /**
   * The main scaffolding action.
   */
  public function scaffold() {
    $config_files = $this->loadScaffoldSourceConfigurations();
    if ($config_files) {
      foreach ($config_files as $file) {
        $config = $this->getConfig($file);
        if (!empty($config['image_styles'])) {
          foreach ($config['image_styles'] as $image_style_config) {
            $this->generateCode($image_style_config);
          }
        }
      }
    }
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($file) {
    $config_data = parent::getConfig($file);
    if ($config_data) {
      if (!empty($config_data['image_styles'])) {
        $prefix_machine_name = isset($config_data['prefix']['machine_name']) ? $config_data['prefix']['machine_name'] : '';
        $prefix_name = isset($config_data['prefix']['name']) ? $config_data['prefix']['name'] : '';
        foreach ($config_data['image_styles'] as &$config) {
          $config['name'] = empty($config['name']) ? $config['machine_name'] : $config['name'];
          $config['name'] = $prefix_name . $config['name'];
          $config['machine_name'] = $prefix_machine_name . $config['machine_name'];
        }
      }
    }
    return $this->processConfigData($config_data);
  }
}
