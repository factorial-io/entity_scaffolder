# What is EntityScaffolder

EntityScaffolder is a scaffolding tool. It is used to create Entities and Fields quickly and conviniently for a Drupal project.

The EntityScaffolder makes use of the Drush and other third party components which allows you to automatically generate much of code required to create some entities and fields. In addition, EntityScaffolder also provides some helper functions to support theming integration with patternlab based frontend.

Some common tasks are:

 * Create Entites
     - Fildable Panels Pane
     - Paragraphs
 * Attach fields to created entities
     - Text
     - Long Text
     - File
     - Image
     - Paragraph
 * Create placeholder Drupal Templates
 * Add some preprocessing to the templates to provide sensible defaults to work with

## Installation

Entity Scaffolder depends on drush (the **DRU**pal **SH**ell). To use entity_scaffodler, you have to first download and install drush from [drush](http://drupal.org/project/drush). Install instructions can be found at Drush's [README.txt](http://drupalcode.org/project/drush.git/blob/HEAD:/README.txt). It contains
a section about installing other commands like drush_make.

Here is the relevant section.

```plain
You can put this folder in a number of places:

- In a .drush folder in your HOME folder. Note, that you have to make the
  .drush folder yourself (so you end up with ~/.drush/entity_scaffodler/README.md).
- In a folder specified with the include option (see above).
- In /path/to/drush/commands (not a Smart Thing, but it would work).
```

## Scaffold File Layout

Your scaffolder source files should be written as regular yaml files, and placed in a directory somewhere in your project. Normally this directory will be named under `_tools/es` and will exist at the top level of your project.

The typical project you can create will look something like this:

```
_tools/es/
    config.yaml
    fpp/
        gallery.yaml
        copy.yaml
        intro.yaml
        video.yaml
        multimedia.yaml
    paragraphs/
        video.yaml
        text.yaml
        headline.yaml
```

## Running EntityScaffolder

Use EntityScaffolder as drush plugin

```shell
$ cd <your-drupal-root-folder>
$ drush es
```

This will read cofniguration files from `_tool/es` directory under `your-drupal-root-folder`.