#!/bin/bash
set +ue
set -x
./get_ways_tags.php | grep -v "KEY:"| sort | uniq > all_ways_tags.tags