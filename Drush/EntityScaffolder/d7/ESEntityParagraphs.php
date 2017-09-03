<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\d7\Scaffolder;
use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\d7\ESEntityBase;

class ESEntityParagraphs extends ESEntityBase {

  public function generateCode($info) {
    $module = 'fe_es';
    $filename = 'fe_es.features.inc';
    // Add File header.
    $block = Scaffolder::CONTENT;
    $key = 'paragraphs_info : ' . Scaffolder::CONTENT . ' : ' . $info['machine_name'];
    $template = '/entity/paragraphs/features.inc.paragraphs_info';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);


    // Add entry to info file.
    $code = "\nfeatures[paragraphs][] = {$info['machine_name']}";
    $module = 'fe_es';
    $filename = 'fe_es.info';
    $block = Scaffolder::CONTENT;
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[features_api][] = api:2";
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    if (!empty($info['local_config']['dependencies'])) {
      foreach ($info['local_config']['dependencies'] as $dependency) {
        $code = "\ndependencies[] = {$dependency}";
        $this->scaffolder->setCode($module, $filename, $block, $code, $code);
      }
    }
  }

  /**
   * Helper functions to create FPPS.
   */
  public function scaffold() {
    $config_files = Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/paragraphs');
    foreach ($config_files as $file) {
      $config = $this->getConfig($file);
      $this->generateCode($config);
      if ($config['fields']) {
        $base = new ESFieldBase($this->scaffolder);
        $base->scaffold($config);
        $instance = new ESFieldInstance($this->scaffolder);
        $instance->scaffold($config);
        $preprocess = new ESFieldPreprocess($this->scaffolder);
        $preprocess->scaffold($config);
      }
    }
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($file) {
    $config = parent::getConfig($file);
    $config['entity_type'] = 'paragraphs_item';
    $config['bundle'] = $config['machine_name'];
    $config['field_prefix'] = 'pgf_' . $config['machine_name'];
    $local_config_file = $this->scaffolder->getTemplatedir() . '/entity/paragraphs/config.yaml';
    $config['local_config'] = Utils::getConfig($local_config_file);
    return $config;
  }

}
