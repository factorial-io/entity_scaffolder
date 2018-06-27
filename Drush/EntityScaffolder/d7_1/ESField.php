<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\Logger;

class ESField extends ESBase {

  /**
   * Helper functions to create FPPS.
   */
  public function scaffold($config) {
    if (!isset($config['fields'])) {
      // @todo insert a meaningful log message here.
      return;
    }
    foreach ($config['fields'] as $field_key => $field_info) {
      $info = $this->getConfig($config, $field_key, $field_info);
      $this->generateCode($info);
    }
  }

  /**
   * Helper function to generate machine name for fields.
   */
  public function getFieldName($config, $field_key) {
    $original_field_key = $field_key;
    $field_key = strtolower($field_key);
    $field_key = str_replace(' ', '_', $field_key);
    if ($original_field_key != $field_key) {
      Logger::log('Field name "' . $original_field_key . '" is invalid, using "' . $field_key . '" instead', 'warning');
    }
    return $config['field_prefix'] . '_' . $field_key;
  }

  /**
   * Get list of supported field types.
   */
  protected function findFieldTypes() {
    $fields = [];
    $config_dirs = $this->scaffolder->getExtendedTemplateDirs();
    foreach ($config_dirs as $dir) {
      $field_dir = $dir . '/field/';
      $list = Utils::getFolders($field_dir);
      foreach ($list as $folder_name) {
        $fields[$folder_name] = $folder_name;
      }
    }
    return $fields;
  }

}
