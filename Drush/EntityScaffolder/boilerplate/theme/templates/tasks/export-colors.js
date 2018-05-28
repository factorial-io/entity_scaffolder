const fs = require('fs');
const postcss = require('postcss');
const postCssExportVars = require('postcss-export-vars');

const path = './source/_patterns/particles/utils/utils-color';
const prefix = 'utils-color';
const suffix = 'css';
const type = 'json';
const css = fs.readFileSync(`${path}/${prefix}.${suffix}`, 'utf8');
const outputPath = `${path}/${prefix}.${type}`;

postcss([
  postCssExportVars({
    file: `${path}/${prefix}`,
    type,
    match: ['-color'],
  })])
  .process(css).then(() => {
    const message = `Wrote colors to ${outputPath}`;
    const file = fs.readFileSync(outputPath, 'utf8');
    fs.writeFileSync(outputPath, `{ "colors": ${file} }`, 'utf8');
    console.log(message);
    console.log(`{ "colors": ${file} }`);
  });
