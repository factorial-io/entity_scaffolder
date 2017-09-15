<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\ScaffolderInterface;
use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\ScaffolderBase;
use Drush\EntityScaffolder\Logger;

class Scaffolder extends ScaffolderBase {

  protected $plugins;

  public function help($type, $name) {
    switch ($type) {
      case 'entity':
        if ($name && !empty($this->plugins[$name])) {
          if (method_exists($this->plugins[$name], 'help')) {
            $this->plugins[$name]->help();
          }
        }
        else {
          Logger::log('Please provide one of the supported entities to display more details.', 'warning');
          $headers = array('Key', 'Example usage', 'Description');
          $data = [];
          $data[] = ['fpp', 'drush esb entity fpp' ,'Fieldable Panels Pane'];
          $data[] = ['paragraphs', 'drush esb entity paragraphs' ,'Paragraphs Item'];
          Logger::table($headers, $data, 'status');
        }
        break;

      case 'field':
        $plugins = [];
        $plugins['field_base'] = new ESFieldBase($this);
        $plugins['field_instance'] = new ESFieldInstance($this);
        $plugins['preprocess'] = new ESFieldPreprocess($this);
        $plugins['patternlab_template_manager'] = new ESPatternLabField($this);
        foreach ($plugins as $plugin) {
          if (method_exists($plugin, 'help')) {
            $plugin->help($name);
          }
        }
        break;

      default:
        Logger::log('Please provide more options to show details.', 'warning');
        $headers = array('Key', 'Example usage', 'Description');
        $data = [];
        $data[] = ['entity', 'drush esb entity fpp' ,'Shows more details about Fieldable Panels Pane'];
        $data[] = ['field', 'drush esb field text' ,'Shows more detail about text field'];
        Logger::table($headers, $data, 'status');
        break;
    }
  }

  public function __construct() {
    parent::__construct();
    $this->setTemplateDir(__DIR__ . '/templates');
    $this->plugins['fpp'] = new ESEntityFPP($this);
    $this->plugins['paragraphs'] = new ESEntityParagraphs($this);
  }

  /**
   * Start scaffolding.
   */
  public function scaffold() {
    if (empty($this->getEntityTypes())) {
      Logger::log(dt('Entity Scaffolder didn\'t find any definitions'), 'error');
      return;
    }
    Logger::log(dt('Found some entity definitions.'), 'status');
    foreach ($this->getEntityTypes() as $entity_type) {
      if (isset($this->plugins[$entity_type])) {
        $this->plugins[$entity_type]->scaffold();
      }
    }
  }

  /**
   * Prepare files
   */
  public function processFiles() {
    $code = $this->getCode();
    // Populate header section of info file.
    if(!empty($code['fe_es'])) {
      $code = file_get_contents($this->getTemplateDir() . '/feature/fe_es/fe_es.info');
      $this->setCode('fe_es', 'fe_es.info', Self::HEADER, 0, $code);
      $code = "\nproject path = sites/all/modules/features\n";
      $this->setCode('fe_es', 'fe_es.info', Self::CONTENT, $code, $code);
    }
    return $this->flattenFiles();
  }

  /**
   * Prepare files
   */
  public function flattenFiles() {
    $code = $this->getCode();
    $files = array();
    foreach ($code as $module_name => $module_data) {
      foreach ($module_data as $filename => $file_data) {
        $file_path = $this->getDirectory($module_name) . "/{$filename}";
        $blocks = array();
        ksort($file_data);
        $code = '';
        foreach($file_data as $code_blocks) {
          if(is_array($code_blocks)) {
            ksort($code_blocks);
            $code_blocks = implode('', $code_blocks);
          }
          $code .= $code_blocks;
        }
        $files[$file_path] = $code;
      }
    }
    return $files;
  }

  /**
   * Helper function to retrieve module path.
   */
  public function getDirectory($module_name) {
    return isset($this->getConfig()['directories'][$module_name]) ? $this->getConfig()['directories'][$module_name] : NULL;
  }

  /**
   * Helper function to retrieve file write scheme based on path.
   */
  public function getWriteScheme($path) {
    // Twig template files.
    if (Utils::endsWith($path, '.twig')) {
      return Utils::TWIG_COMMENT;
    }
    return Utils::FILE_EXISTS_OVERWRITE;
  }

  /**
   * write code to file system.
   */
  public function exportCode() {
    $files = $this->processFiles($this->code);

    $debug = !!drush_get_option('debug');

    // Prepare module directories.
    if (!$debug) {
      Utils::copyFolderContents($this->getTemplateDir() . '/feature/fe_es', $this->getDirectory('fe_es'));
      Utils::copyFolderContents($this->getTemplateDir() . '/preprocess/es_helper', $this->getDirectory('es_helper'));

      // Copy fe_es_filters, only once.
      if (!file_exists($this->getDirectory('fe_es_filters') . '/fe_es_filters.info')) {
        Utils::copyFolderContents($this->getTemplateDir() . '/feature/fe_es_filters', $this->getDirectory('fe_es_filters'));
      }
    }
    // Write dynamic code to files.
    foreach ($files as $filepath => $file_contents) {
      if ($debug) {
        Utils::debug($file_contents, $filepath);
      }
      Utils::write($filepath, $file_contents, $this->getWriteScheme($filepath));
    }
  }
}
