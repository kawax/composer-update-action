# composer update action

![update](https://github.com/kawax/composer-update-action/workflows/composer%20update/badge.svg)
![test](https://github.com/kawax/composer-update-action/workflows/test/badge.svg)
[![Maintainability](https://api.codeclimate.com/v1/badges/7a806f8e8f06017b9caf/maintainability)](https://codeclimate.com/github/kawax/composer-update-action/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/7a806f8e8f06017b9caf/test_coverage)](https://codeclimate.com/github/kawax/composer-update-action/test_coverage)

`composer update` and create pull request.

## Reusable workflow version
https://github.com/kawax/composer-workflow

## Version
| ver    | PHP    |
|--------|--------|
| v1     | 7.4    |
| v2     | 8.0    |
| v3     | 8.1    |
| v4     | 8.2    |
| master | latest |

## Usage

Create `.github/workflows/update.yml`

```yaml
name: composer update

on:
  schedule:
    - cron: '0 0 * * *' #UTC

jobs:
  composer_update_job:
    runs-on: ubuntu-latest
    name: composer update
    steps:
      - name: Checkout
        uses: actions/checkout@v3
      - name: composer update action
        uses: kawax/composer-update-action@master
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

## env
- COMPOSER_PATH : Specify if using subdirectory. Where composer.json is located.
- GIT_NAME : git user name
- GIT_EMAIL : git email
- APP_SINGLE_BRANCH : If set, the new functionality is enabled.
- APP_SINGLE_BRANCH_POSTFIX : A postfix for the branch used for updates. Default value is "-updated". If the branch doesn't exist, a new branch will be created with the parent branch name plus the postfix, e.g. "master-updated".
- GIT_COMMIT_PREFIX : Add a prefix to the commit message and pull request title. E.g. "[UPDATE] "
- COMPOSER_PACKAGES : Specify which packages should be updated. E.g. "typo3/cms-*". Setting this variable will also run Composer with the `--with-dependencies` argument.

```yaml
      - name: composer update action
        uses: kawax/composer-update-action@master
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
          COMPOSER_PATH: /subdir
          GIT_NAME: cu
          GIT_EMAIL: cu@composer-update
          APP_SINGLE_BRANCH: 1
          APP_SINGLE_BRANCH_POSTFIX: -updated
          GIT_COMMIT_PREFIX: '[UPDATE] '
          COMPOSER_PACKAGES: 'typo3/cms-*'
```

## Troubleshooting

### Missing PHP extension

```
foo/bar 1.0.0 requires ext-XXX * -> the requested PHP extension XXX is missing from your system.
```

Configure `platform` in your composer.json.

```json
  "config": {
    "platform": {
      "php": "7.2.0", 
      "ext-XXX": "1.0.0"
     }
  },
```

## LICENCE
MIT
