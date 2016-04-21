<?php
# Code Editor
# VERSION: @@version
# BUILT ON: @@buildDate
# CONFIGURATION:
# The following global var options are optional and can be moved to an external config file editor.config.php.
#$PASSWORD=md5(''); # Uncomment this line to allow login without a password
$DIRS_AT_TOP=TRUE;
$SHELL_PRE='';
$BRANDING_HEADER='';
$BRANDING_FOOTER='';
$DEFAULT_DIR='';
?>
<?php
if(file_exists('editor.config.php'))require('editor.config.php');
if(!isset($ALLOW_SHELL))$ALLOW_SHELL=TRUE;
if(!isset($DIRS_AT_TOP))$DIRS_AT_TOP=TRUE;
if(!isset($SHELL_PRE))$SHELL_PRE='';
$UNIX=isset($_SERVER["OS"])===false || strpos($_SERVER["OS"],'Windows')===FALSE;
$WINDOWS=!$UNIX;
if(!isset($PASSWORD)){
	require('set-password.php');
	exit;
}
if($PASSWORD!=md5('')&&(!isset($_COOKIE['editor-auth'])||md5($_COOKIE['editor-auth'])!=$PASSWORD)){
	require('login.html');
	exit;
}
if(isset($_GET["u"])){ # Experimental feature
	header('Content-Type: text/plain');
	passthru('wget https://raw.githubusercontent.com/simon-thorpe/editor/master/dist/editor.php -O '.escapeshellarg($_SERVER["SCRIPT_FILENAME"]).' 2>&1');
	exit;
}
// @@css
// @@js
require('util.php');
$PATH=isset($_REQUEST["p"])?$_REQUEST["p"]:'';
if($PATH==='' && $_SERVER['REQUEST_METHOD']==='GET'){if(!$DEFAULT_DIR)$DEFAULT_DIR=realpath('.');header('Location: ?p='.urlencodelite($DEFAULT_DIR));exit;}

header('Cache-Control: no-store'); // To disable caching on browser back button.

if($ALLOW_SHELL && isset($_POST["ajaxShell"])){
	$COMMAND=$_POST["ajaxShell"];
	$TEMP_DIR_WITH_TRAILING_SLASH='/tmp/';
	if(isset($_SERVER['TEMP'])){
		$TEMP_DIR_WITH_TRAILING_SLASH=$_SERVER['TEMP'];
		if($WINDOWS)$TEMP_DIR_WITH_TRAILING_SLASH.='\\';
		else $TEMP_DIR_WITH_TRAILING_SLASH.='/';
	}
	$STDOUT_FILE=$TEMP_DIR_WITH_TRAILING_SLASH.'editor.php.STDOUT';
	$RESULT_FILE=$TEMP_DIR_WITH_TRAILING_SLASH.'editor.php.RESULT';
	$LASTCMD_FILE=$TEMP_DIR_WITH_TRAILING_SLASH.'editor.php.LASTCMD';
	$LOCK_FILE=$TEMP_DIR_WITH_TRAILING_SLASH.'editor.php.LOCK';
	$tempExecuteFile=$TEMP_DIR_WITH_TRAILING_SLASH.'editor.php.'.($WINDOWS?'cmd':'sh');
	$WD=$PATH;
	if($WINDOWS)$WD=str_replace('/','\\',$WD);
	$lastCmd=null;
	if(file_exists($LASTCMD_FILE))$lastCmd=file_get_contents($LASTCMD_FILE);
	header('Content-Type: application/json');
	
	if(file_exists($LOCK_FILE)){
		$output=htmlentities(file_get_contents($STDOUT_FILE),ENT_SUBSTITUTE);
		echo json_encode(array("continue"=>TRUE,"output"=>$output,"lastCmd"=>$lastCmd,"debugLockFile"=>$LOCK_FILE));
	}
	elseif(file_exists($STDOUT_FILE)){
		$output=htmlentities(file_get_contents($STDOUT_FILE),ENT_SUBSTITUTE);
		$result=file_get_contents($RESULT_FILE);
		unlink($STDOUT_FILE);
		unlink($RESULT_FILE);
		echo json_encode(array("continue"=>FALSE,"output"=>$output,"lastCmd"=>$lastCmd,"result"=>$result));
	}
	elseif($COMMAND){
		$output=null;
		file_put_contents($LOCK_FILE,'');
		file_put_contents($LASTCMD_FILE,$COMMAND);
		if($WINDOWS){ // No async for windows yet
			file_put_contents($tempExecuteFile,"cd \"".$WD."\" || exit 1\n".$SHELL_PRE."\n".$COMMAND." >>".$STDOUT_FILE."\ndel ".$LASTCMD_FILE."\ndel ".$LOCK_FILE."\ndel ".$tempExecuteFile);
			//shell_exec('START /B CMD /C CALL \"'+$tempExecuteFile.'\"');
			shell_exec($tempExecuteFile);
			//usleep(100000); // So we can see some output on the first round.
			$output=htmlentities(file_get_contents($STDOUT_FILE),ENT_SUBSTITUTE);
		}
		else{
			file_put_contents($tempExecuteFile,"#!/bin/bash\ncd ".escapeshellarg($WD)." || exit 1\n".$SHELL_PRE."\n{ ".$COMMAND."; } 2>&1\nRESULT=$?\nrm ".$LASTCMD_FILE."\nrm ".$LOCK_FILE."\nrm ".$tempExecuteFile."\n[[ \$RESULT == 0 ]] && printf success >$RESULT_FILE || printf failure >$RESULT_FILE");
			shell_exec('/bin/bash '.$tempExecuteFile.' >>'.$STDOUT_FILE.' &');
			//usleep(100000); // So we can see some output on the first round.
			$output=htmlentities(file_get_contents($STDOUT_FILE),ENT_SUBSTITUTE);
		}
		echo json_encode(array("first"=>TRUE,"continue"=>TRUE,"output"=>$output
		,"debug_tempExecuteFile"=>$tempExecuteFile,"debug_command"=>$COMMAND,"debug_stdout"=>$STDOUT_FILE
		));
	}
	else{
		// Nothing to do.
		echo json_encode(array("idle"=>TRUE));
	}
	exit();
}

require('commands.php');

$Recursive=FALSE;
if(isset($_REQUEST["r"]))$Recursive=TRUE;
$Grep="";
if(isset($_REQUEST["grep"]))$Grep=$_REQUEST["grep"];
$Find="";
if(isset($_REQUEST["find"]))$Find=$_REQUEST["find"];
$Locate="";
if(isset($_REQUEST["locate"]))$Locate=$_REQUEST["locate"];
$Title=substr($PATH,strrpos(str_replace('\\','/',$PATH),'/')+1);
if($Title=='')$Title='Code Editor';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<link rel="shortcut icon" href="//cdnjs.cloudflare.com/ajax/libs/fatcow-icons/20130425/FatCow_Icons32x32/file_manager.png">
	<title><?php echo $Title;?></title>
	<link rel="stylesheet" href="editor.css">
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
	<script src="//cdn.jsdelivr.net/ace/1.2.3/min/ace.js"></script>
	<script src="//cdn.jsdelivr.net/garlic.js/1.2.2/garlic.min.js"></script>
	<script src="//cdn.jsdelivr.net/dropzone/4.3.0/dropzone.min.js"></script>
</head>
<body>
<header class="list">
	<?php if(isset($BRANDING_HEADER))echo $BRANDING_HEADER; ?>
	<nav>
		<a href="javascript:void(0)" class="newButton">new</a>
		<a href="javascript:void(0)" class="searchButton">search</a>
		<a href="javascript:void(0)" class="uploadButton">upload</a>
		<a href="?p=<?php echo urlencodelite($PATH);?><?php echo $Recursive?'':'&r=';?>" class="<?php echo $Recursive?'active':'';?>">tree</a>
		<?php if($ALLOW_SHELL){ ?><a href="javascript:void(0)" class="shellButton <?php if(isset($_POST['shell']))echo 'active';?>">shell</a><?php } ?>
		<datalist id=shellHistory></datalist>
	</nav>
	<form class="searchForm" data-persist=garlic data-destroy=false>
		<div class="fieldRow">
			Search: <input type=text style=width:100%><br>
		</div>
		<input type=checkbox checked><label>Recursive</label>
		<select><option <?php if($Locate)echo 'selected';?>>Locate Database</option><option>All</option><option>Filenames</option><option>Content (All Files)</option><option>Content (Code Only)</option></select>
		<button onclick="return(false)">Search</button>
	</form>
	<form class="shellForm" data-persist=garlic data-destroy=false method=post <?php if(isset($_POST['shell']))echo 'style=display:block';?>>
		<?php if(isset($_POST['shell'])){ ?>
			<?php if($WINDOWS){ ?>
				<pre id=shellOutput><?php system('cd '.escapeshellarg($PATH)." && ".$_POST['shell'],$errorCode); ?></pre>
			<?php }else{ ?>
				<pre id=shellOutput><?php system('cd '.escapeshellarg($PATH)." || exit 1;\n".$_POST['shell'].' 2>&1',$errorCode); ?></pre>
			<?php } ?>
			<script><?php if($errorCode===0){ ?>$('.shellForm').css({backgroundColor:'#6f6'});<?php }else{ ?>$('.shellForm').css({backgroundColor:'#f44'});<?php } ?></script>
		<?php }else{ ?>
		<pre id=shellOutput><?php require('shell-motd.txt'); ?></pre>
		<?php } ?>
		<input type="text" placeholder="Shell command" style="width:100%;" list=shellHistory name=shell <?php if(isset($_POST['shell']))echo 'autofocus';?>>
		<div style=margin-top:6px><input type=checkbox id=shellFormBackground><label for=shellFormBackground>Background</label></div>
		<button style="height:0;opacity:0;" type=submit>Run</button>
	</form>
</header>
<?php
	if (is_dir($PATH))
		echo renderFileList();
	elseif(file_exists($PATH))
		echo renderEditor();
	else
		die('Path does not exist: '.$PATH);
	
	function renderEditor()
	{
		global $PATH;
		$html="<header class=editor><nav>";
		$html.="<button onclick=editor.save(false);return(false);>Save</button>";
		$html.="<button onclick=editor.save(true);return(false);>Save &amp; Close</button>";
		$html.="</nav></header>";
		$c=file_get_contents($PATH);
		if(substr($c,0,3)==pack("CCC",0xef,0xbb,0xbf)) // Remove BOM
			$c=substr($c,3);
		$html.="<div id=editor ".(is_writable($PATH)?"":"data-readonly").">" . htmlentities($c,ENT_SUBSTITUTE) . "</div>";
		return $html;
	}
	function getFiles(){
		global $PATH,$Recursive,$Locate,$DIRS_AT_TOP;
		if($Locate)
			$r=getFilesUsingLocate($PATH,$Locate);
		elseif($Recursive)
			$r=scandir_recursive($PATH);
		else
			$r=scandir($PATH);
		sort($r,SORT_STRING|SORT_FLAG_CASE);
		
		if($DIRS_AT_TOP && !$Locate && !$Recursive){
			$files=array();
			$dirs=array();
			foreach($r as $f)
			{
				if(is_dir($PATH.'/'.$f))
					array_push($dirs,$f);
				else
					array_push($files,$f);
			}
			$r=array_merge($dirs,$files);
		}
		return $r;
	}
	function renderFileList()
	{
		global $PATH,$Grep,$Find,$Recursive;
		$editorPPath=realpath($_SERVER["DOCUMENT_ROOT"]);
		$files=getFiles();
		$html="";
		$html.="<div id=list>";
		if ($PATH != ""){
			$PathEscaped=str_replace("'","\\'",$PATH);
			$delOnclick="onclick=\""."if(editor.del('$PathEscaped',null)===true){window.location=$('#list>.dir:first a:first').attr('href');}return(false)\"";
			if(get_dir_count($PATH)>0&&!is_link($PATH)) # Hide del button if dir not empty and not a symlink.
				$delOnclick='';
			$up=substr($PATH,0, strrpos($PATH,'/'));
			if($up==='')
				$up='/';
			elseif($Recursive)
				$up=$PATH; # Back out of search/recursive mode instead of up a level.
			$html.="<div class=dir><a class=seg href=\"?p=" . urlencodelite($up) . "\">..</a><a href=\"javascript:editor.cut('$PathEscaped')\" class=cut></a><a href=\"javascript:editor.copy('$PathEscaped')\" class=copy></a><a href=# $delOnclick class=del></a></div>";
		}
		foreach($files as $filePath)
		{
			$rFile=$filePath;
			$aFile=($PATH=='/'?'':$PATH).'/'.$rFile;
			$pFile=realpath($aFile);
			if($rFile=='.'||$rFile=='..')
				continue;
			$isDir=is_dir($aFile);
			
			
			if($Find){
				if(preg_match('/'.$Find.'/i',$rFile)!==1)
					continue;
			}
			if($Grep!=''){
				if($isDir)continue;
				$fileContents=file_get_contents($aFile,false,null,0,5242880); // Limit to 5MB
				if(strpos(strtolower($fileContents),strtolower($Grep))===FALSE)
					continue;
			}
				
			if($isDir){
				$size=get_dir_count($aFile);
				$friendlySize=$size;
			}
			else{
				$size=filesize($aFile);
				$friendlySize=human_readable_filesize(filesize($aFile));
			}
			$age=human_readable_timespan(time()-filemtime($aFile));
			$direct='';
			if(strpos($pFile,$editorPPath)===0){
				$direct=urlencodelite(str_replace('\\','/',substr($pFile,strlen($editorPPath))));
				if($direct=='')$direct='/';
			}
			$aFileEscaped=str_replace("'","\\'",$aFile);
			if($isDir)
				$dlAnchor='';
			else
				$dlAnchor='<a class=dl href="?d=&p='.$aFileEscaped.'"></a>';
			$delOnclick="onclick=\"editor.del('$aFileEscaped',this);return(false)\"";
			if($isDir&&$size!==0&&!is_link($aFile))$delOnclick=''; # Hide del button if dir not empty and not a symlink.
			
			$segAs='';
			
			// Anchor each path segment.
			$segs=explode('/',$rFile);
			$segsCount=count($segs);
			$segAppend='';
			for($i=0;$i<$segsCount;$i++){
				$segAppend.='/'.$segs[$i];
				if($i===$segsCount-1 && ($size>=1048576 || preg_match('/\.(mp3|aac|ogg|wav|mid|jpg|bmp|gif|png|webp|webm|mp4|mkv|m4v|avi|pdf|zip|rar|tar|gz|7z)$/i',$rFile)===1))
					// Don't allow editing if file is over 1MB or is a media type.
					$href=$direct;
				else
					$href='?p='.urlencodelite(($PATH==='/'?'':$PATH).$segAppend);
				$segIsDir=$i!==$segsCount-1;
				$segAs.='<span class=slash> / </span><a class="seg '.($segIsDir?'d':'').'" href="'.$href.'">'.$segs[$i].'</a>';
			}
			$segAs=substr($segAs,28); // Trim leading " <span class=slash>/</span> "
			
			$pasteAnchor='';
			if($isDir)
				$pasteAnchor="<a href=\"javascript:editor.paste(null,'$aFileEscaped')\" class=paste></a>";
				
			$html.='<div class="'.($isDir?'dir':'file').($size===FALSE?' bad':'').'"><div class=filepath>'.$segAs."</div><span class=size>$friendlySize</span><span class=age>$age</span><a href=\"$direct\" class=direct></a>$dlAnchor$pasteAnchor<a href=\"javascript:editor.cut('$aFileEscaped')\" class=cut></a><a href=\"javascript:editor.copy('$aFileEscaped')\" class=copy></a><a href=# class=del $delOnclick></a></div>";
		}
		$html.="</div>";
		return $html;
	}
	if(isset($BRANDING_FOOTER))echo $BRANDING_FOOTER;
?>
<script src="editor.js"></script>
<?php if(isset($_POST['shell']))echo '<script>$(function(){$(".shellButton").triggerHandler("click");});</script>'; // If was postback then shell form is already open so need to trigger click to init form events ?>
</body>
</html>
