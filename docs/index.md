
!!! warning
    Version **7.1.0** has been discontinued. It is advised to migrate the project
    to **7.1.1** or above.

## Migration guide

 - The default location of the scaffolder config files have changed from `_tools/es` to `.tools/es`.
   So in case you still want to use the default folder structure, please move the files.
 - Create a `config.yaml` inside `.tools/es` directory. An example configuration file is as follows.
   Please adapt the shortcode and directory locations as per your project.

```
project_short_code : "bif"
# Pass the project specific directory locations.
directories :
  theme : "sites/all/themes/custom/bif_frontend"
  templates : "sites/all/themes/custom/bif_frontend/templates"
  patternlab : "sites/all/themes/custom/bif_frontend/source"
```

  - Create/Update `.es.log.yaml` file in `.tools/es` to with the version of
    scaffolder you want are using.
    Eg.

```
version: "7.1.1"
```

  - run `drush es`
  - check generated code and verify by testing.

!!! note
    The main difference in the generated code should be regarding how the field
    pre-processing is done before it is used in templates. The later strategy
    respects Drupal's field formatters.
