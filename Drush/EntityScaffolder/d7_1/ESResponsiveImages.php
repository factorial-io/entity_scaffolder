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
    $image_style_plugin = new ESImageStyle($this->scaffolder);
    // @TODO Scaffold image styles.
    // @TODO Scaffold picture mapping.
    if (!empty($info['mapping'])) {
      foreach($info['mapping'] as $key => $map) {
        $config = $this->generateEsImageStyleData($info, $key, $map);
        $image_style_plugin->generateCode($config);
      }
    }

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

  /**
   * Generate image style data as required by ESImageStyle Scaffolder.
   */
  private function generateEsImageStyleData($info, $key, $data) {
    $machine_name = $info['machine_name'] . '__' . $key;
    $effect = 'image_scale';
    if (!empty($data['width']) && !empty($data['height'])) {
      $effect = 'focal_point_scale_and_crop';
    }
    $output = [
      'machine_name' => $machine_name,
      'name' => $machine_name,
      'effects' => [
        [
          'name' => $effect,
          'data' => $data,
          'index' => 1,
          'weight' => 1,
        ],
      ],
    ];
    return $output;
  }

}
