<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;

class ESFieldBase extends ESField {

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
    return $info;
  }

}
