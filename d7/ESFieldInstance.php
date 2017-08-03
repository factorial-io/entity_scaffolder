<?php

class ESFieldInstance {

  public static function getTemplateDirectory($field_name) {
    return __DIR__ . '/templates/field_instance/' . $field_name;
  }

  public static function featureCodeHeader($info) {
    return drush_entity_scaffolder_render_template('/field_instance/feature.header.inc', $info);
  }

  public static function featureCodeFooter($info) {
    return drush_entity_scaffolder_render_template('/field_instance/feature.footer.inc', $info);
  }

  public static function entityDefinition($info) {
    return drush_entity_scaffolder_render_template('/field_instance/' . $info['type'] . '/feature.content.inc', $info);
  }


  public static function appendEntityDefinitions(&$code, $info) {
    $code['field_instance'][$info['field_name']] = self::entityDefinition($info);
  }

  public static function addFeatureHeaderFooter(&$code, $info) {
    array_unshift($code, self::featureCodeHeader($info));
    $code[] = self::featureCodeFooter($info);
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
   * Helper function to load config and defaults.
   */
  public static function getConfig($config, $field_key, $field_info) {
    $info = $field_info;
    $info['entity_type'] = $config['entity_type'];
    $info['bundle'] = $config['bundle'];
    $info['field_name'] = self::getFieldName($config, $field_key);
    return $info;
  }

}
