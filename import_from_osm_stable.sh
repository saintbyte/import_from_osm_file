#!/bin/bash
set +ue
set -x
nohup ./import_from_osm.php > import_from_osm.php.output
tail -f  import_from_osm.php.output
