#!/bin/bash

echo 'Running GRUNT'

grunt

echo 'Running uglifyjs'

uglifyjs public/scripts.js -o public/scripts.js