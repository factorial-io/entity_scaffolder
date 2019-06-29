<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Symfony\Component\Yaml\Yaml;
use Drush\EntityScaffolder\d7_1\ESBaseInterface;
use Drush\EntityScaffolder\d7_1\ESEntity;
use Drush\EntityScaffolder\d7_1\ESPicture;
use Drush\EntityScaffolder\Logger;

class ESPatternLabBreakpointGroupData extends ESBreakPointGroup {
  public function generateCode($info) {
    // Associate breakpoint with a group.
    $module = 'patternlab_breakpoint_group_data';
    $filename = 'breakpoints.yaml';

    $block = Scaffolder::CONTENT;
    $key = 'breakpoint_groups:' . Scaffolder::CONTENT . Scaffolder::HEADER . $info['machine_name'];
    $template = '/patternlab_breakpoint_group_data/code.breakpoint_groups.group_item';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);


    $block = Scaffolder::CONTENT;
    $key = 'breakpoints:' . Scaffolder::HEADER;
    $template = '/patternlab_breakpoint_group_data/code.breakpoint_groups.breakpoint.header';
    $code = $this->scaffolder->render($template, []);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $block = Scaffolder::CONTENT;
    $key = 'breakpoints:' . Scaffolder::CONTENT . $info['machine_name'];
    $template = '/patternlab_breakpoint_group_data/code.breakpoint_groups.breakpoint';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);
  }

  public function generateGroupCode($info) {
    $module = 'patternlab_breakpoint_group_data';
    $filename = 'breakpoints.yaml';

    $block = Scaffolder::HEADER;
    $key = 'header';
    $template = '/patternlab_breakpoint_group_data/code.breakpoint_groups.file';
    $code = $this->scaffolder->render($template, []);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $block = Scaffolder::CONTENT;
    $key = 'breakpoint_groups:' . Scaffolder::HEADER;
    $template = '/patternlab_breakpoint_group_data/code.breakpoint_groups.group.header';
    $code = $this->scaffolder->render($template, []);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    $block = Scaffolder::CONTENT;
    $key = 'breakpoint_groups:' . Scaffolder::CONTENT;
    $template = '/patternlab_breakpoint_group_data/code.breakpoint_groups.group';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);
  }
}
