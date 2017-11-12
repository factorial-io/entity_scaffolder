<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\d7_1\ESBaseInterface;
use Drush\EntityScaffolder\d7_1\ESEntity;
use Drush\EntityScaffolder\Logger;

class ESBreakPointGroup extends ESBase implements ESBaseInterface {

  public function help() {
    Logger::log('[breakpoint group] : Breakpoint Group', 'status');
    Logger::log('Following are the values supported in configuration', 'status');
    $headers = array('Property', 'Variable type', 'Description');
    $data = [];
    $data[] = ['name', 'string' ,'The label of the breakpoint group, which is displayed to the admins.'];
    $data[] = ['machine_name', 'machine name (string)' , 'Internal machine name.'];
    $data[] = ['breakpoints', 'array' ,'Array of beakpoint definitions that is attached to the Breakpoint Group'];
    Logger::table($headers, $data, 'status');
  }

  public function __construct(Scaffolder $scaffolder) {
    parent::__construct($scaffolder);
    $this->setTemplateDir('/breakpoint_groups');
  }

  public function generateGroupCode($info) {
    $code = "\ndependencies[] = breakpoints";
    $module = 'fe_es';
    $filename = 'fe_es.info';
    $block = Scaffolder::CONTENT;
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[features_api][] = api:2";
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[breakpoint_group][] = " . $info['machine_name'];
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[ctools][] = breakpoints:default_breakpoint_group:1";
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[ctools][] = breakpoints:default_breakpoints:1";
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);

    // Add hook_ctools_plugin_api().
    $module = 'fe_es';
    $filename = 'fe_es.features.inc';
    $block = Scaffolder::CONTENT;
    $key = 'ctools_plugin_api : ' . Scaffolder::CONTENT . ' : breakpoint_group';
    $template = '/breakpoint_groups/features.inc.ctools_plugin_api';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);


    $module = 'fe_es';
    $filename = 'fe_es.default_breakpoint_group.inc';
    // Add File header.
    $block = Scaffolder::HEADER;
    $key = 0;
    $template = '/breakpoint_groups/feature.breakpoint_groups.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add Code block.
    $block = Scaffolder::CONTENT;
    $key = $info['machine_name'];
    $template = '/breakpoint_groups/feature.breakpoint_groups.content';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add file footer.
    $block = Scaffolder::FOOTER;
    $key = 0;
    $template = '/breakpoint_groups/feature.breakpoint_groups.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);
  }

  public function generateCode($info) {
    $module = 'fe_es';
    $filename = 'fe_es.info';
    $block = Scaffolder::CONTENT;
    $code = "\nfeatures[breakpoints][] = " . $info['id'];
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);

    $module = 'fe_es';
    $filename = 'fe_es.default_breakpoints.inc';
    // Add File header.
    $block = Scaffolder::HEADER;
    $key = 0;
    $template = '/breakpoint_groups/feature.breakpoints.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add Code block.
    $block = Scaffolder::CONTENT;
    $key = $info['machine_name'];
    $template = '/breakpoint_groups/feature.breakpoints.content';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add file footer.
    $block = Scaffolder::FOOTER;
    $key = 0;
    $template = '/breakpoint_groups/feature.breakpoints.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);
  }

  /**
   * Loads scaffold source files.
   */
  public function loadScaffoldSourceConfigurations() {
    return Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/breakpoint_groups');
  }

  /**
   * The main scaffolding action.
   */
  public function scaffold() {
    $config_files = $this->loadScaffoldSourceConfigurations();
    if ($config_files) {
      foreach ($config_files as $file) {
        $config = $this->getConfig($file);
        if (!empty($config['breakpoints'])) {
          $this->generateGroupCode($config);
          foreach ($config['breakpoints'] as $breakpoint_config) {
            $this->generateCode($breakpoint_config);
          }
        }
      }
    }
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig(...$params) {
    list($file) = $params;
    $config_data = parent::getConfig($file);
    if ($config_data) {
      if (!empty($config_data['breakpoints'])) {
        foreach ($config_data['breakpoints'] as $ndx => &$config) {
          $config['name'] = $config['machine_name'];
          $config['id'] = 'breakpoints.theme.' . $config_data['machine_name'] . '.' . $config['machine_name'];
          $config['group_name'] = $config_data['machine_name'];
          $config['weight'] = $ndx;
          if (empty($config['multiplier'])) {
            $config['multiplier'] = ['1x' => '1x'];
          }
          else {
            $multiplier = $config['multiplier'];
            $config['multiplier'] = [];
            foreach ($multiplier as $m) {
              $config['multiplier'][$m] = $m;
            }
          }
        }
      }
    }
    return $this->processConfigData($config_data);
  }
}
