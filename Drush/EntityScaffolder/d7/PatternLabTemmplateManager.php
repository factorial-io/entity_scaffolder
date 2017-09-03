<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;

class PatternLabTemmplateManager {

  protected $template_dir;

  public function __construct(Scaffolder $scaffolder) {
    $config = $scaffolder->getConfig();
    $this->template_dir = isset($config['directories']['templates']) ? $config['directories']['templates'] : NULL;
  }

  /**
   * Create template file.
   */
  public function scaffold($config) {
    echo "\n $this->template_dir";
  }

}
