<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;

class ESFieldPreprocess extends ESField {

  public function generateCode($info) {
    $module = 'es_helper';
    $filename = 'es_helper.preprocess.inc';
    // Add File header.
    $block = Scaffolder::HEADER;
    $key = 0;
    $template = '/field_preprocess/file.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add Code block.
    $block = Scaffolder::CONTENT;
    $key = $info['preprocess_hook'] . ' : ' . Scaffolder::HEADER;
    $template = '/field_preprocess/code.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $block = Scaffolder::CONTENT;
    $key = $info['preprocess_hook'] . ' : ' . Scaffolder::CONTENT . ' : ' . $info['field_name'];
    if ($info['cardinality'] == 1) {
      $template = '/field_preprocess/' . $info['type'] . '/code.content';
    }
    else {
      $template = '/field_preprocess/' . $info['type'] . '/code.contents';
    }
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $block = Scaffolder::CONTENT;
    $key = $info['preprocess_hook'] . ' : ' . Scaffolder::FOOTER;
    $template = '/field_preprocess/code.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);


    // Add file footer.
    $block = Scaffolder::FOOTER;
    $key = 0;
    $template = '/field_preprocess/file.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

  }


  /**
   * Helper function to generate machine name for fields.
   */
  public function getPreprocessHookName($config, $field_key) {
    return $config['entity_type'] . '_' . $config['bundle'];
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($config, $field_key, $field_info) {
    $info = $field_info;
    $info['_file'] = $config['_file'];
    $info['entity_type'] = $config['entity_type'];
    $info['bundle'] = $config['bundle'];
    $info['field_name'] = $this->getFieldName($config, $field_key);
    $info['preprocess_hook'] = $this->getPreprocessHookName($config, $field_key);
    $info['cardinality'] = empty($info['cardinality']) ? 1 : $info['cardinality'];
    $this->setTemplateDir('/field_preprocess/' . $info['type']);
    $local_config_file = $this->scaffolder->getTemplatedir() . $this->getTemplateDir() . '/config.yaml';
    $info['local_config'] = Utils::getConfig($local_config_file);
    return $this->processConfigData($info);
  }

}
