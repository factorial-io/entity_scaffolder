Field Instances and Field Bases are defined in the **Entity bundle** they are attached to.

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


