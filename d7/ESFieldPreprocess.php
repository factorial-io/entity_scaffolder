<?php

class ESFieldPreprocess {

  public static function getTemplateDirectory($field_name) {
    return __DIR__ . '/templates/field_preprocess/' . $field_name;
  }

  public static function fileHeader($info) {
    return drush_entity_scaffolder_render_template(__DIR__ . '/templates/field_preprocess/file.header.inc', $info);
  }

  public static function fileFooter($info) {
    return drush_entity_scaffolder_render_template(__DIR__ . '/templates/field_preprocess/file.footer.inc', $info);
  }

  public static function functionCodeHeader($info) {
    return drush_entity_scaffolder_render_template(__DIR__ . '/templates/field_preprocess/code.header.inc', $info);
  }

  public static function functionCodeFooter($info) {
    return drush_entity_scaffolder_render_template(__DIR__ . '/templates/field_preprocess/code.footer.inc', $info);
  }

  public static function entityDefinition($info) {
    return drush_entity_scaffolder_render_template(__DIR__ . '/templates/field_preprocess/' . $info['type'] . '/code.content.inc', $info);
  }


  public static function appendEntityDefinitions(&$code, $info) {
    $code['field_preprocess'][$info['preprocess_hook']] .= self::entityDefinition($info);
  }

  public static function addCodeHeaderFooter(&$code, $info) {
    foreach ($code as $key => $value) {
      $i = array('preprocess_hook' => $key);
      $code[$key] = self::functionCodeHeader($i) . $code[$key] . self::functionCodeFooter($i);
    }
    array_unshift($code, self::fileHeader($info));
    $code[] = self::fileFooter($info);
  }

  /**
   * Helper functions to create FPPS.
   */
  public static function scaffold($config, &$code) {
    foreach ($config['fields'] as $field_key => $field_info) {
      $info = self::getConfig($config, $field_key, $field_info);
      self::appendEntityDefinitions($code, $info);
    }
  }

  /**
   * Helper function to generate machine name for fields.
   */
  public static function getFieldName($config, $field_key) {
    return $config['field_prefix'] . '_' . $field_key;
  }

  /**
   * Helper function to generate machine name for fields.
   */
  public static function getPreprocessHookName($config, $field_key) {
    return $config['entity_type'] . '_' . $config['bundle'];
  }


  /**
   * Helper function to load config and defaults.
   */
  public static function getConfig($config, $field_key, $field_info) {
    $info = $field_info;
    $info['entity_type'] = $config['entity_type'];
    $info['bundle'] = $config['bundle'];
    $info['field_name'] = self::getFieldName($config, $field_key);
    $info['preprocess_hook'] = self::getPreprocessHookName($config, $field_key);
    return $info;
  }

}
