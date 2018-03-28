<?php

/**
 * @file
 * Provides preprocess logic and other functionality for theming.
 */

// Ensure that __DIR__ constant is defined:
if (!defined('__DIR__')) {
  define('__DIR__', dirname(__FILE__));
}

// Require files.
require_once __DIR__ . '/includes/theme.inc';
require_once __DIR__ . '/includes/form.inc';
require_once __DIR__ . '/includes/menu.inc';
require_once __DIR__ . '/includes/node.inc';
require_once __DIR__ . '/includes/fieldable_panels_pane.inc';
require_once __DIR__ . '/includes/paragraphs.inc';
require_once __DIR__ . '/includes/views.inc';
require_once __DIR__ . '/includes/panels.inc';
require_once __DIR__ . '/includes/helper.inc';
require_once __DIR__ . '/includes/twig.inc';
require_once __DIR__ . '/includes/page.inc';
require_once __DIR__ . '/includes/css_js.inc';
