<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\d7_1\ESBaseInterface;
use Drush\EntityScaffolder\d7_1\ESEntity;
use Drush\EntityScaffolder\Logger;

class ESEntityNode extends ESEntity implements ESBaseInterface {

  public function help() {
    Logger::log('[node] : Node', 'status');
    Logger::log('Following are the values supported in configuration', 'status');
    $headers = array('Property', 'Variable type', 'Description');
    $data = [];
    $data[] = ['name', 'string' ,'The label of the fpp, which is displayed to the editors.'];
    $data[] = ['machine_name', 'machine name (string)' , 'Internal machine name.'];
    $data[] = ['fields', 'array' ,'Array of field definitions that is attached to the entity'];
    Logger::table($headers, $data, 'status');
  }

  public function __construct(Scaffolder $scaffolder) {
    parent::__construct($scaffolder);
    $this->plugins['preprocess'] = new ESFieldPreprocessNode($this->scaffolder);
    $this->setTemplateDir('/entity/node');
  }

  public function generateCode($info) {
    Logger::log('NOTE: Entity Scaffolder does not generate definitions for nodes.', 'status');
  }

  /**
   * Loads scaffold source files.
   */
  public function loadScaffoldSourceConfigurations() {
    return Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/node');
  }

}
