# config.yaml

Project specific configuration file. All global settings required for the EntityScaffolder will be stored in this file. This file should be located in the directory supplied via `config-dir` option for `drush es`.

```yaml
# Pass the project specific directory locations.
directories:
  templates : 'sites/all/themes/custom/MyProjectTheme/templates'
  theme : 'sites/all/themes/custom/MyProjectTheme'
  patternlab: 'sites/all/themes/custom/MyProjectTheme/source'
```

Key | Explanation
----|----
directories | Pass the project specific directory locations
directories.templates  | Path to template folder
directories.theme      | Path to the theme folder
directories.patternlab | Path to patterlab sourcefolder