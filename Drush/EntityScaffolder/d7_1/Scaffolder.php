<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\ScaffolderInterface;
use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\ScaffolderBase;
use Drush\EntityScaffolder\Logger;

class Scaffolder extends ScaffolderBase {
  // @see http://php.net/version_compare.
  const VERSION = '7.2.20';

  const DEFAULT_TEMPLATE_DIR = __DIR__ . '/templates';
  const TEMPLATE_NAMESPACE = 'd7_1';

  protected $plugins;

  public function help($type, $name) {
    // Register more plugins for help.
    $this->plugins['field'] = new ESFieldBase($this);

    if (empty($type)) {
      // @todo get types.
      $options = [
        'field' => dt('Field'),
        'image_style' => dt('Image Style'),
        'picture' => dt('Picture'),
        'fpp' => dt('Fieldable Panels Pane'),
        'breakpoint_groups' => dt('Breakpoint Groups'),
      ];
      $type = drush_choice($options);
      if ($type && !empty($this->plugins[$type])) {
        if (method_exists($this->plugins[$type], 'help')) {
          $this->plugins[$type]->help($name);
        }
      }
    }
    return;
  }

  public function __construct() {
    parent::__construct(self::TEMPLATE_NAMESPACE);
    if (empty($this->getTemplateDir())) {
      $this->setTemplateDir(self::DEFAULT_TEMPLATE_DIR, $skipNamespaceAddition = TRUE);
      $this->setExtendedTemplateDirs(0, self::DEFAULT_TEMPLATE_DIR, $skipNamespaceAddition = TRUE);
    }
    $this->plugins['image_style'] = new ESImageStyle($this);
    $this->plugins['breakpoint_groups'] = new ESBreakPointGroup($this);
    $this->plugins['pl_breakpoint_groups'] = new ESPatternLabBreakpointGroupData($this);
    $this->plugins['picture'] = new ESPicture($this);
    $this->plugins['picture_image_data'] = new ESPatternLabImageData($this);
    $this->plugins['fpp'] = new ESEntityFPP($this);
    $this->plugins['node'] = new ESEntityNode($this);
    $this->plugins['paragraphs'] = new ESEntityParagraphs($this);
    $this->plugins['list_predefined_options'] = new ESListPredefinedOptions($this);
    $this->plugins['config'] = new ESConfig($this);
    $this->plugins['responsive_images'] = new ESResponsiveImages($this);
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
        if ($entity_type == 'picture') {
          $this->plugins['picture_image_data']->scaffold();
        }
        if ($entity_type == 'breakpoint_groups') {
          $this->plugins['pl_breakpoint_groups']->scaffold();
        }
      }
    }
    $this->plugins['list_predefined_options']->scaffold();
    $this->plugins['config']->scaffold();
  }

  /**
   * Prepare files.
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
        $file_path = NULL;
        $module_dir = $this->getDirectory($module_name);
        $file_path = "$module_dir/{$filename}";
        if (empty($module_dir)) {
          Logger::log(dt('Missing directory configuration for module : @module', array('@module' => $module_name)), 'warning');
          continue;
        }
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
    return !empty($this->getConfig()['directories'][$module_name]) ? $this->getConfig()['directories'][$module_name] : NULL;
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
