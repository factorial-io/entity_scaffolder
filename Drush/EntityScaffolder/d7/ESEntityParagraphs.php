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
    $template = $this->getTemplateDir() . '/features.inc.paragraphs_info';
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
   * Loads scaffold source files.
   */
  public function loadScaffoldSourceConfigurations() {
    return Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/paragraphs');
  }

  /**
   * Gets the directory from which the templates will be picked up.
   */
  public function getTemplateDir() {
    return '/entity/paragraphs';
  }

}
