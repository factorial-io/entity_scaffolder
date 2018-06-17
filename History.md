7.1.x / WIP
==================

  * Add History.md
  * Add AUTHORS
  * Add support for locally overriding template per project
  * Removed support for 7.1.0

7.1.1 / 2018-06-16
==================

  * Add warning in generated code to discourage manual modification
  * Create LICENSE
  * Introduce experimental Boilerplate Code generation
  * Version can be supplied as option while runnings scaffolder
  * Prompt for user input if ES is going to run on a newer version than the one which was used to scaffold last time
  * Log scaffolder version into config directory after scaffolding is completed
  * When trying to use invalid field machine name, provide a warning message
  * Add weight and default to to scaffolder field instances
  * Field Type can be extended by using `parent` key in config.yaml for field types
  * Add picture_mapping scaffolder
  * Restructure template locations and generation
  * search for config file in `.tools/es` by default

7.1.0 / 2017-11-12
==================

  * Add an example of image style with no data configuration
  * Introduce ImageStyle plugin
  * Add picture field templates
  * Support for optional description field in FPP definition added.
  * Support for validation of required fields added.
  * Fixed a bug where cardinality could have been set to zero
  * Restructuring EntityScaffolder to use autoloading and namespaces
  * Add support for paragraph entities
  * Add exclusive feature for text formats
  * Dependency to modules can be read from templates
  * Add helper function to support partial deployment of Panel pages
  * Replace template engine with twig
  * Add support for cardinality for fields
  * Add support for Field Instance
  * Add support for Field Base
  * Add support for FPP
  * search for config file in `_tools/es` by default
