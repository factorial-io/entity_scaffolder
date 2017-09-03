<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;

class ESEntityParagraphs extends ESEntityBase {

  public function __construct(Scaffolder $scaffolder) {
    parent::__construct($scaffolder);
    $this->plugins['field_base'] = new ESFieldBase($this->scaffolder);
    $this->plugins['field_instance'] = new ESFieldInstance($this->scaffolder);
    $this->plugins['preprocess'] = new ESFieldPreprocess($this->scaffolder);
    $this->plugins['patternlab_template_manager'] = new PatternLabTemmplateManager($this->scaffolder);
  }

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
      foreach ($this->plugins as $key => $plugin) {
        $plugin->scaffold($config);
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
    $config['pattern'] = [];
    $config['pattern'][] = array(
      'block' => Scaffolder::HEADER,
      'key' => 0,
      'template' => '/entity/paragraphs/pattern',
    );
    $config['pattern'][] = array(
      'block' => Scaffolder::FOOTER,
      'key' => 0,
      'code' => "#}\n",
    );
    return $config;
  }

}
