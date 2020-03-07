# composer update action

`composer update` and create pull request.

## Usage

Create `.github/workflows/update.yml`

```yaml
on:
  schedule:
    - cron:  '0 0 * * *' #UTC

jobs:
  composer_update_job:
    runs-on: ubuntu-latest
    name: composer update
    steps:
      - name: Checkout
        uses: actions/checkout@v2
      - name: composer update action step
        uses: kawax/composer-update-action@master
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

## env
- COMPOSER_PATH : Specify if using subdirectory. Where composer.json is located.
- GIT_NAME : git user name
- GIT_EMAIL : git email

```yaml
      - name: composer update action step
        uses: kawax/composer-update-action@master
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
          COMPOSER_PATH: /subdir
          GIT_NAME: cu
          GIT_EMAIL: cu@composer-update
```

## LICENCE
MIT
