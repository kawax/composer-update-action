# composer update action

![update](https://github.com/kawax/composer-update-action/workflows/composer%20update/badge.svg)
![test](https://github.com/kawax/composer-update-action/workflows/test/badge.svg)

`composer update` and create pull request.

## Version
|ver|PHP|
|---|---|
|v1 |7.4|
|master|latest|

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
      - name: composer update action step
        uses: kawax/composer-update-action@v1
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

## env
- COMPOSER_PATH : Specify if using subdirectory. Where composer.json is located.
- GIT_NAME : git user name
- GIT_EMAIL : git email

```yaml
      - name: composer update action step
        uses: kawax/composer-update-action@v1
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
