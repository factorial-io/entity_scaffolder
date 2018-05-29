# Image styles

The folder `image_style` contains a set of yaml-files defining one or more image styles. Each yaml-file contains a set of image styles.

Here's an example:

```yaml
prefix:
  machine_name: article_image_portrait_
  name: "Article Image (portrait)"

multiplier:
  - 1x
  - 2x

image_styles:
  - machine_name: xl-viewport
    effects:
      - name: image_scale
        data:
          width: 468
  - machine_name: lg-viewport
    effects:
      - name: image_scale
        data:
          width: 468
  - machine_name: md-viewport
    effects:
      - name: image_scale
        data:
          width: 468
  - machine_name: sm-viewport
    effects:
      - name: image_scale
        data:
          width: 288
```

When using the `prefix`the name of a single image style will be `<prefix/machine_name><machine_name>`  e.g. `article_image_portrait_md-viewport`

`effects` contains a list of image effects. `es`supports these image-effects:

* `image_scale`
* `image_desaturate`
* `focal_point_scale_and_crop`