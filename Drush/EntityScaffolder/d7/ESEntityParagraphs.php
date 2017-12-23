<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\d7\ESBaseInterface;
use Drush\EntityScaffolder\d7\ESEntity;
use Drush\EntityScaffolder\Logger;

class ESEntityParagraphs extends ESEntity implements ESBaseInterface {

  public function help() {
    Logger::log('[paragraphs] : Paragraphs Item', 'status');
    Logger::log('Following are the values supported in configuration', 'status');
    $headers = array('Property', 'Variable type', 'Description');
    $data = [];
    $data[] = ['name', 'string' ,'The label of the paragraph bundle, which is displayed to the editors.'];
    $data[] = ['machine_name', 'machine name (string)' , 'Internal machine name.'];
    $data[] = ['fields', 'array' ,'Array of field definitions that is attached to the entity'];
    Logger::table($headers, $data, 'status');
  }

  public function __construct(Scaffolder $scaffolder) {
    parent::__construct($scaffolder);
    $this->plugins['field_base'] = new ESFieldBase($this->scaffolder);
    $this->plugins['field_instance'] = new ESFieldInstance($this->scaffolder);
    $this->plugins['preprocess'] = new ESFieldPreprocess($this->scaffolder);
    $this->plugins['patternlab_template_manager'] = new ESPatternLabField($this->scaffolder);
    $this->setTemplateDir('/entity/paragraphs');
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

}
