<?php

class ESFieldBase {

  public static function getTemplateDirectory($field_name) {
    return __DIR__ . '/templates/field_base/' . $field_name;
  }

  public static function featureCodeHeader($info) {
    return drush_entity_scaffolder_render_template(__DIR__ . '/templates/field_base/feature.header.inc', $info);
  }

  public static function featureCodeFooter($info) {
    return drush_entity_scaffolder_render_template(__DIR__ . '/templates/field_base/feature.footer.inc', $info);
  }

  public static function entityDefinition($info) {
    return drush_entity_scaffolder_render_template(__DIR__ . '/templates/field_base/' . $info['type'] . '/feature.content.inc', $info);
  }


  public static function appendEntityDefinitions(&$code, $info) {
    $code['field_base'][$info['machine_name']] = self::entityDefinition($info);
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
      $info = $field_info;
      $info['field_name'] = self::getFieldName($config, $field_key);
      self::appendEntityDefinitions($code, $info);
    }
  }

  /**
   * Helper function to generate machine name for fields.
   */
  public static function getFieldName($config, $field_key) {
    return $config['entity_name'] . '_' .$config['entity_bundle'] . '_' . $field_key;
  }

}
