<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;

class ESPatternLabField extends ESPatternLab {

  /**
   * Helper function to make comments for current instance.
   */
  public function getComments($config) {
    $comments = [];
    $comments[] = array(
      'block' => Scaffolder::CONTENT,
      'key' => $config['field_name'],
      'template' => '/field_preprocess/' . $config['type'] . '/pattern',
    );
    return $comments;
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($config, $field_key, $field_info) {
    $info = $field_info;
    $info['entity_type'] = $config['entity_type'];
    $info['bundle'] = $config['bundle'];
    $info['field_name'] = $this->getFieldName($config, $field_key);
    $info['cardinality'] = empty($info['cardinality']) ? 1 : $info['cardinality'];
    return $info;
  }

}
