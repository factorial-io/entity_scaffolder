<?php

class ESEntityFPP {

  public static function featureCodeHeader($info) {
    return "<?php
/**
 * @file
 * fe_es.fieldable_panels_pane_type.inc
 */

/**
 * Implements hook_default_fieldable_panels_pane_type().
 */
function fe_es_default_fieldable_panels_pane_type() {
  \$export = array();\n";
  }

  public static function featureCodeFooter($info) {
    return "  return \$export;
}
";
  }

  public static function entityDefinition($info) {
    return "  \$fieldable_panels_pane_type = new stdClass();
  \$fieldable_panels_pane_type->disabled = FALSE;
  \$fieldable_panels_pane_type->api_version = 1;
  \$fieldable_panels_pane_type->name = '{$info['machine_name']}';
  \$fieldable_panels_pane_type->title = '{$info['name']}';
  \$fieldable_panels_pane_type->description = '';
  \$export['{$info['machine_name']}'] = \$fieldable_panels_pane_type;\n";
  }

  public static function appendEntityDefinitions(&$code, $info) {
    $code['fpp'][$info['machine_name']] = self::entityDefinition($info);
    $code['fe_es.info'][] = "features[fieldable_panels_pane_type][] = {$info['machine_name']}";

  }

  public static function addFeatureHeaderFooter(&$code, $info) {
    array_unshift($code, self::featureCodeHeader($info));
    $code[] = self::featureCodeFooter($info);
  }

  /**
   * Helper functions to create FPPS.
   */
  function scaffold($config_dir, &$code) {
    $config_files = drush_entity_scaffolder_get_config_files($config_dir . '/fpp');
    foreach ($config_files as $file) {
      $config = Spyc::YAMLLoad($file);
      self::appendEntityDefinitions($code, $config);
    }
  }

}
