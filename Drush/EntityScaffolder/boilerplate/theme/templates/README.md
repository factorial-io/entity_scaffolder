# Factorial frontend stack 

> A boilerplate for a Factorial frontend theme

## Prerequisites

* [node.js](https://nodejs.org/en/)

## Installation

    $ npm install

## Available tasks
 
`npm run ...` | Description
---|---
build | Compile and bundle all CSS and JS files.
build:css | Compile and bundle all CSS files to `build/index.css`.
build:js | Compile and bundle all JS files to `build/index.js`.
build:test | Copy and preprocess idiomatic test files to `build`.
build:svg-sprite | Process files from `assets/svg` and create a sprite map using symbols
build:pattern-lab | Generate a pattern lab build
deploy | Deploy `gh-pages` branch.
start | Start a development server at `http://localhost:8080`.
test | Run all tests. 
test:browser | Run all browser tests.
test:lint-css | Lint all CSS files.
test:lint-js | Lint all JS files.
watch | Watch for file changes in `lib` and trigger a new build.

## Common modules 

This repository already includes a collection of our most commonly used modules. 
These are proven to be robust in real-world applications. Please opt-out of 
anything you don't use by removing them from the dependencies in `package.json` 
before moving to production. See their respective github repos for documentation. 

### CSS 

Module | Description
---|---
[factorial-utils-fonts](https://github.com/factorial-io/utils-font) | Mostly sane typographic base utilities 
[factorial-utils-margin](https://github.com/factorial-io/utils-margin) | Low-level and immutable margin utilities
[factorial-utils-padding](https://github.com/factorial-io/utils-padding) | Low-level and immutable padding utilities
[suitcss-base](https://github.com/suitcss/base) | Suit CSS base styles, resets margins etc.
[suitcss-components](https://github.com/suitcss/components) | Collection of Suit CSS components
[suitcss-utils](https://github.com/suitcss/utils) | Collection of Suit CSS utilities

### JavaScript

Module | Description
---|---
[enquire.js](https://github.com/WickyNilliams/enquire.js/) | Media Queries in JavaScript
[lazysizes](https://github.com/aFarkas/lazysizes) | Lazyloading done right
[lodash](https://github.com/lodash/lodash) | General purpose utility library
[picturefill](https://github.com/scottjehl/picturefill) | Responsive images using the picture element
[svg4everybody](https://github.com/jonathantneal/svg4everybody) | Fixes remote SVG symbols in IE.
[swiper](https://github.com/nolimits4web/swiper/) | A modern slider component
[waypoints](https://github.com/imakewebthings/waypoints) | Create basic scroll events
