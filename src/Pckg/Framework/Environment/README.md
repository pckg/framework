# Environments

_Helpers: env() isWin() isUnix()_

## Production

_Helpers: prod()_

Production environment `./www/index.php` is default unprotected/public entry point for your app. Framework automatically
takes care of disabling error display, optionally register rollbar exception manager and register error output handler.

## Development

_Helpers: dev() implicitDev()_

Development environment `./www/dev.php` is only accessable from IPs listed in `pckg.framework.dev` config. All errors
are shown by default, whoops exception handler is registered with `PrettyPageHandler` for quicker and easier debugging
and `debugbar` is shown on frontend and backend. Development environment includes basically everything for developer
needs.

## Console

_Helpers: console()_

Console handler `./console` is only accessable from bash. Errors are also shown by default and `whoops` exception
handler is registered with `PlainTextHandler` handler.
