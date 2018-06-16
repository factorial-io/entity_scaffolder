# config.yaml

Project specific configuration file.

All global settings required for the EntityScaffolder will be stored in this file. This file should be located in the directory supplied via `config-dir` option for `drush es`.

The default location if no `config-dir` is supplied would be `.tools/es/config.yaml`.

### Example

```yaml
project_short_code : mic
templates:
  factorial:
    # Can be FALLBACK, EXTEND, OVERRIDE
    type: OVERRIDE
    dir: /.entity_scaffolder/factorial

# Pass the project specific directory locations.
directories :
  theme : sites/all/themes/custom/my_custom_theme
  templates : sites/all/themes/custom/my_custom_theme/templates
  patternlab : sites/all/themes/custom/my_custom_theme/source
```

### Key/value pairs

The following key/value pairs provide meta-data about your scaffolder configuration and define some of the basic functionality.

- project_short_code (required)
- templates (optional)
- directories (required)

#### project_short_code (required)

Project short code. Used for various prefixes like module names, theme names, etc. Suggested to use 2 or 3 alphabets.

```yaml
project_short_code : mic
```

### templates (optional)

A list of custom template definitions for scaffolding.

Supports 3 strategies,

- **DEFAULT** - Replace the template definitions provided by Entity Scaffolder, and sets itself as the default.
- **FALLBACK** - Use these templates only if they are missing in *DEFAULT*
- **EXTEND** - Use *DEFAULT* template only if they are missing in these templates.

So in short, EXTEND > DEFAULT > FALLBACK.

```yaml
templates:
  factorial:
    # Can be FALLBACK, EXTEND or OVERRIDE.
    type: OVERRIDE
    # path relative to `config-dir` with a leading slash.
    dir: /.entity_scaffolder/factorial
```

#### directories (required)

Entity Scaffolder requires location of some directories to write generated files.
These can be supplied as such.

```yaml
# Pass the project specific directory locations.
directories :
  # Location of custom theme
  theme : sites/all/themes/custom/my_custom_theme
  # Location of template directory within the custom theme
  templates : sites/all/themes/custom/my_custom_theme/templates
  # Location of patternlab source folder.
  patternlab : sites/all/themes/custom/my_custom_theme/source
```
