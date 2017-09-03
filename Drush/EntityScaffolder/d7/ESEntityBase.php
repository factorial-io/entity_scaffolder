<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Scaffolder;
use Drush\EntityScaffolder\Utils;

class ESEntityBase {

  protected $scaffolder;

  public function __construct(Scaffolder $scaffolder) {
    $this->scaffolder = $scaffolder;
    $this->initCode();
  }

  /**
   * Helper function to initialize code.
   */
  protected function initCode() {
    $info = array();
    $module = 'fe_es';
    $filename = 'fe_es.features.inc';
    // Add File header.
    $block = Scaffolder::HEADER;
    $key = 0;
    $template = '/entity/features.inc.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add hook_ctools_plugin_api().
    $block = Scaffolder::CONTENT;
    $key = 'ctools_plugin_api : ' . Scaffolder::HEADER;
    $template = '/entity/features.inc.ctools_plugin_api';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $key = 'ctools_plugin_api : ' . Scaffolder::FOOTER;
    $code = "\n}";
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add hook_paragraphs_info().
    $block = Scaffolder::CONTENT;
    $key = 'paragraphs_info : ' . Scaffolder::HEADER;
    $template = '/entity/features.inc.paragraphs_info';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $key = 'paragraphs_info : ' . Scaffolder::FOOTER;
    $code = "\n  return \$items;\n}";
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $module = 'fe_es';
    $filename = 'fe_es.features.inc';
    // Add File header.
    $block = Scaffolder::FOOTER;
    $key = 0;
    $template = '/entity/features.inc.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);


  }

  /**
   * The main scaffolding action.
   */
  public function scaffold() {
    return ;
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($file) {
    return Utils::getConfig($file);
  }

}
