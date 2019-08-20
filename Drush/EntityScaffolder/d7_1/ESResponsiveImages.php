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
    $picture_plugin = new ESPicture($this->scaffolder);

    if (!empty($info['mapping'])) {
      $multipliers = !empty($info['multipliers']) ? $info['multipliers'] : [1];
      // Scaffold image styles.
      foreach($info['mapping'] as $key => $map) {
        foreach ($multipliers as $multiplier) {
          $config = $this->generateEsImageStyleData($info, $key, $map, $multiplier);
          $image_style_plugin->generateCode($config);
        }
      }

      // @TODO Scaffold picture mapping.
      $config = $this->generateEsPictureData($info, $key, $map);
      $picture_plugin->generateCode($config);
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
  private function generateEsImageStyleData($info, $key, $data, $multiplier) {
    $machine_name = $this->generateImageStyleName($info, $key, $multiplier);
    $effect = 'image_scale';
    if (!empty($data['width']) && !empty($data['height'])) {
      $effect = 'focal_point_scale_and_crop';
    }
    $scale = trim($multiplier, 'x');
    if (!empty($data['width'])) {
      $data['width'] = round($data['width'] * $scale);
    }

    if (!empty($data['height'])) {
      $data['height'] = round($data['height'] * $scale);
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

  /**
   * Generate image style data as required by ESImageStyle Scaffolder.
   */
  private function generateEsPictureData($info) {
    $machine_name = $info['machine_name'];
    $output = [
      'machine_name' => $machine_name,
      'name' => $machine_name,
      'breakpoint_group' => $info['breakpoint_group'],
      'mapping' => [],
    ];
    foreach($info['mapping'] as $key => $value) {
      $map_key = 'breakpoints.theme.' . $info['breakpoint_group'] . '.' . $key;
      // @TODO get list of multipliers 1x, 2x, 3x, etc.
      $multipliers = $info['multipliers'];
      foreach($multipliers as $multiplier) {
        $output['mapping'][$map_key][$multiplier] = $this->generateImageStyleName($info, $key, $multiplier);
      }
    }
    return $output;
  }

  /**
   * Generate image style name from various parameters.
   */
  private function generateImageStyleName($info, $key, $multiplier) {
    $machine_name = $info['machine_name'];
    return $machine_name . '__' . $key . '__' . $multiplier;
  }
}
