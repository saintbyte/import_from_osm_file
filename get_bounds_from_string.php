#!/usr/bin/php
<?php
require('./config.php');
require('./functions.php');
$in_format = 'string';
$type = $_SERVER['argv'][1];
$line = $_SERVER['argv'][2];
#<bounds minlon="57.03902" minlat="55.87235" maxlon="66.38458" maxlat="62.17319" origin="http://www.openstreetmap.org/api/0.6"/>
if (strpos($line,"<") !== false)
{
   $in_format = 'from_osm_file';
}
switch($in_format)
{
 case 'from_osm_file':
                      $minlon=get_part1($line,'minlon="','"');
                      $minlat=get_part1($line,'minlat="','"');
                      $maxlon=get_part1($line,'maxlon="','"');
                      $maxlat=get_part1($line,'maxlat="','"');
                      break;
 case 'string': 
         list($minlon,$minlat,$maxlon,$maxlat) = explode(',',$line);
         break;
}
print $in_format."\n";
print $type."\n";
print $line."\n";
print "minlon=".$minlon."\n";
print "minlat=".$minlat."\n";
print "maxlon=".$maxlon."\n";
print "maxlat=".$maxlat."\n";
print "maxlat=".$maxlat."\n";
print "bounds=".$minlat.','.$minlon.','.$maxlat.','.$maxlon."\n";

putenv("minlon=".$minlon);
putenv("minlat=".$minlat);
putenv("maxlon=".$maxlon);
putenv("maxlat=".$maxlat);
