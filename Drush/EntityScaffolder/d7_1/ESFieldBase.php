<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\Logger;

class ESFieldBase extends ESField {

  public function help($name) {
    $config = $this->getConfig([], [], ['type' => $name]);
    Logger::log('[field_base] : ' . $name, 'status');
    Logger::log('Following are the values supported in configuration');
    $headers = array('', 'Property', 'Variable type', 'Description');
    $data = [];
    $data[] = ['*', 'name', 'string', "The label of the fpp, which is displayed to the editors."];
    $data[] = ['*', 'map', 'string', "The variable to which the values in this field will be mapped to\nPatternlab twig templates."];
    $data[] = ['*', 'type', 'string', "The type of this field."];
    $data[] = ['*', 'machine_name', 'machine name (string)', "String used to construct machine name of the field.\nThis will be prefixed with appropriate string under naming convention."];
    if ($config['local_config']['variables']) {
      foreach ($config['local_config']['variables'] as $key => $value) {
        $required = $value['required'] ? "*" : '';
        $data[] = [$required, $key, $value['type'], $value['description']];
      }
    }
    Logger::table($headers, $data, 'status');
    Logger::log('NOTE: A asterisk "*" in first column means the field is required.');
  }

  public function generateCode($info) {
    $module = 'fe_es';
    $filename = 'fe_es.features.field_base.inc';
    // Add File header.
    $block = Scaffolder::HEADER;
    $key = 0;
    $template = '/field_base/feature.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add Code block.
    $block = Scaffolder::CONTENT;
    $key = $info['field_name'];
    $template = '/field_base/' . $info['type'] . '/feature.content';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add file footer.
    $block = Scaffolder::FOOTER;
    $key = 0;
    $template = '/field_base/feature.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add entry to info file.
    $code = "\nfeatures[field_base][] = {$info['field_name']}";
    $module = 'fe_es';
    $filename = 'fe_es.info';
    $block = Scaffolder::CONTENT;
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    if (!empty($info['local_config']['dependencies'])) {
      foreach ($info['local_config']['dependencies'] as $dependency) {
        $code = "\ndependencies[] = {$dependency}";
        $this->scaffolder->setCode($module, $filename, $block, $code, $code);
      }
    }
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($config, $field_key, $field_info) {
    $info = $field_info;
    $info['field_name'] = $this->getFieldName($config, $field_key);
    $info['cardinality'] = empty($info['cardinality']) ? 1 : $info['cardinality'];
    $this->setTemplateDir('/field_base/' . $info['type']);
    $local_config_file = $this->scaffolder->getTemplatedir() . $this->getTemplateDir() . '/config.yaml';
    $info['local_config'] = Utils::getConfig($local_config_file);
    return $this->processConfigData($info);
  }

}
