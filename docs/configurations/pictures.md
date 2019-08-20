# Pictures

!!! warning
    **DEPRECATED**

    Most of the time you would need the [Responsive Images](/configurations/responsive_images/) solution. Use this only if you want to create standalone image styles in rare cases.

The folder `picture` contains a set of yaml-files defining one or more picture-configurations. A picture has a machine name and a mapping of breakpoints, multipliers to image-styles.

The picture-module will render a `<picture>`-element applying the configuration.

A yaml-file can contain only one configuration for a picture. It references the breakpoint_group and has a `mapping` mapping breakpoints, multipliers to a given image-style.

Here's an example:

```yaml
machine_name: article_image_portrait
name: "Article image (portrait)"
breakpoint_group: frontend
mapping:
  xl-viewport:
    1x: article_image_portrait_xl_viewport_1x
    2x: article_image_portrait_xl_viewport_2x
  lg-viewport:
    1x: article_image_portrait_lg_viewport_1x
    2x: article_image_portrait_lg_viewport_2x
  md-viewport:
    1x: article_image_portrait_md_viewport_1x
    2x: article_image_portrait_md_viewport_2x
  sm-viewport:
    1x: article_image_portrait_sm_viewport_1x
    2x: article_image_portrait_sm_viewport_2x
```

