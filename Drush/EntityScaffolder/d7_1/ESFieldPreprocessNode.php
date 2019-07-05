<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\Logger;

class ESFieldPreprocessNode extends ESFieldPreprocess {

  /**
   * Helper function to generate machine name for fields.
   */
  public function getFieldName($config, $field_key) {
    // When displaying just info, Field name would not be provided.
    if (drush_get_option('info')) {
      return '';
    }
    $original_field_key = $field_key;
    $field_key = strtolower($field_key);
    $field_key = str_replace(' ', '_', $field_key);
    if ($original_field_key != $field_key) {
      Logger::log('Field name "' . $original_field_key . '" is invalid, using "' . $field_key . '" instead', 'warning');
    }
    return $field_key;
  }
}
