# Assets for the project

**index.ts**: Serves solely as a re-exporter of important components from the library (barrel export). Its real utility comes into play when the library is published on NPM.

**load.tsx**: Responsible for loading/mounting the library's components in the DOM with React. This is the entry file for Webpack to generate the bundle.

The currently used scripts are:

```bash
npm run watch 
```
This command is used to enable watch mode, allowing real-time changes. However, reloading the page is necessary to see the changes. Additionally, it's agreed upon to install the assets with Symfony using the command bin/console assets:install --symlink.


```bash
npm run build
```

This command is used to generate the library bundle in production mode.


