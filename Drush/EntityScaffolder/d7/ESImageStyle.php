<?php

namespace Drush\EntityScaffolder\d7;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\d7\ESBaseInterface;
use Drush\EntityScaffolder\d7\ESEntity;
use Drush\EntityScaffolder\Logger;

class ESImageStyle extends ESEntity implements ESBaseInterface {

  public function help() {
    Logger::log('[image_style] : Image style', 'status');
    Logger::log('Following are the values supported in configuration', 'status');
    $headers = array('Property', 'Variable type', 'Description');
    $data = [];
    $data[] = ['name', 'string' ,'The label of the Style, which is displayed to the admins.'];
    $data[] = ['machine_name', 'machine name (string)' , 'Internal machine name.'];
    $data[] = ['effects', 'array' ,'Array of effects definitions that is attached to the Image Style'];
    Logger::table($headers, $data, 'status');
  }

  public function __construct(Scaffolder $scaffolder) {
    parent::__construct($scaffolder);
    $this->setTemplateDir('/image_style');
  }

  public function generateCode($info) {
    // Add entry to info file.
    $code = "\ndependencies[] = image";
    $module = 'fe_es';
    $filename = 'fe_es.info';
    $block = Scaffolder::CONTENT;
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[features_api][] = api:2";
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
  }

  /**
   * Loads scaffold source files.
   */
  public function loadScaffoldSourceConfigurations() {
    return Utils::getConfigFiles($this->scaffolder->getConfigDir());
  }

}
