## What is it?
Entity Scaffolder is a drush plugin.

It reads the configuration from yaml files and creates appropriate feature files and preprocess functions.

## For Drupal 8 and above
Check out https://github.com/factorial-io/phab-entity-scaffolder/ project for drupal 8 and above.

## Documentation

A detailed documentation is hosted here https://factorial-io.github.io/entity_scaffolder/.
A video introduction can be found here https://vimeo.com/260325910

## Installation
- Checkout the code in `~/.drush/` so that it is recognized as plugin.
- run `composer install`

## Example Usage:
1. Create gallery.yaml under `%drupal_root%/.tools/es/fpp/` so the structure looks like the following.

    ```
        .tools
         |__es
            |__fpp
               |__gallery.yaml
    ```

2. Populate `gallery.yaml` with following content

    ```
    name: "Awesome Gallery"
    machine_name: "gallery"
    fields:
      slideshow_images:
        map: patternImages
        type: image
        cardinality: -1
        label: Some Images
        image_style: thumbnail
      headline:
        map: description
        type: text_long
        text_format_filtered_html: full_html
        label: Headline
      caption:
        map: title
        type: text
        text_format_filtered_html: full_html
        label: Caption

    ```

3. Run `drush es` in %drupal_root%
4. A feature called `fe_es` and a module called `es_helper` should be created automatically with required files.
5. Verify that the FPP called "Awesome Gallery" with "Some Images" field is created.
6. Verify that preprocess function works as expected by creating a template called `fieldable-panels-pane--gallery.tpl.twig` and inspecting variable `patternImages`
