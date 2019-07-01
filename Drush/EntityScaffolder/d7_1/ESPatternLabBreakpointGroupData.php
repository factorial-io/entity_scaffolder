<?php

namespace Drush\EntityScaffolder\d7_1;

class ESPatternLabBreakpointGroupData extends ESBreakPointGroup {
  public function generateCode($info) {
    // Associate breakpoint with a group.
    $module = 'patternlab_breakpoint_group_data';
    $filename = 'breakpoints.yaml';

    $block = Scaffolder::CONTENT;
    $key = 'breakpoint_groups:' . Scaffolder::CONTENT . $info['group_name'] . Scaffolder::CONTENT . $info['machine_name'];

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
    $key = 'breakpoint_groups:' . Scaffolder::CONTENT . $info['machine_name'] . Scaffolder::HEADER;
    $template = '/patternlab_breakpoint_group_data/code.breakpoint_groups.group';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);
  }
}
