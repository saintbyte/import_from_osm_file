#!/bin/bash
set +ue
set -x
./get_relation.php | grep  "KEY:"| awk -F ":" '{ print $2 }' | sort | uniq -c | sort -n > all_relation_tags_keys.tags