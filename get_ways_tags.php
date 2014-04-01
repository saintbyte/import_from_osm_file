#!/usr/bin/php
<?
function get_part1($src,$begin,$end)
{
  $i = strpos($src,$begin);
  if ($i===false) return '';
  $i2 = strpos($src,$end,$i+strlen($begin)+1);
  if ($i2 === false) return '';
  $r = substr($src,$i+strlen($begin),$i2-($i+strlen($begin)));
  return $r;
}
function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}
$file_begin = 'RU-SVE';
$file_to_write = fopen($file_begin.'.wayindex','w');
$file = @fopen($file_begin.".osm", "r");
$arr = array();
$cnt=0;
$buffer = '';
$in_way = false;
$cur_way = 0;
$tag_stat = array();
while (!feof($file)) {
$line = fgets($file,2048);
$line = trim($line);
if (strpos($line,'<way') !== false )
{
 $in_way = true;
 $cnt++;
 $id = get_part1($line,'id="','"');
 $cur_way = $id;
 continue;
}
if (strpos($line,'</way>') !== false )
{
 $in_way = false;
 continue;
}
if ($in_way)
{
  if (strpos($line,'<way') !== false )
  { 
    // Node
  }
  if (strpos($line,'<tag') !== false )
  {
    print $line."\n";
    $key = get_part1($line,'k="','"');
    print "KEY:".$key."\n";
    $tag_stat[$key] = intval($tag_stat[$key]) + 1;
  }
}
/*
    $cnt++;
    $id = get_part1($line,'id="','"');
    $lat = get_part1($line,'lat="','"');
    $lon = get_part1($line,'lon="','"');
    //$arr[$id] = array($lat,$lon);
    $mem = memory_get_usage(true);
    $buffer .= $id.';'.$lat.';'.$lon."\n";
    if ( $cnt % 1000 == 0)
    {
	fwrite($file_to_write,$buffer);
        $buffer = '';
	print $line."\n";
	print 'ID:'.$id."\n";
	print 'LAT:'.$lat."\n";
	print 'LON:'.$lon."\n";
	print 'cnt:'.formatSizeUnits($cnt)."\n";
	print 'Mem:'.formatSizeUnits($mem)."\n";
	print "\n";
       /*
       cnt:304.69 KB
       Mem:153.75 MB
       * /

    }



}
*/
}
fclose($file);
fclose($file_to_write);
foreach ($tag_stat as $k => $v)
{
  print 'STAT:'.$k.'='.$v."\n";
}