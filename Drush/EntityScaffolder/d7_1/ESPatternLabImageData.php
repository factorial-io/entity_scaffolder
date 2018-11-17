<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Symfony\Component\Yaml\Yaml;
use Drush\EntityScaffolder\d7_1\ESBaseInterface;
use Drush\EntityScaffolder\d7_1\ESEntity;
use Drush\EntityScaffolder\d7_1\ESPicture;
use Drush\EntityScaffolder\Logger;

class ESPatternLabImageData extends ESPicture {

  const ASPECT_RATIO = 4/3;

  public function generateCode($info) {
    $scaffolder_config = $this->scaffolder->getConfig();
    $style_name = str_replace('_', '-', $info['machine_name']);
    $component_name = 'image';
    if (!empty($scaffolder_config['patternlab']['components']['image'])) {
      $component_name = $scaffolder_config['patternlab']['components']['image'];
    }
    $data = [
      'width' => $width,
      'height' => $height,
      'src' => "http://via.placeholder.com/{$width}x{$height}",
    ];
    $width = 120;
    $height = 80;
    $sources = $info['sources'];
    $comments = [];
    foreach ($sources as $breakpoint => $images) {
      $srcset = [];
      $width = NULL;
      $height = NULL;
      foreach ($images as $multiplier => $image) {
        $w = $image['width'];
        $h = $image['height'];
        if (empty($w) && empty($h)) {
          continue;
        }
        if (empty($w)) {
          $comments[] = 'Auto-calculated width for : breakpoint' . $breakpoint . ' and multiplier ' . $multiplier;
          $w = (int) ($h * ESPatternLabImageData::ASPECT_RATIO);
        }
        if (empty($h)) {
          $comments[] = 'Auto-calculated height for : breakpoint' . $breakpoint . ' and multiplier ' . $multiplier;
          $h = (int) ($w / ESPatternLabImageData::ASPECT_RATIO);
        }
        if ($multiplier == '1x') {
          $width = $w;
          $height = $h;
        }
        $srcset[$multiplier] = "http://via.placeholder.com/{$w}x{$h} $multiplier";
      }
      $srcset = implode(', ', $srcset);
      $media = $info['breakpoint_group'][$breakpoint]['media'];
      $d = [
        "width" => $width,
        "height" => $height,
        "srcset" => $srcset,
        "media" => $media,
      ];
      $data['sources'][$breakpoint] = $d;
    }

    // Update data with the last breakpoint image data (for 1x).
    $data['width'] = $width;
    $data['height'] = $height;
    $data['src'] = "http://via.placeholder.com/{$width}x{$height}";
    $param = [
      'data' => Yaml::dump($data),
      'comments' => $comments,
    ];
    $module = 'patternlab_image_data';
    $filename = $component_name . '~' . $style_name . '.yaml';
    $block = Scaffolder::CONTENT;
    $key = 'data';
    $template = '/patternlab_image_data/code.image_data.file';
    $code = $this->scaffolder->render($template, $param);

    $this->scaffolder->setCode($module, $filename, $block, $key, $code);
  }

  /**
   * Loads scaffold source files.
   */
  public function loadScaffoldSourceConfigurations() {
    return Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/picture');
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
    $image_styles = $this->getImageStylesConfig();
    $config_data = Utils::getConfig($file);
    // Expand Breakpoing Group configuration.
    $config_data['breakpoint_group'] = $this->getBreakPointConfig($config_data['breakpoint_group']);
    // Load Image styles data.
    foreach ($config_data['mapping'] as $breakpoint => $images) {
      foreach ($images as $multiplier => $image_style) {
        $config_data['sources'][$breakpoint][$multiplier] = $image_styles[$image_style];
      }
    }
    return $this->processConfigData($config_data);
  }

  /**
   * Helper function to load breakpoint groups configuration.
   */
  private function getBreakPointConfig($breakpoint_group) {
    $data = [];
    $breakpoint_group_config_files = Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/breakpoint_groups');
    foreach ($breakpoint_group_config_files as $file) {
      $breakpoint_group_config = parent::getConfig($file);
      if ($breakpoint_group_config['machine_name'] == $breakpoint_group) {
        // return $breakpoint_group_config;
        foreach ($breakpoint_group_config['breakpoints'] as $map) {
          $data[$map['machine_name']] = $map;
        }
        break;
      }
    }
    return $data;
  }

  /**
   * Helper function to load breakpoint groups configuration.
   */
  private function getImageStylesConfig() {
    static $styles = [];
    if (!empty($styles)) {
      return $styles;
    }
    $image_style_config_files = Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/image_style');
    if (!empty($image_style_config_files)) {
      foreach ($image_style_config_files as $image_style_config_file) {
        $config_data = Utils::getConfig($image_style_config_file);
        if ($config_data) {
          if (!empty($config_data['image_styles'])) {
            $prefix_machine_name = isset($config_data['prefix']['machine_name']) ? $config_data['prefix']['machine_name'] : '';
            $prefix_name = isset($config_data['prefix']['name']) ? $config_data['prefix']['name'] : '';
            $multiplier_config = isset($config_data['multiplier']) ? $config_data['multiplier'] : ['1x' => '1x'];
            $multiplier = [];
            foreach ($multiplier_config as $key => $value) {
              $multiplier[$value] = floatval(str_replace('x', '', $value));
            }

            $new_image_styles = [];
            foreach ($multiplier as $suffix => $scale) {
              foreach ($config_data['image_styles'] as $config) {
                $config['name'] = empty($config['name']) ? $config['machine_name'] : $config['name'];
                $config['name'] = $prefix_name . $config['name'];
                $config['machine_name'] = $prefix_machine_name . $config['machine_name'];
                if (count($multiplier) > 1) {
                  $config['name'] .= '@' . $suffix;
                  $config['machine_name'] .= '_' . $suffix;
                }
                $config['machine_name'] = preg_replace("/[^a-z0-9_]/", '_', $config['machine_name']);

                if ($config['effects']) {
                  foreach ($config['effects'] as $key => &$effects) {
                    $index = $key + 1;
                    $effects['index'] = $index;
                    $effects['weight'] = empty($effects['weight']) ? $index : $effects['weight'];
                    foreach (array('width', 'height') as $k) {
                      if (isset ($effects['data'][$k])) {
                        $effects['data'][$k] = round($effects['data'][$k] * $scale);
                      }
                    }
                  }
                }
                $styles[$config['machine_name']] = $this->getImageData($config);
              }
            }
          }
        }
      }
    }
    return $styles;
  }

  /**
   * Helper function to get width and height from image effects.
   */
  private function getImageData($config) {
    $data = [
      'height' => NULL,
      'width' => NULL,
    ];
    if (!empty($config['effects'])) {
      foreach ($config['effects'] as $effect) {
        switch ($effect['name']) {
          case 'image_scale':
          case 'focal_point_scale_and_crop':
            if (!empty($effect['data']['width'])) {
              $data['width'] = (int) $effect['data']['width'];
            }
            if (!empty($effect['data']['height'])) {
              $data['height'] = (int) $effect['data']['height'];
            }
            break;
        }
      }
    }
    return $data;
  }
}
