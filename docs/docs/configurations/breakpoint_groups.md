# Breakpoint Groups

Breakpoint Groups contain the configuration of breakpoints and their grouping. Breakpoints are needed for the picture module, so it can map a specific image style to a given breakpoint.

Breakpoint groups should resemble the frontend breakpoint configuration.

Breakpoint groups are defined in one yaml file which is stored in the `_tools/es/breakpoint_groups`-folder.

A grou consists of a list of breakpoints which store a machine-name a media-query and a list of multipliers (for retina-displays)

Here's an example:

```yaml
name: Frontend breakpoints
machine_name: frontend
breakpoints:
  - machine_name: xl-viewport
    media: '(min-width: 1600px)'
    multiplier:
      - 1x
      - 2x

  - machine_name: lg-viewport
    media: '(min-width: 1280px)'
    multiplier:
      - 1x
      - 2x

  - machine_name: md-viewport
    media: '(min-width: 1024px) and (max-width: 1279px)'
    multiplier:
      - 1x
      - 2x

  - machine_name: md-lg-viewport
    media: '(min-width: 1024px)'
    multiplier:
      - 1x
      - 2x

  - machine_name: sm-md-viewport
    media: '(max-width: 1279px)'
    multiplier:
      - 1x
      - 2x

  - machine_name: sm-viewport
    media: '(max-width: 1023px)'
    multiplier:
      - 1x
      - 2x
```

