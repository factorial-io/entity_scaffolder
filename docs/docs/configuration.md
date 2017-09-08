# config.yaml

```yaml
# Pass the project specific directory locations.
directories:
  templates : 'sites/all/themes/custom/MyProjectTheme/templates'
  theme : 'sites/all/themes/custom/MyProjectTheme'
  patternlab: 'sites/all/themes/custom/MyProjectTheme/source'
```

# Entities

Use the following table to determine the folder under which the configuration files for entities have to be placed.

Folder Name  |   Entity Type
-------------|--------------
fpp          | Fieldable Panels Panes
paragraphs   | Paragraph Items

Create a file for each entity bundle inside the respective folders. Usually the files are named according to the bundles.

An example bundle specific configuration file looks like below
```yaml
# Label of the Drupal entity bundle.
name: Brochure

# Machine name in Drupal.
machine_name: brochure

# Fields acttached to the bundles.
fields:
  files:
    map: download_brochure
    cardinality: 1
    type: file
    label: PDF of our Brochure
    file_extensions: "pdf"
  introduction:
    map: introduction
    type: text_long
    label: Introduction
    allowed_formats:
      - html
    default_format: html
```

# Field Instance & Field Base
Field Instances and Field Bases are defined in the bundle they are attached to.

Following fields are supported,
## Text
@TODO

## Long Text

 Options         | Descriptions
-----------------|-------------
 type            | Type of the field. Must be `text_long`.
 map             | Patternlab mapping
 cadrinality     | Number of values stored in this field. -1 means unlimitted.
 label           | Drupal Field Label
 allowed formats | List of allowed text formats.
 default_format  | Default text format for the field.

**Example**
```
name: Copy
machine_name: copy
fields:
  content:
    type: text_long
    map: content
    cardinality: 1
    label: Content
    allowed_formats:
      - html
    default_format: html
```
## File
@TODO

## Image
@TODO

## Paragraph
@TODO


