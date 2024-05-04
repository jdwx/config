#!/bin/sh
export PHAN_DISABLE_XDEBUG_WARN=1
time php ${HOME}/bin/phan >phan.txt
wc -l phan.txt
