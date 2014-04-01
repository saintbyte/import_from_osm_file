#!/usr/bin/php
<?php
require('./config.php');
require('./functions.php');
init();
all_clear();
$file = @fopen($_config['osm_file'], "r");
$cnt=0;
$in_node = false;
$in_way  = false;
$in_relation = false;
$node_id = 0;
$way_id = 0;
$relation_id = 0;
$nodes_in_way = array();
$member_in_relation = array();
while (!feof($file)) 
{
    $cnt++;
    $line = fgets($file,2048);
    $line = trim($line);
    if (strpos($line,'<node') !== false )
    {
	$in_node = true;
        $node_id  = get_part1($line,'id="','"');
        $lat = get_part1($line,'lat="','"');
        $lon = get_part1($line,'lon="','"');
        node_store($node_id,$lat,$lon);
        if (strpos($line,'/>') !== false )
        {
	    $in_node = false;
        }
    }

    if (strpos($line,'</node>') !== false )
    {
	$in_node = false;
    }
    if ($in_node)
    {  
       if (strpos($line,'<tag') !== false )
       {
           $k = get_part1($line,'k="','"');
           $v = get_part1($line,'v="','"');
           tag_for_node_store($node_id,$k,$v);
       }
       else
       {
          // Неожиданно у ноде есть что-то кроме тагов
          //print 'NOT_TAG:'.$line."\n";
       }
    }
    //--------------------------------------------
    if (strpos($line,'<way') !== false )
    {
      $way_id  = get_part1($line,'id="','"');
      $in_way = true;
    }
    if (strpos($line,'</way>') !== false )
    {
      way_store($way_id,$nodes_in_way);
      $nodes_in_way = array();
      $in_way = false;
    }
    //--------------------------------------------
    if (strpos($line,'<relation') !== false )
    {
      $own_relation_id  = get_part1($line,'id="','"');
      $in_relation = true;
    }
    if (strpos($line,'</relation>') !== false )
    {
      relations_store($own_relation_id,$member_in_relation);
      $member_in_relation = array();
      $in_relation = false;
    }
    //--------------------------------------------
    if ($in_way)
    {
       if (strpos($line,'<tag') !== false )
       {
           $k = get_part1($line,'k="','"');
           $v = get_part1($line,'v="','"');
           tag_for_way_store($way_id,$k,$v);
       }
       if (strpos($line,'<nd') !== false )
       {
          $ref = get_part1($line,'ref="','"');
          $nodes_in_way[] = $ref;
       }
    }
    //--------------------------------------------
    if ($in_relation)
    {
       if (strpos($line,'<tag') !== false )
       {
           $k = get_part1($line,'k="','"');
           $v = get_part1($line,'v="','"');
           tag_for_relation_store($relation_id,$k,$v);
       }
       if (strpos($line,'<member') !== false)
       {
           $ref  = get_part1($line,   'ref="','"');
           $type = get_part1($line,   'type="','"');
           $role = get_part1($line,   'role="','"');
           $way_id = 0;
           $node_id = 0;
           $relation_id = 0;
           
           if ($type=='node') $node_id=$ref;
           if ($type=='way') $way_id=$ref;
           if ($type=='relation') $relation_id=$ref;
           $member_in_relation[]=array(
                                      'type'=>$type,
                                      'node_id'=>$node_id,
                                      'way_id' => $way_id,
                                      'relation_id'=>$relation_id,
                                      'role'=>$role
                                      );
       }
    }
    //---------------------------------------------
    if ( $cnt % 10000 == 0)
    {
          print "\n";
          print $cnt.' ';
          timestamp_show();
    }
}
print $cnt;
timestamp_show();
final_work();
timestamp_show();
