<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;

class ESField extends ESBase {

  /**
   * Helper functions to create FPPS.
   */
  public function scaffold($config) {
    foreach ($config['fields'] as $field_key => $field_info) {
      $info = $this->getConfig($config, $field_key, $field_info);
      $this->generateCode($info);
    }
  }

  /**
   * Helper function to generate machine name for fields.
   */
  public function getFieldName($config, $field_key) {
    $field_key = strtolower($field_key);
    return $config['field_prefix'] . '_' . $field_key;
  }

}
