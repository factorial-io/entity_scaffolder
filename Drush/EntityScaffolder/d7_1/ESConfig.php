<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\d7_1\ESBaseInterface;
use Drush\EntityScaffolder\d7_1\ESEntity;
use Drush\EntityScaffolder\Logger;

class ESConfig extends ESBase implements ESBaseInterface {

  public function help() {
    Logger::log('[config.yaml] : Config', 'status');
    Logger::log('Following are the values supported in configuration', 'status');
    $headers = array('Property', 'Variable type', 'Description');
    $data = [];
    $data[] = ['project_short_code', 'string' ,'Project short code used to generate various machine names.'];
    $data[] = ['directories', 'array' , 'List of various directories.'];
    Logger::table($headers, $data, 'status');
  }

  public function __construct(Scaffolder $scaffolder) {
    parent::__construct($scaffolder);
    $this->setTemplateDir('/config');
  }

  public function generateCode($info) {
    $module = 'es_helper';
    $filename = 'es_helper.patternlab.inc';
    // Add File header.
    $block = Scaffolder::HEADER;
    $key = 0;
    $template = '/config/es_helper.patternlab.inc';
    $code = $this->scaffolder->render($template, $info);

    $this->scaffolder->setCode($module, $filename, $block, $key, $code);
  }


  /**
   * Loads scaffold source files.
   */
  public function loadScaffoldSourceConfigurations() {
    return $this->scaffolder->getConfigDir() . '/config.yaml';
  }

  /**
   * The main scaffolding action.
   */
  public function scaffold() {
    $file = $this->loadScaffoldSourceConfigurations();
    $config = $this->getConfig($file);
    $this->generateCode($config);
  }

  /**
   * Process the config data before it is used.
   */
  public function processConfigData($config) {
    $config = parent::processConfigData($config);
    $config['patternlab_path'] = '/source';
    if (!empty($config['directories']['patternlab_source_dir'])) {
      $config['patternlab_path'] = $config['directories']['patternlab_source_dir'];
    }
    return $config;
  }
}
