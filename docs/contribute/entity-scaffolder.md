If you get stuck at any point you can create a [ticket on GitHub](https://github.com/factorial-io/entity_scaffolder/issues).

## Plugin enhancement
Contribution to plugin itself requires a good understanding of PHP, Drupal and features. The Scaffolder for D7 tries to create files which features would have created from configurations stored in the database. Contact the core developers to see how you could help in this department.


## Contributing Field templates
### Field Base
The templates and configurations required to create a field_base definition. Each new field definitions are added as new folder under `/Drush/EntityScaffolder/d7/templates/field_base`.
```
Drush/EntityScaffolder/d7/templates/field_base/file
    config.yaml
    feature.content.twig
```
`config.yaml` supports following options

 Options       |    Details
---------------|--------------------------
  dependencies | Usedto declare the field dependency with drupal modules.
  variables    | An array of vriables that is used to create the fields. The key is the name of the variable.
  variables.%key%.required | Boolean indicating if the variable is mandatory or not.
  variables.%key%.default  | Value that is assinged to the variable if user doesn't provide it in the configuration.


The content of `feature.content.twig` is the snippet required to create the feature export for field_base.

### Field Instance
### Field Preprocess
