<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;

class ESPatternLabEntity extends ESPatternLab {

    /**
   * Helper function to make comments for current instance.
   */
  public function getComments($config) {
    $comments = [];
    $comments[] = array(
      'block' => Scaffolder::HEADER,
      'key' => 0,
      'template' => '/entity/' . $config['type'] . '/pattern',
    );
    $comments[] = array(
      'block' => Scaffolder::FOOTER,
      'key' => 0,
      'code' => "#}\n",
    );
    return $comments;
  }


  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($config, $field_key, $field_info) {
    $config['type'] = $config['local_config']['type'];
    return $config;
  }

}
