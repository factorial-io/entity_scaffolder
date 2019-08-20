# Responsive Images

!!! note
    Responsivie image solution depends on **[Breakpoint Groups](/configurations/breakpoint_groups/)** being defined first. 

### Define Breakpoint Groups first

Lets say we have set of breakpoints `mobile`, `desktop` and `large` as defined in [the example for Breakpoints Groups](/configurations/breakpoint_groups/#example)

### Responsive Images definitions

Responsive image definitions lies inside responsive_images folder. Please check the comments on details about what each parameters does.

#### Example

_.tools/es/responsive_images/popup.yaml_

```yaml
machine_name: popup
# Breakpoint group's machine name we have to use. 
breakpoint_group: global
# Multipliers present in breakpoint group. 
# @TODO in future, this would be read from breakpoint group defnitions.
multipliers:
  - 1x
  - 2x
# Define width and height of the images that we need for each breakpoint.
mapping:
  # The key would be breakpoint machine_name
  mobile:
    # Define dimentions of the images for the breakpoint.
    width: 100
    height: 200
  desktop:
    # The image would be resized to the dimention provided using focal_point_scale_and_crop effect.
    width: 981
    height: 552
  large:
    # If any of the dimention is left empty, then image will be scaled to that dimension.
    width: 1080
```


