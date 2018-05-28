# Factorial frontend theme

> A boilerplate for a Factorial frontend theme

## Prerequisites

* [node.js](https://nodejs.org/en/)
* [yarn](https://yarnpkg.com/en/)
* [composer](https://getcomposer.org/)

## Installation

    $ composer install
    $ yarn install
    $ yarn build:pattern-lab

## Available tasks

`yarn run ...` | Description
---|---
**start** | Start a development server at `http://localhost:4567`.
**build** | Compile and bundle all CSS and JS files.
build:css | Compile and bundle all CSS files to `build/index.css`.
build:js | Compile and bundle all JS files to `build/index.js`.
build:export-colors | Convert PostCSS color variables to JSON.
build:svg-sprite | Process files from `assets/svg` and create a sprite map using symbols
build:pattern-lab | Generate a pattern lab build
test | Run all tests.
test:browser | Run all browser tests.
test:lint-css | Lint all CSS files.
test:lint-js | Lint all JS files.