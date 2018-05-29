# What are features?

[Features](https://www.drupal.org/project/features) provides a UI and API for
taking different site building components from modules with exportables and
bundling them together in a single feature module. A feature module is like any
other Drupal module except that it contains additional information in its info
file so that configuration can be checked, updated, or reverted programmatically.


Entity Scaffolder makes use of features module to export its code. The actual
creation and update of the entities and fields are taken care by Features module.


## fe_es

!!! warning
    Do not edit this feature manually. Changes will be lost/overriden next time
    you run `drush es`.

Main features module created by Entity Scaffolder. Everytime you run `drush es`
the folder gets overwritten.


## fe_es_filters

Provides default text filters. The feature is not over-written in subsequent runs.
This allows updating and export of the feature per instance.

[Read more about text filters and input format on
Drupal.org](https://www.drupal.org/node/213156)