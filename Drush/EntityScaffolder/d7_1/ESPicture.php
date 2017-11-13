<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\d7_1\ESBaseInterface;
use Drush\EntityScaffolder\d7_1\ESEntity;
use Drush\EntityScaffolder\Logger;

class ESPicture extends ESBase implements ESBaseInterface {

  public function help() {
    Logger::log('[picture] : Picture', 'status');
    Logger::log('Following are the values supported in configuration', 'status');
    $headers = array('Property', 'Variable type', 'Description');
    $data = [];
    $data[] = ['name', 'string' ,'The label of the breakpoint group, which is displayed to the admins.'];
    $data[] = ['machine_name', 'machine name (string)' , 'Internal machine name.'];
    $data[] = ['mapping', 'array' ,'Array of mapping definitions that is attached to the Picture'];
    Logger::table($headers, $data, 'status');
  }

  public function __construct(Scaffolder $scaffolder) {
    parent::__construct($scaffolder);
    $this->setTemplateDir('/picture');
  }

  public function generateCode($info) {
    $code = "\ndependencies[] = picture";
    $module = 'fe_es';
    $filename = 'fe_es.info';
    $block = Scaffolder::CONTENT;
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[features_api][] = api:2";
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[picture_mapping][] = " . $info['machine_name'];
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);

    // Add hook_ctools_plugin_api().
    $module = 'fe_es';
    $filename = 'fe_es.features.inc';
    $block = Scaffolder::CONTENT;
    $key = 'ctools_plugin_api : ' . Scaffolder::CONTENT . ' : picture';
    $template = '/picture/features.inc.ctools_plugin_api';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);


    $module = 'fe_es';
    $filename = 'fe_es.default_picture_mapping.inc';
    // Add File header.
    $block = Scaffolder::HEADER;
    $key = 0;
    $template = '/picture/feature.picture.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add Code block.
    $block = Scaffolder::CONTENT;
    $key = $info['machine_name'];
    $template = '/picture/feature.picture.content';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add file footer.
    $block = Scaffolder::FOOTER;
    $key = 0;
    $template = '/picture/feature.picture.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);
  }

  /**
   * Loads scaffold source files.
   */
  public function loadScaffoldSourceConfigurations() {
    return Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/picture');
  }

  /**
   * The main scaffolding action.
   */
  public function scaffold() {
    $config_files = $this->loadScaffoldSourceConfigurations();
    if ($config_files) {
      foreach ($config_files as $file) {
        $config = $this->getConfig($file);
        $this->generateCode($config);
      }
    }
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig(...$params) {
    list($file) = $params;
    $config_data = parent::getConfig($file);
    if ($config_data) {
      $mapping = [];
      foreach ($config_data['mapping'] as $key => $m) {
        $mapping['breakpoints.theme.' . $config_data['breakpoint_group'] . '.' . $key] = $m;
      }
      $config_data['mapping'] = $mapping;
    }
    return $this->processConfigData($config_data);
  }
}
