# Breakpoint Groups

Breakpoint Groups contain the configuration of breakpoints and their grouping. Breakpoints are needed for the picture module, so it can map a specific image style to a given breakpoint.

Breakpoint groups should resemble the frontend breakpoint configuration.

Breakpoint groups are defined in one yaml file which is stored in the `.tools/es/breakpoint_groups`-folder.

A group consists of a list of breakpoints which store a machine-name a media-query and a list of multipliers (eg 2x for retina-displays)


### Example

_.tools/es/breakpoints/global.yaml_

```yaml
name: Global
machine_name: global
multiplier:
  - 1x
  - 2x
breakpoints:
  # Mobile (portrait and landscape)
  - machine_name: mobile
    media: '@media only screen and (min-device-width : 320px) and (max-device-width : 480px)'

  # Desktop and laptops
  - machine_name: desktop
    media: '@media only screen  and (min-width : 1224px)'

  # Large screens
  - machine_name: large
    media: '@media only screen  and (min-width : 1824px)'
```

