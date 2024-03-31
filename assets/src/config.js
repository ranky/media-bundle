export default {
  env: process.env.NODE_ENV,
  assets_url_prefix: process.env.ASSETS_URL_PREFIX,
  uppy_locales: process.env.UPPY_LOCALES?.split(','),
  root_class: '.ranky-media',
};
