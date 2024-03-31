const path = require('path');
const fs = require('fs');

const folderPath = path.resolve(__dirname, '../node_modules/@uppy/locales/src');
const locales = fs
  .readdirSync(folderPath)
  .map((fileName) => {
    const { name } = path.parse(fileName);
    return name;
  }) || [];

module.exports = locales;
