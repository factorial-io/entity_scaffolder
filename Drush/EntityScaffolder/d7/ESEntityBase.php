<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\ScaffolderBase;

class ESEntityBase {

  protected $scaffolder;
  protected $plugins;

  public function __construct(Scaffolder $scaffolder) {
    $this->scaffolder = $scaffolder;
    $this->initCode();
  }

  /**
   * Helper function to initialize code.
   */
  protected function initCode() {
    $info = array();
    $module = 'fe_es';
    $filename = 'fe_es.features.inc';
    // Add File header.
    $block = Scaffolder::HEADER;
    $key = 0;
    $template = '/entity/features.inc.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add hook_ctools_plugin_api().
    $block = Scaffolder::CONTENT;
    $key = 'ctools_plugin_api : ' . Scaffolder::HEADER;
    $template = '/entity/features.inc.ctools_plugin_api';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $key = 'ctools_plugin_api : ' . Scaffolder::FOOTER;
    $code = "\n}";
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add hook_paragraphs_info().
    $block = Scaffolder::CONTENT;
    $key = 'paragraphs_info : ' . Scaffolder::HEADER;
    $template = '/entity/features.inc.paragraphs_info';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $key = 'paragraphs_info : ' . Scaffolder::FOOTER;
    $code = "\n  return \$items;\n}";
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $module = 'fe_es';
    $filename = 'fe_es.features.inc';
    // Add File header.
    $block = Scaffolder::FOOTER;
    $key = 0;
    $template = '/entity/features.inc.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

  }

  /**
   * Gets the directory from which the templates will be picked up.
   */
  public function getTemplateDir() {
    return '/entity';
  }

  /**
   * The main scaffolding action.
   */
  public function scaffold() {
    $config_files = $this->loadScaffoldSourceConfigurations();
    foreach ($config_files as $file) {
      $config = $this->getConfig($file);
      $this->generateCode($config);
      foreach ($this->plugins as $key => $plugin) {
        $plugin->scaffold($config);
      }
    }
  }

  /**
   * Loads scaffold source files.
   */
  public function loadScaffoldSourceConfigurations() {
    return NULL;
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($file) {
    $config = Utils::getConfig($file);
    $local_config_file = $this->scaffolder->getTemplatedir() . $this->getTemplatedir() . '/config.yaml';
    $config['local_config'] = Utils::getConfig($local_config_file);

    $config['entity_type'] = $config['local_config']['entity_type'];
    $config['bundle'] = $config['machine_name'];
    $config['field_prefix'] = "{$config['local_config']['short_name']}_{$config['machine_name']}";
    $config['pattern'] = [];
    $config['pattern'][] = array(
      'block' => Scaffolder::HEADER,
      'key' => 0,
      'template' => $this->getTemplatedir() . '/pattern',
    );
    $config['pattern'][] = array(
      'block' => Scaffolder::FOOTER,
      'key' => 0,
      'code' => "#}\n",
    );
    return $config;
  }

}
