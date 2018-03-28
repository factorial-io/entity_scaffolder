<?php

namespace Drush\EntityScaffolder;

use Drush\EntityScaffolder\ScaffolderInterface;
use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\ScaffolderBase;
use Drush\EntityScaffolder\Logger;

class BoilerPlate {

  protected $templateDir;

  public function __construct() {
    $this->setTemplateDir(__DIR__ . '/boilerplate');
  }

  /**
   * Generate Boilerplate Code.
   */
  public function generate($command) {
    $config = $this->loadConfig();
    $args = array();
    $this->loadInteractiveVariables($config, $args);

    if (isset($config['commands'][$command])) {
      foreach ($config['commands'][$command] as $type) {
        $this->generateType($type, $args);
      }
    }
    else {
      $this->generateType($command, $args);
    }
  }

  /**
   * Start scaffolding.
   */
  public function generateType($type, $args) {
    // Load Config File.
    $config = $this->loadConfigForType($type);
    Logger::log(dt('Generate - @name', array('@name' => $config['name'])), 'status');
    $this->loadInteractiveVariables($config, $args);
    Logger::log(dt('Copying files started'), 'status');
    $out = [];
    $dir_path = $this->getTemplateDir() . '/' . $type . '/templates';
    $dir = new \RecursiveDirectoryIterator($dir_path);
    $iterator = new \RecursiveIteratorIterator($dir);
    foreach ($iterator as $file) {
      if ($this->skipFileProcessing($file)) {
        continue;
      }
      $destination = $this->getDestination($file, $dir_path);
      $file_content = '';
      if ($file->getExtension() == "twig") {
        $file_content = Utils::render($file->getPath(), $file->getBasename(), $args);
      }
      else {
        if ($file->getSize()) {
          $file_content = $file->openFile()->fread($file->getSize());
        }
      }
      if (isset($config['files'][$destination])) {
        $destination = Utils::renderInline($config['files'][$destination], $args);
      }
      $destination = $args['directory'] . $destination;
      Utils::write($destination, $file_content);
    }
    Logger::log(dt('Copying files finished'), 'status');
  }

  /**
   * Helper fuction to load interactive variables.
   */
  protected function loadInteractiveVariables($config, &$args) {
    if ($config['interactive']) {
      foreach ($config['variables'] as $key => $value) {

        if ($value['hidden']) {
          $args[$key] = $value['default'];
        }
        else {
          $args[$key] = drush_prompt($value['label'], $value['default'], $value['required']);
        }

        if (isset($value['pattern'])) {
          $args[$key] = Utils::renderInline($value['pattern'], $args);
        }
      }
    }
  }

  /**
   * Helper function to decide if current file object needs to be processed.
   */
  protected function skipFileProcessing($file) {
    $flag = FALSE;
    if ($file->getType() != 'file') {
      $flag = TRUE;
    }
    switch ($file->getFilename()) {
      case '.DS_Store':
        $flag = TRUE;
    }
    return $flag;
  }

  /**
   * Helper function to retrieve mapped destination.
   */
  protected function getDestination($file, $template_dir_path) {
    $file_path = $file->getPath();

    if ($file->getExtension() == "skip") {
      $file_name = basename($file->getFilename(), '.skip');
    }
    else {
      $file_name = basename($file->getFilename(), '.twig');
    }

    return str_replace($template_dir_path, '', $file_path) . '/' . $file_name;
  }

  /**
   * Load configuration data.
   */
  protected function loadConfig() {
    $config = Utils::getConfig($this->getTemplateDir() . '/config.yaml');
    return $config;
  }

  /**
   * Load configuration data.
   */
  protected function loadConfigForType($type) {
    $dir = $this->getTemplateDir() . '/' . $type;
    $config = Utils::getConfig($dir . '/config.yaml');
    $defaults = array(
      'version' => 'd7_1',
    );
    $config = array_replace_recursive($defaults, $config);
    return $config;
  }

  /**
   * Sets the directory from which the templates has to be picked up.
   */
  public function setTemplateDir($dir) {
    $this->templateDir = $dir;
  }

  /**
   * Gets the directory from which the templates will be picked up.
   */
  public function getTemplateDir() {
    return $this->templateDir;
  }

}
