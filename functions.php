<?php
function init()
{
  global $_config;
  global $file_to_write; 
  if ($_config['store_type'] == 'mysql')
  {
     mysql_connect($_config['mysql_host'],$_config['mysql_login'],$_config['mysql_password']);
     mysql_selectdb($_config['mysql_db']);
     mysql_query($_config['mysql_init_sql']);
  }
  if ($_config['store_type'] == 'file')
  {
    $file_to_write = fopen($file_begin.'.nidx','w');
  }
  if ($_config['store_type'] == 'stdout')
  {
    print 'BEGIN_TIME:'.date('r')."\n";
  }
}

function final_work()
{
  global $_config;
  global $file_to_write;
  if ($_config['store_type'] == 'mysql')
  {
     mysql_close();
  }
  if ($_config['store_type'] == 'file')
  {
    fclose($file_to_write);
  }
  if ($_config['store_type'] == 'stdout')
  {
    print 'END_TIME:'.date('r')."\n";
  }
}
function timestamp_show()
{
    print ''.date('r')."\n";
}

function all_clear()
{
   nodes_clear();
   ways_clear();
   tags_clear();
   relation_clear();
}

function tags_clear()
{
    global $_config;
    if ($_config['store_type'] == 'mysql')
    {
      $sql = 'TRUNCATE tags';
      mysql_query($sql);
      $sql = 'TRUNCATE tag_relation';
      mysql_query($sql);
      $sql = 'TRUNCATE tag_values';
      mysql_query($sql);
    }
}
function ways_clear()
{
    global $_config;
    if ($_config['store_type'] == 'mysql')
    {
        $sql = 'TRUNCATE ways';
        mysql_query($sql);
        $sql = 'TRUNCATE ways_nodes';
        mysql_query($sql);
    }
}
function nodes_clear()
{
    global $_config;
    if ($_config['store_type'] == 'mysql')
    {
        $sql = 'TRUNCATE nodes';
        mysql_query($sql);
    }
}
function relation_clear()
{
    global $_config;
    if ($_config['store_type'] == 'mysql')
    {
        $sql = 'TRUNCATE  relation';
        mysql_query($sql);
        $sql = 'TRUNCATE  relations_members';
        mysql_query($sql);

    }

}
function way_store($way_id,$nodes)
{
   global $_config;
   (string)$type = 'line';
   if ($nodes[0] == $nodes[count($nodes)-1])
   {
         $type='area';
   }
   $nodes = nodes_get($nodes);

   if ($_config['store_type'] == 'mysql')
   {
      (float)$maxlat =0;
      (float)$maxlon =0;
      (float)$minlat =190;
      (float)$minlon =190;
      foreach($nodes as $node_id => $node_data)
      {
         $sql = 'INSERT DELAYED INTO ways_nodes(way_id,node_id) VALUES('.$way_id.','.$node_id.')';
         mysql_query($sql);
         if (floatval($node_data['lat'])>$maxlat) $maxlat = floatval($node_data['lat']);
         if (floatval($node_data['lat'])<$minlat) $minlat = floatval($node_data['lat']); 
         if (floatval($node_data['lon'])>$maxlon) $maxlon = floatval($node_data['lon']);
         if (floatval($node_data['lon'])<$minlon) $minlon = floatval($node_data['lon']); 

      }
      $sql = 'INSERT DELAYED INTO ways(way_id,maxlat,maxlon,minlat,minlon,type)
                              VALUES('.$way_id.','.$maxlat.','.$maxlon.','.$minlat.','.$minlon.',"'.$type.'")';
      mysql_query($sql);
   }
}
function node_store($node_id,$node_lat,$node_lon)
{
  global $_config;
  if ($_config['store_type'] == 'mysql')
  {
    $sql = 'INSERT DELAYED INTO nodes (node_id,lat,lon) VALUES('.$node_id.','.$node_lat.','.$node_lon.')';
    mysql_query($sql);
  }
  if ($_config['store_type'] == 'file')
  {
    $str = $node_id.';'.$node_lat.';'.$node_lon."\n";
  }
  if ($_config['store_type'] == 'stdout')
  {
    $str = $node_id.';'.$node_lat.';'.$node_lon."\n";
    print $str;
  }
}
function relations_store($relation_id,$members)
{
  global $_config;
  if ($_config['store_type'] == 'mysql')
  {
     foreach($members as $member)
     {
        $sql = 'INSERT DELAYED  INTO relations_members(own_relation_id,node_id,way_id,relation_id,role)
            VALUES('.$relation_id.','.$member['node_id'].','.$member['way_id'].',
                    '.$member['relation_id'].',"'.$member['role'].'")';
        mysql_query($sql);
     }
     $sql = 'INSERT DELAYED INTO relations(relation_id) VALUES('.$relation_id.');';
     mysql_query($sql);
  }
}
function nodes_get($nodes_ids)
{
  global $_config;
  if (!is_array($nodes_ids))
  {
    $nodes_ids = array($nodes_ids);
  } 
  if ($_config['store_type'] == 'mysql')
  {
     $result = array();
     $sql = 'SELECT * FROM nodes WHERE node_id IN ( '.join(',',$nodes_ids).' )';
     $qh = mysql_query($sql);
     while ($row = mysql_fetch_array($qh, MYSQL_ASSOC)) 
     {
        $result[$row['node_id']] = $row;
     }
     return $result;
  }
}
function tags_store($tagname)
{
  global $_config;
  $tagname = strtolower($tagname);
  if ($_config['store_type'] == 'mysql')
  {  
      $tagname = mysql_escape_string($tagname);
      $sql = 'SELECT id FROM tags WHERE name="'.$tagname.'"';
      $qh = mysql_query($sql);
      if (mysql_num_rows($qh) > 0)
      {
         $arr = mysql_fetch_array($qh);
         return intval($arr['id']);
      }
      $sql = 'INSERT INTO tags(name) VALUES("'.$tagname.'")';
      mysql_query($sql);
      return mysql_insert_id();
  }
}

function tag_value_store($value)
{
  global $_config;
  if ($_config['store_type'] == 'mysql')
  {
      $value = mysql_escape_string($value);
      $sql = 'SELECT id FROM tag_values WHERE value="'.$value.'"';
      $qh = mysql_query($sql);
      if (mysql_num_rows($qh) > 0)
      {
         $arr = mysql_fetch_array($qh);
         return intval($arr['id']);
      }
      $sql = 'INSERT INTO tag_values(value) VALUES("'.$value.'")';
      mysql_query($sql);
      return mysql_insert_id();

  }
}

function tag_for_node_store($node_id,$k,$v)
{
  global $_config;
  if ($_config['store_type'] == 'mysql')
  {
     $k_id = tags_store($k);
     $v_id = tag_value_store($v);
     $sql = 'INSERT INTO tag_relation (node_id,tag_id,tag_value_id) VALUES('.$node_id.','.$k_id.', '.$v_id.')';
     mysql_query($sql);
  }
}

function tag_for_relation_store($relation_id,$k,$v)
{
  global $_config;
  if ($_config['store_type'] == 'mysql')
  {
     $k_id = tags_store($k);
     $v_id = tag_value_store($v);
     $sql = 'INSERT INTO tag_relation (relation_id,tag_id,tag_value_id) VALUES('.$relation_id.','.$k_id.', '.$v_id.')';
     mysql_query($sql);
  }
}

function tag_for_way_store($way_id,$k,$v)
{
  global $_config;
  if ($_config['store_type'] == 'mysql')
  {
     $k_id = tags_store($k);
     $v_id = tag_value_store($v);
     $sql = 'INSERT INTO tag_relation (way_id,tag_id,tag_value_id) VALUES('.$way_id.','.$k_id.', '.$v_id.')';
     mysql_query($sql);
  }
}


function mysql_index_field($table,$field)
{
   $sql = 'ALTER TABLE '.$table.'  ADD INDEX ('.$field.')';
   mysql_query($sql);
}

function get_part1($src,$begin,$end)
{
  $i = strpos($src,$begin);
  if ($i===false) return '';
  $i2 = strpos($src,$end,$i+strlen($begin)+1);
  if ($i2 === false) return '';
  $r = substr($src,$i+strlen($begin),$i2-($i+strlen($begin)));
  return $r;
}
