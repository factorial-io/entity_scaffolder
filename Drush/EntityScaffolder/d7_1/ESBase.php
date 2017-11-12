<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\Logger;
use Drush\EntityScaffolder\ScaffolderBase;

class ESBase {

  protected $scaffolder;
  protected $plugins;
  protected $template_dir;
  protected $parent;
  protected $info;

  public function __construct(Scaffolder $scaffolder) {
    $this->scaffolder = $scaffolder;
    $this->initCode();
  }

  /**
   * Helper function to initialize code.
   */
  public function initCode() {
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
    $code = "\n  );\n\n  return \$items;\n}";
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
   * Helper function to load config and defaults.
   */
  public function getConfig(...$params) {
    list($file) = $params;
    $config = Utils::getConfig($file);
    $config['_file'] = $file;
    $local_config_file = $this->scaffolder->getTemplatedir() . $this->getTemplatedir() . '/config.yaml';
    $config['local_config'] = Utils::getConfig($local_config_file);
    return $this->processConfigData($config);
  }

  /**
   * Process the config data before it is used.
   */
  public function processConfigData($config) {

    if (empty($config)) {
      Logger::log(dt('Configuration is empty'), 'error');
      return NULL;
    }

    // Return config loaded without validation if no variables definitions found
    // in local config files.
    if (empty($config['local_config']['variables'])) {
      return $config;
    }
    // Check if input file is valid.
    if ($this->variablesValidate($config, $config['local_config']['variables'])) {
      // Fill default values into variables.
      $config = $this->variablesFillDefaults($config, $config['local_config']['variables']);
      return $config;
    }
    else {
      return NULL;
    }
  }

  /**
   * Validate the input files.
   */
  public function variablesValidate($input, $defaults) {
    $has_all_required_variables = TRUE;
    foreach ($defaults as $key => $value) {
      if ($value['required'] == TRUE && empty($input[$key])) {
        $has_all_required_variables = FALSE;
        $vars = array(
          '%key' => $key,
          '%file' => $input['_file'],
        );
        Logger::log(dt('Required variable %key is missing in %file', $vars), 'error');
      }
    }
    return $has_all_required_variables;
  }

  /**
   * Validate the input files.
   */
  public function variablesFillDefaults($input, $defaults) {
    foreach ($defaults as $key => $value) {
      if (!isset($input[$key])) {
        $input[$key] = $value['default'];
      }
    }
    return $input;
  }

  /**
   * Gets the directory from which the templates will be picked up.
   */
  public function getTemplateDir() {
    return $this->template_dir;
  }

  /**
   * Sets the directory from which the templates will be picked up.
   */
  public function setTemplateDir($dir) {
    $this->template_dir = $dir;
  }

  /**
   * Gets the directory from which the templates will be picked up.
   */
  public function getParent() {
    return $this->parent;
  }

  /**
   * Sets the directory from which the templates will be picked up.
   */
  public function setParent($parent) {
    $this->parent = $parent;
  }

  /**
   * Gets the directory from which the templates will be picked up.
   */
  public function getInfo() {
    return $this->info;
  }

  /**
   * Sets the directory from which the templates will be picked up.
   */
  public function setInfo($info) {
    $this->info = $info;
  }

}
