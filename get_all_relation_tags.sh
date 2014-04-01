#!/bin/bash
set +ue
set -x
./get_relation.php | grep -v "KEY:"| grep -v "STAT:" |sort | uniq > all_relation_tags.tags