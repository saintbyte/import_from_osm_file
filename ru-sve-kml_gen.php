#!/usr/bin/php
<?php
require('./config.php');
require('./functions.php');
init();
$sql = 'SELECT  relations_members.* , nodes.* FROM relations_members LEFT JOIN ways_nodes ON relations_members.way_id = ways_nodes.way_id LEFT JOIN nodes ON ways_nodes.node_id = nodes.node_id WHERE  relations_members.own_relation_id=79379  AND relations_members.way_id != 0;';
$qh = mysql_query($sql);
$kml_template = '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>'."\r\n";
$kml_template .= '<kml xmlns="http://www.opengis.net/kml/2.2">'."\r\n";
$kml_template .= '<Document>'."\r\n";
$kml_template .= '<name>Test</name>'."\r\n";
$kml_template .= '<description>Test</description>'."\r\n";
$kml_template .= '<Style id="yellowLineGreenPoly">'."\r\n";
$kml_template .= '<LineStyle>'."\r\n";
$kml_template .= '<color>7f00ffff</color>'."\r\n";
$kml_template .= '<width>4</width>'."\r\n";
$kml_template .= '</LineStyle>'."\r\n";
$kml_template .= '<PolyStyle>'."\r\n";
$kml_template .= '<color>7f00ff00</color>'."\r\n";
$kml_template .= '</PolyStyle>'."\r\n";
$kml_template .= '</Style>'."\r\n";
$kml_template .= '<Placemark>'."\r\n";
$kml_template .= '<styleUrl>#yellowLineGreenPoly</styleUrl>'."\r\n";
$kml_template .= '<LineString>'."\r\n";
$kml_template .= '<extrude>1</extrude>'."\r\n";
$kml_template .= '<tessellate>1</tessellate>'."\r\n";
$kml_template .= '<altitudeMode>absolute</altitudeMode>'."\r\n";
$kml_template .= '<coordinates>'."\r\n";
$alt = '2000';
while ($row = mysql_fetch_array($qh, MYSQL_ASSOC)) 
{
$kml_template .= $row['lon'].','.$row['latM@M@'].','.$alt."\r\n";
}
$kml_template .= '</coordinates>'."\r\n";
$kml_template .= '</LineString>'."\r\n";
$kml_template .= '</Placemark>'."\r\n";
$kml_template .= '</Document>'."\r\n";
$kml_template .= '</kml>'."\r\n";
file_put_contents('1.kml',$kml_template);
?>