<?php

class ESEntityBase {

  protected $scaffolder;

  public function __construct(ScaffolderBase $scaffolder) {
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
    $block = ScaffolderBase::HEADER;
    $key = 0;
    $template = '/entity/features.inc.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $block = ScaffolderBase::CONTENT;
    $key = 'ctools_plugin_api : ' . ScaffolderBase::HEADER;
    $template = '/entity/features.inc.ctools_plugin_api';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $key = 'ctools_plugin_api : ' . ScaffolderBase::FOOTER;
    $code = "\n}";
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $module = 'fe_es';
    $filename = 'fe_es.features.inc';
    // Add File header.
    $block = ScaffolderBase::FOOTER;
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
