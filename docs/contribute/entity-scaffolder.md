If you get stuck at any point, please create a [ticket on GitHub](https://github.com/factorial-io/entity_scaffolder/issues).

## Plugin enhancement
Contribution to plugin itself requires a good understanding of PHP, Drupal and features.
The Scaffolder for D7 tries to create files, which features would have created from configurations stored in the database.

If you want to help in creating more plugins or enhance existing ones, please contact the maintainers of the project via GitHub issue queue.

## Contributing Field templates

Entity Scaffolder is very opinionated. The definitions created by Entity Scaffolder follows a certain pattern. It uses templates with very restricted support of configurations. Thus, it creates very similar definitions (like field base, field instance, field preprocessing ...) across the whole project.

Following path placeholders would be used in the examples that follows,

- `%template_dir%` : The template directory for scaffolder plugins for the given version. Eg, `/Drush/EntityScaffolder/d7_1/templates/`
- `%field_name%` : Name of the field that would be used as field type in input yaml files.

Field base, Field Instance and Field Preprocess definitions are all created under `%template_dir%/%field_name%`

### Field Base
The templates and configurations required to create a field_base definition. Each new field definitions are added in **field_base** directory under `%template_dir%/%field_name%/`.

Eg. for **file** field,
```
%template_dir%/file/field_base
    config.yaml
    feature.content.twig
```
`config.yaml` supports following options

 Options       |    Details
---------------|--------------------------
  dependencies | Used to declare the field dependency with Drupal modules.
  variables    | An array of variables that is used to create the fields. The key is the name of the variable.
  variables.%key%.required | Boolean indicating if the variable is mandatory or not.
  variables.%key%.default  | Value that is assigned to the variable if user doesn't provide it in the configuration.

*Sample config.yaml*
```yaml
# Declare the field dependency with Drupal files and media.
dependencies :
  - file
  - media
variables :
  cardinality :
    type : numeric
    placeholder : 1
    # Make the field as non mandatory, i.e. no need to be supplied in yaml configuration.
    required : false
    # Use default value 1, when cardinality is not provided.
    default : 1
```

*Sample feature.content.twig*
```php


  // Exported field_base: '{{ field_name }}'.
  $field_bases['{{ field_name }}'] = array(
    'active' => 1,
    'cardinality' => {{ cardinality }},
    'deleted' => 0,
    'entity_types' => array(),
    'field_name' => '{{ field_name }}',
    'indexes' => array(
      'fid' => array(
        0 => 'fid',
      ),
    ),
    'locked' => 0,
    'module' => 'file',
    'settings' => array(
      'display_default' => 0,
      'display_field' => 0,
      'uri_scheme' => 'public',
    ),
    'translatable' => 0,
    'type' => 'file',
  );
```
The content of `feature.content.twig` is the snippet required to create the feature export for field_base.

!!! tip
    To generate above code,

    - Create a dummy field using Drupal's Field UI.
    - Export the dummy field using feature
    - Copy the base definition to feature.content.twig
    - Replace the relevant part with placeholders. Eg. field_name and cardinality as seen above.

### Field Instance
Field Instance definition generation is almost same as field base definition.
```
%template_dir%/file/field_instance
    config.yaml
    feature.content.twig
```
Instead of field_base, you would put the templates in field_instance.
Replace the content in feature.content.twig with field instance output from exported dummy feature.

### Field Preprocess
```
%template_dir%/file/preprocess
    config.yaml
    code.content.twig
    pattern.twig
```

- config.yaml follows more or less same convention as for field_base and field_instance's config.yaml
- code.content.twig would contain the variables preprocessing template for theme.
- pattern.twig will hold template for helper comment in twig template, showing details of variable and its mapping.
