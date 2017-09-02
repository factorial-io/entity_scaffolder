<?php

require_once "ESEntityBase.php";

class ESEntityFPP extends ESEntityBase {

  public function generateCode($info) {
    $module = 'fe_es';
    $filename = 'fe_es.fieldable_panels_pane_type.inc';
    // Add File header.
    $block = ScaffolderBase::HEADER;
    $key = 0;
    $template = '/entity/fpp/feature.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add Code block.
    $block = ScaffolderBase::CONTENT;
    $key = $info['machine_name'];
    $template = '/entity/fpp/feature.content';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add file footer.
    $block = ScaffolderBase::FOOTER;
    $key = 0;
    $template = '/entity/fpp/feature.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add entry to info file.
    $code = "\nfeatures[fieldable_panels_pane_type][] = {$info['machine_name']}";
    $module = 'fe_es';
    $filename = 'fe_es.info';
    $block = ScaffolderBase::CONTENT;
    $key = $code;
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

  }

  /**
   * Helper functions to create FPPS.
   */
  public function scaffold() {
    $config_files = Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/fpp');
    foreach ($config_files as $file) {
      $config = self::getConfig($file);
      $this->generateCode($config);
      if ($config['fields']) {
        $base = new ESFieldBase($this->scaffolder);
        $base->scaffold($config);
        $instance = new ESFieldInstance($this->scaffolder);
        $instance->scaffold($config);
        // ESFieldPreprocess::scaffold($config, $code);
      }
    }
  }

  /**
   * Helper function to load config and defaults.
   */
  public function getConfig($file) {
    $config = parent::getConfig($file);
    $config['entity_type'] = 'fieldable_panels_pane';
    $config['bundle'] = $config['machine_name'];
    $config['field_prefix'] = 'fpp_' . $config['machine_name'];
    return $config;
  }

}
