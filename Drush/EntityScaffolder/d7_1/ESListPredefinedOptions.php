<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Logger;
use Drush\EntityScaffolder\Utils;

class ESListPredefinedOptions extends ESBase implements ESBaseInterface {

  public function generateCode($info) {
    $module = 'es_helper';
    $filename = 'es_helper.list_predefined_options.inc';
    // Add File header.
    $block = Scaffolder::HEADER;
    $key = 0;
    $template = '/list_predefined_options/file.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);


    // Add Code block : Hook.
    // Add Code block.
    $block = Scaffolder::CONTENT . ':' . Scaffolder::HEADER;
    $key = $info['hook_list_options'] . ' : ' . Scaffolder::HEADER;
    $template = '/list_predefined_options/code.hook_list_options.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $block = Scaffolder::CONTENT . ':' . Scaffolder::HEADER;
    $key = $info['hook_list_options'] . ' : ' . Scaffolder::CONTENT . ' : ' . $info['machine_name'];
    $template = '/list_predefined_options/code.hook_list_options.content';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $block = Scaffolder::CONTENT . ':' . Scaffolder::HEADER;
    $key = $info['hook_list_options'] . ' : ' . Scaffolder::FOOTER;
    $template = '/list_predefined_options/code.hook_list_options.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add Code block : Function.
    $block = Scaffolder::CONTENT . ':' . Scaffolder::CONTENT;
    $key = $info['list_options_function'] . ' : ' . Scaffolder::HEADER . ' : ' . $info['machine_name'];
    $template = '/list_predefined_options/code.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $block = Scaffolder::CONTENT . ':' . Scaffolder::CONTENT;
    $key = $info['list_options_function'] . ' : ' . Scaffolder::CONTENT . ' : ' . $info['machine_name'];
    $template = '/list_predefined_options/code.content';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $block = Scaffolder::CONTENT . ':' . Scaffolder::CONTENT;
    $key = $info['list_options_function'] . ' : ' . Scaffolder::FOOTER . ' : ' . $info['machine_name'];
    $template = '/list_predefined_options/code.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);


    // Add file footer.
    $block = Scaffolder::FOOTER;
    $key = 0;
    $template = '/list_predefined_options/file.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

  }
    /**
     * Loads scaffold source files.
     */
    public function loadScaffoldSourceConfigurations() {
        return Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/list_predefined_options');
    }

    /**
     * The main scaffolding action.
     */
    public function scaffold() {
        $config_files = $this->loadScaffoldSourceConfigurations();
        if ($config_files) {
            foreach ($config_files as $file) {
                $config = $this->getConfig($file);
                $this->generateCode($config);
            }
        }
    }
}
