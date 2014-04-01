#!/bin/bash
set +ue
set -x
./get_ways_tags.php | grep "KEY:"| awk -F ":" '{ print $2 }' | sort | uniq  -c | sort -n > all_ways_tags_keys_stat.tags