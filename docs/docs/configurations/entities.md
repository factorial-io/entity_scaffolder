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
