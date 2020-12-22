# composer update action

![update](https://github.com/kawax/composer-update-action/workflows/composer%20update/badge.svg)
![test](https://github.com/kawax/composer-update-action/workflows/test/badge.svg)
[![Maintainability](https://api.codeclimate.com/v1/badges/7a806f8e8f06017b9caf/maintainability)](https://codeclimate.com/github/kawax/composer-update-action/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/7a806f8e8f06017b9caf/test_coverage)](https://codeclimate.com/github/kawax/composer-update-action/test_coverage)

`composer update` and create pull request.

## Version
|ver|PHP|
|---|---|
|v1 |7.4|
|v2 |8.0|
|master|8.0|

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
        uses: actions/checkout@v2
      - name: composer update action
        uses: kawax/composer-update-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

## env
- COMPOSER_PATH : Specify if using subdirectory. Where composer.json is located.
- GIT_NAME : git user name
- GIT_EMAIL : git email

```yaml
      - name: composer update action
        uses: kawax/composer-update-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
          COMPOSER_PATH: /subdir
          GIT_NAME: cu
          GIT_EMAIL: cu@composer-update
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
