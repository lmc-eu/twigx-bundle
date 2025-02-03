# Changelog

<!-- There should always be "Unreleased" section at the beginning. -->
## Unreleased

## 4.0.0 - 2025-01-09
- [BC]: Drop support Symfony `4.4` and not LTS versions `6.x`
- [BC]: Drop support PHP `^7.4`
- Feat: Add support for Symfony `^7.2`
- Feat: Introduce support PHP `^8.4`
- Deps: Drop old PHP polyfill packages
- Deps: Update dev dependencies
- Chore: Update docker container php version to `8.4`

## 3.3.0 - 2024-06-12
- Feat: Normalize template name using regex to allow capitalized prefixes

## 3.2.1 - 2023-03-30
- Fix: Negative lookahead breaks parsing of element closing bracket
- Fix: Allow space between double parenthesis refs #DS-657

## 3.2.0 - 2023-01-26
- Docs: Add badge with code coverage status
- Deps: Set phpunit version to at least 9.5.20
- Chore: Publish code coverage report from pipeline to Coveralls
- Chore: Install Xdebug as code coverage driver
- Docs: Add comments to tag compiler regex (refs #DS-537)
- Fix: Remove Twig comments from parsed attributes/props (refs #DS-537)
- Feat: Allow Twig comments be placed between props (refs #DS-537)
- Docs: Remove phar files from comment
- Style: Fix indentation of tabs in Makefile
- Chore: Rename docker container to more specific name

## 3.1.0 - 2022-11-01
- Docs: Add missing changelog messages
- Chore: Introduce `.editorconfig` configuration file
- Chore: Introduce `.gitattributes` file to lower download size
- Docs: Add version badge with link to Packagist
- Chore: Run tests on newer PHP versions
- Chore: Update deprecated github actions

## 3.0.0 - 2022-09-24
- [BC]: Drop support Symfony `3.4` and not LTS versions `4.x` and `5.x`
- Feat: Introduce support PHP `^8.1` and `^6.1`
- Feat: Introduce more similar JSX variables syntax
- Chore: Fix remove unnecessary configuration parameter `css_class_prefix`
- Chore: Fix typo in Makefile command
- Chore: Fix ecs-fix command in Makefile
- Update `lmc/coding-standard` package to version `3.3`

## 2.1.0 - 2022-07-28
- Feat: Enable glob function patterns in a paths (refs #4)

## 2.0.0 - 2022-07-22
- Chore: Introduce self-documented makefile to maintain this project
- Chore: Introduce docker support for better development experience
- Chore: Remove unused command for updating snapshots
- Chore: Remove unused configuration file for coding standard
- Setup CI pipeline using GitHub Actions
- Move components into separate package in Spirit Design System repository
    and make JSX like syntax compiler in Twig as public Symfony bundle
- Rename bundle to lmc/twigx-bundle
- Refactor components and update their readme


## 1.7.0 - 2022-05-09
- Add Svg twig extension for optimal loading of svg files as inline
- Add configuration param `icons` to define icon set path and alias

## 1.6.0 - 2022-04-29
- Add main props `data-*` and `id` into `Button` and `ButtonLink` components
- Introduce `Header`, `Navbar`, `NavbarActions`, `NavbarClose`, `NavbarToggle`, `Nav `, `NavItem`, `NavLink `components
- Allow `aria-*` as main props in `Button` and `ButtonLink` components
- Ignore empty string in `mainProps` twig function
- Introduce `Text` component as typography helper
- Introduce `Heading` component as typography helper
- Introduce `Link` component as typography helper
- Add `Alert` component
- Print raw `label` and `message` props in `TextField` and `CheckboxField` components

## 1.5.0 - 2022-04-04
- [BC] Use is prefix for boolean props
- Bugfix `Grid` component props
- Bugfix `Grid` component reset layout class if cols, tablet or desktop props defined
- Add onClick prop into `ButtonLink` component
- Add `title` prop into `ButtonLink`

## 1.4.0 - 2022-03-28
- Add support Twig 1.44.6 for Jobs

## 1.3.0 - 2022-03-22
- Add `ButtonLink`, `Container`, `Grid`, `Stack`, `TextField` and `CheckboxField` component
- Add Snapshot tests
- Update documentation
- Bugfix camelCase filename in compiler

## 1.2.0 - 2021-12-15
- Add prop `class` into components for customization
- Add base spirit component alias `spirit`
- Add tests extendable components cases
- Bugfix load bundle in project with multiple twig extension

## 1.1.0 - 2021-12-13
- Add Symfony 3 support for Jobs

## 1.0.0 - 2021-12-10
- [BC] Add possible link multiple components path into same alias in configuration
- [BC] Rename config param `path` into `paths`
- [BC] Rename config param `path_alias` into `paths_alias`
- [BC] Add configuration param `spirit_css_class_prefix` to define prefixes in class components
- Add Twig implementation of spirit component [Button](https://github.com/lmc-eu/spirit-design-system/tree/main/packages/web/src/components/Button) and [Tag](https://github.com/lmc-eu/spirit-design-system/tree/main/packages/web/src/components/Tag)
- configuration enabling html like-syntax into twig with config param `html_syntax_lexer`
- Add PHPStan into QA and refactoring
- Add `square` and `onClick` properties into Button component

## 0.1.0 - 2021-11-29
- Drop support php 7.3
- Add lowest deps into QA pipeline
- Add symfony polyfill php 8.0 functions
- Add simple components unit tests
- Upgrade LMC coding standards from v2 to v3

## 0.0.2 - 2021-11-24
- Add syntax example into README
- Fix connect yaml configuration into Twig

## 0.0.1 - 2021-11-24
- Initial release
