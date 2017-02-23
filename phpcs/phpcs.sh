#!/bin/bash
composer install -o --prefer-dist
./vendor/bin/phpcs --extensions=php --ignore=vendor,phpcs --report-full -p --standard=phpcs/meteocontrol/ruleset.xml .
