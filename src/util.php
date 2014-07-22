<?php
function getFilesUsingLocate($dir,$pattern){
	$output=array();
	$r=array();
	exec('/usr/bin/mlocate -ir '.escapeshellarg($dir.'.*'.$pattern),$output);
	foreach($output as &$fullPath){
		$relativePath='';
		if(strlen($dir)>0 && $dir!='/')$relativePath=substr($fullPath,strlen($dir)+1);
		else $relativePath=$fullPath;
		
		// Remove trailing '/' as output should be relative to $dir.
		if($relativePath[0]=='/')
		$relativePath=substr($relativePath,1);
		
		array_push($r,$relativePath);
	}
	return $r;
}
function scandir_recursive($dir,$includeDirs=TRUE,$_prefix=''){
		$dir=rtrim($dir,'\\/');
		$r=array();
		foreach(scandir($dir)as$f){
			if($f!=='.'&&$f!=='..')
				if(is_dir("$dir/$f")){
					$r=array_merge($r,scandir_recursive("$dir/$f",$includeDirs,"$_prefix$f/"));
					if($includeDirs)$r[]=$_prefix.$f;
				}else $r[]=$_prefix.$f;
		}
		return $r;
}
function human_readable_filesize($v)
{
	if($v>=1024*1024*1024)
		return (floor($v/(1024*1024*1024)*10)/10)." GB";
	elseif($v>=1024*1024)
		return (floor($v/(1024*1024)*10)/10)." MB";
	elseif($v>=1024)
		return (floor($v/1024*10)/10)." KB";
	else
		return $v." bytes";
}
function human_readable_timespan($v)
{
	if($v>=60*60*24)
		return (floor($v/(60*60*24)*10)/10)." days";
	elseif($v>=60*60)
		return (floor($v/(60*60)*10)/10)." hours";
	elseif($v>=60)
		return floor($v/60)." min";
	else
		return $v." sec";
}
function get_dir_count($dir,$du=false){
	if(is_readable($dir)){
		if($du==TRUE){ // Recursively calculate the size of the directory's descendant items.
			$output=shell_exec('/usr/bin/find '.escapeshellarg($dir).' |wc -l');
			return intval($output)-1;
		}
		else{
			// Cound the items within the directory.
			return count(scandir($dir))-2; // -2 to exclude the "." and ".."
		}
	}
	else
		return null;
}
function urlencodelite($s){
	$s=rawurlencode($s);
	$s=str_replace('%2F','/',$s);
	$s=str_replace(' ','%20',$s);
	return $s;
}
?>