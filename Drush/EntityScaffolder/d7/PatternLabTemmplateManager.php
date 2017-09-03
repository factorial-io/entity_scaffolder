<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;

class PatternLabTemmplateManager {

  protected $template_dir;
  protected $scaffolder;

  public function __construct(Scaffolder $scaffolder) {
    $this->scaffolder = $scaffolder;
    $this->template_dir = $this->scaffolder->getDirectory('templates');
  }

  /**
   * Create template file.
   */
  public function scaffold($config) {
    $module = 'templates';
    $filename = $this->getDrupalTemplateName($config);
    $block = Scaffolder::HEADER;
    $key = 0;
    $code = "[PLACEHOLDER FOR $filename]";
    if ($filename) {
      $this->scaffolder->setCode($module, $filename, $block, $key, $code);
    }
  }

  /**
   * Helper function to create template name for given config.
   */
  public function getDrupalTemplateName($config) {
    $sub_folder = $config['entity_type'];
    if ($sub_folder == 'paragraphs_item') {
      $sub_folder = 'paragraphs';
    }
    $filename = NULL;
    echo var_dump($config);
    switch ($config['entity_type']) {
      case 'fieldable_panels_pane':
        $filename = 'fpp/fieldable-panels-pane--' . str_replace('_', '-', $config['bundle']) . '.tpl.twig';
        break;

      case 'paragraphs_item':
        $filename = 'paragraphs-item/paragraphs-item--' . str_replace('_', '-', $config['bundle']) . '.tpl.twig';
        break;

    }
    return $filename;
  }
}
