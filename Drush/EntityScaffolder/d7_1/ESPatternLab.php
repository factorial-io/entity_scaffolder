<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;

class ESPatternLab extends ESField {

  public function generateCode($config) {
    if ($config) {
      $comments = $this->getComments($config);
      foreach ($comments as $pattern) {
        $module = 'templates';
        $filename = $this->getDrupalTemplateName($config);
        if (empty($filename)) {
          continue;
        }
        $block = $pattern['block'];
        $key = $pattern['key'];

        $code = NULL;
        if (isset($pattern['code'])) {
          $code = $pattern['code'];
        }
        elseif(isset($pattern['template'])) {
          $template = $pattern['template'];
          $code = $this->scaffolder->render($template, $config);
        }
        if ($code) {
          $this->scaffolder->setCode($module, $filename, $block, $key, $code);
        }
      }
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
    switch ($config['entity_type']) {
      case 'fieldable_panels_pane':
        $filename = 'fpp/fieldable-panels-pane--' . str_replace('_', '-', $config['bundle']) . '.tpl.twig';
        break;

      case 'paragraphs_item':
        $filename = 'paragraphs-item/paragraphs-item--' . str_replace('_', '-', $config['bundle']) . '.tpl.twig';
        break;

      case 'node':
        // Don't Generate template for nodes.
        $filename = FALSE;
        break;

    }
    return $filename;
  }

}
