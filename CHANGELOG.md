# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v2.0.0-beta.3](https://github.com/ranky/media-bundle/compare/2.0.0-beta.2...v2.0.0-beta.3) - 2023-02-26

### Commits

- docs: update README.md and migration guide [`862a7de`](https://github.com/ranky/media-bundle/commit/862a7decf027e1addb52b79c3e17a5b3fb14e7a2)
- fix: avoid replacing breakpoint in image name [`cf7c03d`](https://github.com/ranky/media-bundle/commit/cf7c03dc7c5a8d2ccb136ed800a2b40555896089)

## [2.0.0-beta.2](https://github.com/ranky/media-bundle/compare/v2.0.0-beta.1...2.0.0-beta.2) - 2023-02-26

### Commits

- refactor: There is no longer any need to resolve the URL/domain or determine whether it is absolute or relative. [`14c3eb8`](https://github.com/ranky/media-bundle/commit/14c3eb8e82021ca0f2f4f4631e0c2e8eee1554bb)
- refactor: remove exception when path is full url and resolve it [`68434b9`](https://github.com/ranky/media-bundle/commit/68434b914312a135b8cbe932a8a011eff0464913)
- refactor: Reduced the number of Twig functions. [`0d1d7dd`](https://github.com/ranky/media-bundle/commit/0d1d7dd38fc287bfb4132c1cf9ff5aa3b955ab4d)
- style: remove extra lines [`26bb06c`](https://github.com/ranky/media-bundle/commit/26bb06cd39213d0b1ea57519671ad829722be4e1)

## [v2.0.0-beta.1](https://github.com/ranky/media-bundle/compare/v1.2.2...v2.0.0-beta.1) - 2023-02-12

### Commits

- test: update test for supporting multiple storage options [`8dc06f1`](https://github.com/ranky/media-bundle/commit/8dc06f1563e893927dfec3051e7083668f782ccb)
- feat: integration with FlysystemBundle for support of different filesystem [`a1ab88a`](https://github.com/ranky/media-bundle/commit/a1ab88a8dea24f277b069bc0c87f730b07e0a4a2)
- Feat: update application for supporting multiple storage options. Thanks to FlysystemBundle [`839a381`](https://github.com/ranky/media-bundle/commit/839a381c72a851048e0c68f454a703f5b4777f22)
- refactor: change builder configuration in multiple methods to improve its functionality and performance [`4c1a727`](https://github.com/ranky/media-bundle/commit/4c1a727b1c1407ba3f9311db0fbddc64e7005e87)
- feat: remove interface suffix [`a90a583`](https://github.com/ranky/media-bundle/commit/a90a5833508a5fe578a7b8f3922ad7ef53090cb0)
- refactor: modify suffix of event name from 'updated' to 'changed' to conform to standards [`271bd00`](https://github.com/ranky/media-bundle/commit/271bd007c8e394db88a5a974dc1f883589ff008c)
- docs: add a migration guide from version 1 to version 2 [`93e3a9b`](https://github.com/ranky/media-bundle/commit/93e3a9b4bd89cfa115d6fe92e2de0af2446fb1b9)
- refactor(style): update PHPDoc to conform with PHPStan level 8 [`0c3bf32`](https://github.com/ranky/media-bundle/commit/0c3bf328a26eeab50f028f97c247aa5a5fc89659)
- refactor: move exceptions to Filesystem as they are part of infrastructure, not domain [`499a462`](https://github.com/ranky/media-bundle/commit/499a4628277482495d73902ccc1828122cb945d4)
- feat: add new domain exception [`d3001db`](https://github.com/ranky/media-bundle/commit/d3001db085ec57376f66b4fd4c59af2d18d1683b)
- feat: validate `uploadUrl` when the adapter is different from local [`9f3e6fb`](https://github.com/ranky/media-bundle/commit/9f3e6fbc294c6f1354c13fc7074bface4bc7d581)
- feat: update composer with new requirements [`b944162`](https://github.com/ranky/media-bundle/commit/b944162146ae36210e635ef36b4a095723a2dd3a)
- refactor: remove deprecated configuration [`3ef6fde`](https://github.com/ranky/media-bundle/commit/3ef6fde7bb670e23988ce6c695181009c7b2dd6e)
- test: Update .env file with new environment variables for AWS S· [`30d47d5`](https://github.com/ranky/media-bundle/commit/30d47d57f989fdc53df6114f7e071ca81ef60dd9)
- style: update PHP CS Fixer configuration to avoid mbstring extension requeriment [`b7cf54d`](https://github.com/ranky/media-bundle/commit/b7cf54d8c28b9c5d00ac89bc94e059d5c3ba2228)
- fix: correct function return in PHPDoc to comply with PHPStan [`078344d`](https://github.com/ranky/media-bundle/commit/078344dfba9998410d71ccdcd2cf5ead2c7a8f85)
- test: Update behat feature to meet new requirements for supporting multiple storage options [`05dc1cc`](https://github.com/ranky/media-bundle/commit/05dc1cc0147b7caa4a7f746f00ddff87b9be90c2)

## [v1.2.2](https://github.com/ranky/media-bundle/compare/v1.2.1...v1.2.2) - 2023-02-02

### Commits

- ci(refactor): refactor Makefile to improve .env loading and targeting [`e1d6757`](https://github.com/ranky/media-bundle/commit/e1d675727f09cb8bb04b15f9e166585ab62c141b)
- feat(ui): improve media bundle interface [`a72a335`](https://github.com/ranky/media-bundle/commit/a72a335ebdf77aeef253fe575f6a742bd0619818)
- docs: clarify README information [`9c73a6f`](https://github.com/ranky/media-bundle/commit/9c73a6f61bf9d28bef4307f57b42ea91916b3286)
- fix(build): correct .env file exclusion in .gitignore [`0a1ab50`](https://github.com/ranky/media-bundle/commit/0a1ab5088c1b538cf39d9556a871b2dbcebcbf12)
- style: remove double line break [`3507165`](https://github.com/ranky/media-bundle/commit/35071656be8642cf135e542b726b6da31cbf19cf)
- test(refactor): Rename Behat context class for consistency and clarity. [`c3b25c1`](https://github.com/ranky/media-bundle/commit/c3b25c136eaf0a147bb7c7b57fcc098a59edab45)
- ci(refactor): add --no-scripts to composer install [`8b7080f`](https://github.com/ranky/media-bundle/commit/8b7080f7f95ad177717092ce39d14c7cd84e93c9)
- docs: fix typos in the README.md [`c8d77f5`](https://github.com/ranky/media-bundle/commit/c8d77f5ff5dece184324143a106209648281f45d)
- ci(fix): array format behat.yml file [`38751e6`](https://github.com/ranky/media-bundle/commit/38751e697a8ddf3e8ce5eb27132c3c49254ba53b)
- ci(fix): add the main branch to pull request [`a366e05`](https://github.com/ranky/media-bundle/commit/a366e05c5c5909d27ab2788d759ab93b531ecc35)
- ci: ignore file in PHPStan analysis [`66baf24`](https://github.com/ranky/media-bundle/commit/66baf243a0db1b7591100d99ca01457d2edd02dc)

## [v1.2.1](https://github.com/ranky/media-bundle/compare/v1.2.0...v1.2.1) - 2023-01-11

### Commits

- test(fix): fix translation test for modal_title key [`8de549a`](https://github.com/ranky/media-bundle/commit/8de549a3f9677ab8499c37e5fb3f6764673e0f1c)

## [v1.2.0](https://github.com/ranky/media-bundle/compare/v1.1.15...v1.2.0) - 2023-01-11

### Commits

- feat: postgresql support [`8592a90`](https://github.com/ranky/media-bundle/commit/8592a907f82613521d4ed2a47efb7f289007d77b)
- test: postgresql support [`058a65e`](https://github.com/ranky/media-bundle/commit/058a65eb87ed437febe39d937e364f9960b8c010)
- docs: improve README.md [`1ddc45b`](https://github.com/ranky/media-bundle/commit/1ddc45b98a64962a3164a18b9f665fa35b7832db)
- fix: fix overridden styles [`da873e8`](https://github.com/ranky/media-bundle/commit/da873e8a260eda4017a7790c899c086970cdb4a9)
- ci: add the --env-file parameter into DOCKER_COMPOSE variable in the Makefile [`18dd5c0`](https://github.com/ranky/media-bundle/commit/18dd5c0e931f1333e4506e7f66ade0f04777eab5)
- fix: improve modal_title translation key [`f45a6ce`](https://github.com/ranky/media-bundle/commit/f45a6ce2d013056abca5371217067c5f04320938)
- ci: fix pull_request check [`d746344`](https://github.com/ranky/media-bundle/commit/d746344dad53481c3f26c5036e676a5cac7f3dc6)

## [v1.1.15](https://github.com/ranky/media-bundle/compare/v1.1.14...v1.1.15) - 2023-01-10

### Commits

- ci: add new targets to Makefile [`195d946`](https://github.com/ranky/media-bundle/commit/195d946dacfeccb3e0b2fd605342dea4c11950e9)
- refactor: Replace docker-compose with Docker Compose plugin [`1c23756`](https://github.com/ranky/media-bundle/commit/1c2375608568292f102b92ade7438a9cee6ce990)
- refactor: Add environment variables instead of hardcoded values [`0d489a9`](https://github.com/ranky/media-bundle/commit/0d489a986c05f8a22a7f89899209cba074e50d69)
- refactor: Add home user directory [`97b9fe3`](https://github.com/ranky/media-bundle/commit/97b9fe3396416588d0cf31d99dba41190cf679af)
- ci: fix install compose plugin [`45399df`](https://github.com/ranky/media-bundle/commit/45399df45a147bc09a4574474afc59f0a0b0f285)
- refactor: fix typo [`17bcc49`](https://github.com/ranky/media-bundle/commit/17bcc49a35ce4a02f78296dd3ff9226f2f658e60)
- ci: add docker image description [`92c321a`](https://github.com/ranky/media-bundle/commit/92c321a23e406b32d261b366131dbbafbe5e0665)
- test: add --complete argument for avoiding deprecated warning [`04592b3`](https://github.com/ranky/media-bundle/commit/04592b354f6de4d7c5ae6f1fe9f8ce4e3d5d11af)

## [v1.1.14](https://github.com/ranky/media-bundle/compare/v1.1.13...v1.1.14) - 2022-12-28

### Commits

- doc: Improvements to README [`6f14080`](https://github.com/ranky/media-bundle/commit/6f140801427e7f55aead91e83ef387bb6d1b85bf)

## [v1.1.13](https://github.com/ranky/media-bundle/compare/v1.1.12...v1.1.13) - 2022-12-28

### Commits

- ci: Improvements to GitHub actions [`c22c065`](https://github.com/ranky/media-bundle/commit/c22c065a73be08371b6c20880617fad482294e11)

## [v1.1.12](https://github.com/ranky/media-bundle/compare/v1.1.11...v1.1.12) - 2022-12-27

### Commits

- ci: Update docker image path [`e1f9386`](https://github.com/ranky/media-bundle/commit/e1f9386d0958b4082754ba62c3af5eae91697434)

## [v1.1.11](https://github.com/ranky/media-bundle/compare/v1.1.10...v1.1.11) - 2022-12-27

### Commits

- ci: Improve GitHub Actions workflow [`268eb4b`](https://github.com/ranky/media-bundle/commit/268eb4b7a3b7c9253bde74940d27b2292ff79031)

## [v1.1.10](https://github.com/ranky/media-bundle/compare/v1.1.9...v1.1.10) - 2022-12-24

### Commits

- Fix code style issues using PHP-CS-Fixer [`8deba68`](https://github.com/ranky/media-bundle/commit/8deba68998e1754dcd68ac1c9b7072450c015a10)
- Added workflow [`68900cc`](https://github.com/ranky/media-bundle/commit/68900ccaca248ec3ff7ba4cbb129a227cea38194)

## [v1.1.9](https://github.com/ranky/media-bundle/compare/v1.1.8...v1.1.9) - 2022-12-24

### Commits

- Updated with changes related to the implementation of GitHub Actions [`2ac1d36`](https://github.com/ranky/media-bundle/commit/2ac1d36cabd9c65933949d23f0c4551cdb9ca684)

## [v1.1.8](https://github.com/ranky/media-bundle/compare/v1.1.7...v1.1.8) - 2022-12-22

### Commits

- Improved preview of Mime Types [`b454419`](https://github.com/ranky/media-bundle/commit/b45441917fbd4dd1ce0ac14096132e0fc6b24d8e)

## [v1.1.7](https://github.com/ranky/media-bundle/compare/v1.1.6...v1.1.7) - 2022-12-22

### Commits

- PSD twig template [`5a609e1`](https://github.com/ranky/media-bundle/commit/5a609e18c0272d5d52d49b4fcd27571cbf8b617e)

## [v1.1.6](https://github.com/ranky/media-bundle/compare/v1.1.5...v1.1.6) - 2022-12-21

### Commits

- Improve Makefile [`9d3bec8`](https://github.com/ranky/media-bundle/commit/9d3bec82b82dff74bd3c800f6b1cc25ef6808fa6)
- Make the user_entity optional [`2a71670`](https://github.com/ranky/media-bundle/commit/2a71670c191c17fcd67a9d24fad4515f797fd681)
- Updated the README, to clarify some points and add some more information. [`f6d5f4d`](https://github.com/ranky/media-bundle/commit/f6d5f4d4bba92f23fb48752833ba975b9651f377)
- Update .gitattributes so that Composer does not install unnecessary files. [`9f32ec7`](https://github.com/ranky/media-bundle/commit/9f32ec7783234ca27d8e6298a7e1bd13f8b1706c)

## [v1.1.5](https://github.com/ranky/media-bundle/compare/v1.1.4...v1.1.5) - 2022-12-21

## [v1.1.4](https://github.com/ranky/media-bundle/compare/v1.1.3...v1.1.4) - 2022-12-21

### Commits

- Add an external link on media files in EasyAdmin list view [`5c5fe2b`](https://github.com/ranky/media-bundle/commit/5c5fe2bf39dd158970008d2148833b5180ae3f55)
- Updated the README, to clarify some points and add some more information. [`e328643`](https://github.com/ranky/media-bundle/commit/e328643e50c988499a33f991405a7dc415f920d8)
- Fix preview text media [`dd3a1fb`](https://github.com/ranky/media-bundle/commit/dd3a1fb22d1b48c6887256a54a05f3700081ecae)

## [v1.1.3](https://github.com/ranky/media-bundle/compare/v1.1.2...v1.1.3) - 2022-12-21

### Commits

- Updated README [`df1bbd6`](https://github.com/ranky/media-bundle/commit/df1bbd685b19bbce2f44646371cc854d70046e4c)

## [v1.1.2](https://github.com/ranky/media-bundle/compare/v1.1.1...v1.1.2) - 2022-12-21

### Commits

- Add check for invalid GD and Imagick extensions, update README, add PSD preview image [`5df6bb5`](https://github.com/ranky/media-bundle/commit/5df6bb52605bedd37624a2b042b80f612e6382cb)

## [v1.1.1](https://github.com/ranky/media-bundle/compare/v1.1.0...v1.1.1) - 2022-12-21

### Commits

- Add check for invalid GD and Imagick extensions, update README, add PSD preview image [`395a752`](https://github.com/ranky/media-bundle/commit/395a752fd80abce0ff799c2c19effb807763018b)

## [v1.1.0](https://github.com/ranky/media-bundle/compare/v1.0.0...v1.1.0) - 2022-12-20

### Commits

- Update README, EasyAdmin integration, SQLite support and some other small fixes [`21b16cb`](https://github.com/ranky/media-bundle/commit/21b16cb6f6320a529022fda81c55f019a4911493)

## v1.0.0 - 2022-12-18

### Commits

- first commit [`5e46a1d`](https://github.com/ranky/media-bundle/commit/5e46a1d34c056914d1348d2b66bdb5f954e13d65)
