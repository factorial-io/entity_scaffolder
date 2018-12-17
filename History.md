
7.2.1 / 2018-12-26
==================
  * Add support for checkboxes for entitityrefererence widgets
  * Introduced support for preprocessing for node fields
  * BUGFIX Refactor parent template logic for preprocess function generator
  * Suppress warning while trying to get Picture mapping configuration if empty

7.2.0 / 2018-11-17
==================
  * Refactor field preprocessing logic
  * Add warning if mapping in picture template is missing
  * Refactor getTemplateFile and checkTemplateFileExists to make it more reusable against field based preprocessors
  * Introduce Generic field processing functions to pass values to Frontend
  * Add empty check to variable to avoid confusion in preprocessed vars
  * Rework max_length template for text and text-long
  * Fix maxlength_js bug for text_long

7.1.4 / 2018-10-25
==================
  * Add IEF widget for entityreferences
  * Fix checkbox fields
  * Use brightcove_field_playlist_browser for playlist videos
  * Add brightcove_playlist field
  * Adjusted paragraph reference
  * Hide language switcher in FPP by default
  * Add helper function to show all file permissions
  * Generate yaml files instead of json files for image data
  * upgrade symphony/yaml
  * Make component name configuration while generating patternlab image data
  * Fix paragraph reference feature export when multiple allowed bundle is chosen
  * Introduce complex data type (supported via paragraph)
  * Add support for default value for checkbox
  * Expose maxlength on text_long
  * Expose default_edit_mode settings to entity scaffolder
  * Bump Scaffolder version
  * Fix list_predefined_options logic when multiple lists are defined
  * Add empty es_helper.list_predefined_options.inc
  * BUGFIX : reintroduce weight into entityreference template
  * Introduce support for list_options_info in ES
  * Add target_sort_by_property option for entityreferences
  * Introduce support to auto-populate brightcove field data with jsold schema
  * Introduce support for options_select in entityreference widget
  * Add instatag to composer.json for bi_d7 project
  * BUGFIX: before path from link fields are used, decode them
  * Logging message improvements to show full path of files when they are copied
  * Add warning if directory configuration is missing
  * Add support to generate demo image data for patternlab
  * Empty variable access check in preprocessing entity reference fields
  * Adjust brightcove audio player data preprocessing
  * Implement an interactive information system for plugins
  * Handle empty height and width for brightcove audio field
  * Bugfix: Don't print height and width if they are not provided for brightcove video
  * Fix the infinite loop issue in hook_update_N
  * User lower case project code for home folder names in BI
  * Use lower case for ssh user name in BI
  * Introduce absolute_url and validate_url variables to linkit instance
  * Add brightcove_audio_data field
  * Add linkit field
  * Refactor Template extention code
  * Change strategy name OVERRIDE to DEFAULT

7.1.2 / 2018-06-16
==================

  * Add support for local template override/extend
  * Drop support for multiple scaffolder strategy in same codebase.
  * "d7" version removed.
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
