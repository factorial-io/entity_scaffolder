## What is it?
Entity Scaffolder is a drush plugin.

It reads the configuration from yaml files and creates appropriate feature files and preprocess functions.

## Installation
Checkout the code in `~/.drush/` so that it is recognized as plugin.

## Example Usage:
1. Create gallery.yaml under `%drupal_root%/_tools/es/fpp/` so the structure looks like the following.

    ```
        _tools
            \-es
                \-fpp
                    \-gallery.yaml
    ```

2. Populate `gallery.yaml` with following content

    ```
    name: "Awesome Gallery"
    machine_name: "gallery"
    fields:
      slideshow_images:
        map: patternImages
        type: images
        label: Some Images
        image_style: thumbnail
    ```

3. Run `drush es` in %drupal_root%
4. A feature called `fe_es` and a module called `es_helper` should be created automatically with required files.
5. Verify that the FPP called "Awesome Gallery" with "Some Images" field is created.
6. Verify that preprocess function works as expected by creating a template called `fieldable-panels-pane--gallery.tpl.twig` and inspecting variable `patternImages`


