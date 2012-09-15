#!/bin/sh
# Travis doesn't have the ldap extension
sed -i'.tmp' '/ext-ldap/d' composer.json

# Adapt dependencies to the matrix build
sed -i'.tmp' 's!"symfony/\(.*\)": "[<>=0-9\.\*,]*"!"symfony/\1": "'$SYMFONY_VERSION'"!' composer.json
