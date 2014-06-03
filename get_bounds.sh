#!/bin/bash
set +ue
set -x
LINE=`head -n100 $1 | grep "<bounds"`
./get_bounds_from_string.php tovalue "${LINE}"
