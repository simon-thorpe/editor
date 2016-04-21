<?php
# Code Editor
# VERSION: 1.0.1-master
# BUILT ON: 2016-04-21T03:53:31.265Z
# CONFIGURATION:
# The following global var options are optional and can be moved to an external config file editor.config.php.
#$PASSWORD=md5('admin'); # Uncomment this line to allow login without a password
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
	?><!DOCTYPE html>
<html lang="en" class="set-password">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Code Editor - Set Password</title>
		<noscript>
			<?php
				if(isset($_POST['password'])){
					file_put_contents('./editor.config.php',"<?php\n\$PASSWORD=md5(".escapeshellarg($_POST['password']).");\n?>",FILE_APPEND);
					header('Location: '.$_SERVER['SCRIPT_NAME']);
				}
			?>
		</noscript>
		<link rel="stylesheet" href="//cdn.jsdelivr.net/bootstrap/3.3.6/css/bootstrap.min.css">
		<style>
		.set-password body {
			margin-top: 30px;
			font-size: 13px;
		}
		
		.set-password .container {
			max-width: 380px;
		}
		</style>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
		<script>
		$(function()
		{
			$('button').click(function()
			{
				document.cookie = "editor-auth=" + document.forms[0].password.value + ";path=" + window.location.pathname + ";max-age=315360000" + (document.location.protocol === "http:" ? "" : ";secure");
			});
		});
		</script>
	</head>
	<body>
		<div class="container">
			<form role="form" method="post">
				<div class="form-group">
					<p class="help-block">
						This is the first time you have logged in and no password has been set. Create a new password below.
					</p>
					<label for="exampleInputPassword1">New password</label>
					<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="password" autofocus required>
				</div>
				<!--
				<div class="form-group">
					<label for="exampleInputPassword1">Confirm password</label>
					<input type="password" class="form-control" id="exampleInputPassword2" placeholder="Confirm Password" name="passwordConfirm" required>
				</div>
				-->
				<button type="submit" class="btn btn-default">Submit</button>
			</form>
		</div>
	</body>
</html><?php
	exit;
}
if($PASSWORD!=md5('')&&(!isset($_COOKIE['editor-auth'])||md5($_COOKIE['editor-auth'])!=$PASSWORD)){
	?><!DOCTYPE html>
<html lang="en" class="login">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Code Editor - Login</title>
		<link rel="stylesheet" href="//cdn.jsdelivr.net/bootstrap/3.3.6/css/bootstrap.min.css">
		<style>
		.login body {
			margin-top: 30px;
			font-size: 13px;
		}
		
		.login .container {
			max-width: 380px;
		}
		</style>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
		<script>
		$(function()
		{
			$('button').click(function()
			{
				document.cookie = "editor-auth=" + document.forms[0].password.value + ";path=" + window.location.pathname + ";max-age=315360000" + (document.location.protocol === "http:" ? "" : ";secure");
				window.location = window.location.origin + window.location.pathname + window.location.search;
				return false;
			});
			if (window.location.hash.substring(1))
			{
				$("input[type=password]").val(window.location.hash.substring(1));
				window.location.hash = ''; // Don't retry if password wrong
				$('button').get(0).click();
			}
		});
		</script>
	</head>
	<body>
		<div class="container">
			<form role="form" method="get" class="form-inline">
				<div class="form-group">
					<input type="password" class="form-control" id="password" placeholder="Password" autofocus required>
				</div>
				<button type="submit" class="btn btn-default">Login</button>
			</form>
		</div>
	</body>
</html><?php
	exit;
}
if(isset($_GET["u"])){ # Experimental feature
	header('Content-Type: text/plain');
	passthru('wget https://raw.githubusercontent.com/simon-thorpe/editor/master/dist/editor.php -O '.escapeshellarg($_SERVER["SCRIPT_FILENAME"]).' 2>&1');
	exit;
}
if(isset($_GET['css'])){header('Content-Type: text/css');
header('Cache-Control: public, maxage=31536000');
?>#list,header nav .active{font-weight:700}#list>div,form{border-top:1px solid #444}body{font-family:sans-serif;font-size:11px;margin:0}a{text-decoration:none}input[type=text]{border:1px solid #444}header nav a{float:left;padding:6px 7px;color:#444}header nav a:hover{background-color:#aaa}header nav:after{content:"";clear:both;display:table}form{display:none;padding:10px}form .fieldRow{margin-bottom:5px}pre{margin:0 0 10px}input[readonly]{background-color:#ddd;opacity:.3}#list{border-bottom:1px solid #444}#list>div{width:100%;display:table}#list .copy,#list .cut,#list .del,#list .direct,#list .dl,#list .paste,#list .remove-clip{width:16px;min-width:16px;background-size:100%;background-position:50% 50%;background-repeat:no-repeat;background-color:#fff}#list>div>*{display:table-cell;padding:2px 0 2px 3px}#list .del:not([onclick]),#list .dir .paste,#list .direct[href=""]{display:none}#list>div>a .indent{margin-left:10px}#list>div.bad{text-decoration:line-through}#list .cut{background-image:url(//cdnjs.cloudflare.com/ajax/libs/fatcow-icons/20130425/FatCow_Icons32x32/cut.png)}#list .copy{background-image:url(//cdnjs.cloudflare.com/ajax/libs/fatcow-icons/20130425/FatCow_Icons32x32/page_copy.png)}#list .paste{background-image:url(//cdnjs.cloudflare.com/ajax/libs/fatcow-icons/20130425/FatCow_Icons32x32/page_paste.png)}#list .del,#list .remove-clip{background-image:url(//cdnjs.cloudflare.com/ajax/libs/fatcow-icons/20130425/FatCow_Icons32x32/delete.png)}#list .direct{background-image:url(//cdnjs.cloudflare.com/ajax/libs/fatcow-icons/20130425/FatCow_Icons32x32/world_go.png)}#list .dl{background-image:url(//cdnjs.cloudflare.com/ajax/libs/fatcow-icons/20130425/FatCow_Icons32x32/inbox_download.png)}#list>div:nth-of-type(odd){background-color:#dfd}#list>div:hover,#list>div:nth-of-type(odd):hover{background-color:#ddd}#list .seg:hover{text-decoration:underline;color:#444!important}#list .seg[href=""]:hover{text-decoration:none;color:#ccc!important}#list .dir,#list .file{cursor:pointer}#list .filepath .slash{color:#bbb}#list .dir .filepath,#list .dir .seg{color:#bb0}#list .file .filepath,#list .file .seg{color:#1b0}#list .file .seg.d{color:#bb0}#list .seg[href=""]{color:#ccc;cursor:default}#list .age,#list .size{color:#bbb;font-size:10px;width:100px}#list .size{margin-left:5px}#list .dir .size:after{content:" files"}#list .age:after{content:" old"}@media (max-width:979px){body{font-size:12px}#list>div>*{display:block;padding-bottom:0}#list .age,#list .size{float:left;height:16px;height:auto;padding:0 0 4px}#list .copy,#list .cut,#list .del,#list .direct,#list .dl,#list .paste,#list .remove-clip{float:right;height:17px;width:17px;padding:0}}button{border:none;margin:0;font-size:10px}#editor{margin-top:15px;position:absolute;top:0;bottom:0;left:0;right:0}<?php
exit;
}
if(isset($_GET['js'])){header('Content-Type: application/javascript');
header('Cache-Control: public, maxage=31536000');
?>!function(a){"use strict";var b="",c={};try{-1!==window.location.search.search(new RegExp("[?&]p=([^&$]*)","i"))&&(b=RegExp.$1),b=decodeURIComponent(b)}catch(d){alert(d),alert("Path="+b)}window.editor=c,c.detectFileMode=function(a){if(/^\/etc\/apache2\//.test(a))return"apache_conf";if(/Dockerfile$/.test(a))return"dockerfile";var b=a.substring(a.lastIndexOf(".")+1);switch(b){case"css":return"css";case"js":return"javascript";case"json":return"json";case"asax":case"ashx":case"cs":return"csharp";case"xml":return"xml";case"phtml":case"php":return"php";case"config":return"xml";case"as":return"actionscript";case"bat":case"cmd":return"batchfile";case"c":case"h":case"hpp":case"cpp":return"c_cpp";case"coffee":return"coffee";case"dart":return"dart";case"diff":return"diff";case"asp":case"asa":case"aspx":case"ascx":case"htm":case"html":return"html";case"ini":return"ini";case"java":return"java";case"jsp":return"jsp";case"less":return"less";case"lua":return"lua";case"pl":return"perl";case"ps":return"powershell";case"py":return"python";case"cgi":case"sh":return"sh";case"sql":return"sql";case"svg":return"svg";case"md":return"markdown";default:return""}},c.detectFileModeByContent=function(a){return 0===a.indexOf("#!/bin/sh")?"sh":0===a.indexOf("#!/bin/bash")?"sh":""},c.save=function(a){jQuery.ajax({url:"?",type:"post",dataType:"json",data:{content:c.instance.getValue(),p:b},success:function(){a?history.back():c.isModified&&(c.isModified=!1,document.title=document.title.substring(1))},error:function(a){alert(a.responseText)}})},c.clip={getItems:function(){var a=localStorage.clipValue;return a?JSON.parse(a):[]},setItems:function(a){localStorage.clipValue=JSON.stringify(a)},add:function(a,b){var c=this.getItems();c.push({path:a,type:b}),this.setItems(c)},remove:function(a){var b,d=a,e=c.clip.getItems();return"object"==typeof d&&(d=a.path),(b=e.filter(function(a){return a.path===d})[0])?(e.splice(e.indexOf(b),1),this.setItems(e),!0):!1}},c.del=function(b,c){var d=!1,e=a(c).parent("div");return 0===e.length&&console.error("DEBUG: Cannot find row in DOM."),a.ajax({url:"?",method:"post",data:{p:b,rm:1},async:!1,success:function(a){a.success===!0?(e.remove(),d=!0):alert(a.message)},error:function(a){alert(a.responseText)}}),d},c.cut=function(a){c.clip.add(a,"cut"),console.log("Clipboard: "+a)},c.copy=function(a){c.clip.add(a,"copy"),console.log("Clipboard: "+a)},c.paste=function(b,d){var e,f,g;b||(b=c.clip.getItems()[0].path),e=c.clip.getItems().filter(function(a){return a.path===b})[0],f=prompt("copy"===e.type?"Copy to:":"Move to:",e.path.substring(1+e.path.lastIndexOf("/"))),f&&(d+="/"+f,g={source:e.path,dest:d},"copy"===e.type?g.cp=1:"cut"===e.type&&(g.mv=1),d&&a.ajax({url:"?",method:"post",data:g,success:function(a){a.success===!0?(c.clip.remove(b),window.location=window.location):alert(a.message)},error:function(a){alert(a.responseText)}}))},c.instance=null,function(){var d,e,f;a("#editor").each(function(){a("header.list").hide(),f=c.detectFileMode(b),f||(f=c.detectFileModeByContent(document.getElementById("editor").textContent.trim())),c.instance=ace.edit("editor"),a(this).get(0).hasAttribute("data-readonly")&&(document.title+=" [readonly]",c.instance.setOptions({readOnly:!0,highlightActiveLine:!1,highlightGutterLine:!1})),c.instance.on("change",function(){c.isModified||(c.isModified=!0,document.title="*"+document.title)}),f?("undefined"!=typeof console&&console.log("Setting editor mode: ace/mode/"+f),c.instance.getSession().setMode("ace/mode/"+f)):"undefined"!=typeof console&&console.log("Unsupported editor mode. All available modes here: https://github.com/ajaxorg/ace-builds/tree/master/src-noconflict"),c.instance.commands.addCommand({name:"Save",bindKey:{win:"Ctrl-s",mac:"Command-s"},exec:function(){c.save(!1)}})}),window.location.hash&&(window.location=window.location.search),d=c.clip.getItems(),e=a("#list"),d.forEach(function(d){var f=a("<a/>").text(d.path),g=a("<a/>").addClass("paste").attr("href","#"),h=a("<a/>").addClass("remove-clip").attr("href","#");e.append(a("<div/>").addClass("clip").append(f).append(g).append(h)),g.click(function(){return c.paste(d.path,b),!1}),h.click(function(){return c.clip.remove(d.path)&&a(this).parent().remove(),!1})}),d.length>0&&a("#list>div.dir>a.paste").css({display:"table-cell"})}(),a(document).keydown(function(b){115===b.keyCode&&a(".shellButton").click()}),a(function(){a(".searchForm select").change(function(){"Locate Database"===a(this).val()?a(".searchForm input[type=checkbox]").prop("checked",!1).prop("disabled",!0):a(".searchForm input[type=checkbox]").prop("disabled",!1)}),a(".searchForm button").click(function(){var c=a(".searchForm select").val(),d="?p="+encodeURIComponent(b).replace(/%2F/g,"/");a(".searchForm input[type=checkbox]").prop("checked")&&(d+="&r="),"Filenames"!==c&&"All"!==c||(d+="&find="+encodeURIComponent(a(".searchForm input[type=text]").val()).replace(/%2F/g,"/")),"Content (All Files)"!==c&&"All"!==c||(d+="&grep="+encodeURIComponent(a(".searchForm input[type=text]").val()).replace(/%2F/g,"/")),"Content (Code Only)"===c&&(d+="&find=^[^.]*$|\\.(php|js|json|..?ss|p?html?|as..?|cs|vb|rb|py|txt|md|xml|xslt?|config)$&grep="+encodeURIComponent(a(".searchForm input[type=text]").val()).replace(/%2F/g,"/")),"Locate Database"===c&&(d+="&locate="+encodeURIComponent(a(".searchForm input[type=text]").val().replace(/[ ]+/g,".*")).replace(/%2F/g,"/")),window.location=d}),a(".newButton").click(function(){var c=prompt("New file (end with / for dir):","new.txt"),d="file";"/"===c.substring(c.length-1)&&(d="dir",c=c.substring(0,c.length-1)),c&&a.ajax({url:"?",method:"post",data:{"new":1,p:b+"/"+c,type:d},dataType:"json",success:function(a){a.success===!0?window.location="?p="+encodeURIComponent(b+"/"+c).replace(/%2F/g,"/"):alert(a.message)},error:function(a){alert(a.responseText)}})}),a(".searchButton").click(function(){a(this).addClass("active"),a(".searchForm").show().find("input[type=text]:first").select()}),a(".shellButton").click(function(){a(this).addClass("active");var c,d=a(".shellForm").show().find("input[type=text]:first").select(),e=!0,f=!1,g=!0;c=function(h){"undefined"==typeof h&&(h=null),a.ajax({url:"?",type:"post",dataType:"json",data:{ajaxShell:h,p:b},success:function(b){return b.lastCmd&&d.val(b.lastCmd).prop("readonly",!0),b.idle===!0?void d.prop("readonly",!1):(e===!0&&b.first!==!0&&(f=!0,a(".shellForm input#shellFormBackground").prop("checked",!0)),e=!1,g=!g,a("#shellOutput").html((f?"Continuing last session...\n\n":"")+b.output+(b["continue"]===!0?g?"_":" ":"")),void(b["continue"]===!0?setTimeout(c,500):("failure"===b.result?(a(".shellForm").css({backgroundColor:"#f44"}),alert("Last command failed.")):"success"===b.result?a(".shellForm").css({backgroundColor:"#6f6"}):(a(".shellForm").css({backgroundColor:"#bbb"}),alert("Last command had no result.")),window.onbeforeunload=null,d.prop("readonly",!1).select())))}})},c(),a(".shellForm").submit(function(){var b=JSON.parse(localStorage.shellHistory||"[]");return b.push(d.val()),localStorage.shellHistory=JSON.stringify(b),setTimeout(function(){window.onbeforeunload=function(){return"You have a shell command running."}},500),a(".shellForm input#shellFormBackground").prop("checked")?(a(".shellForm").css({backgroundColor:"#fff"}),c(d.val()),!1):!0})}),a('#list .dir a[class!="seg"], #list .file a[class!="seg"]').click(function(a){a.stopPropagation()}),a("#list .dir, #list .file").click(function(){a(this).find("a.seg:last").get(0).click()});var c=function(){var b,c,d=a("#shellHistory"),e=JSON.parse(localStorage.shellHistory||"[]");for(b=0;b<e.length,c=e[b];b+=1)d.append(a("<option/>").attr("value",c))};c();var d=null,e=function(b){null===d&&(d=a('<dialog id="styledModal" />').text("Uploading ").append(a("<progress max=100/>")),a(document.body).append(d),d.get(0).showModal());var c=d.find("progress").get(0);c.value=b,100===b&&(alert("Upload Complete!"),window.location=window.location)};a(document.body).dropzone({url:"?p="+b,clickable:!1,previewsContainer:"body",totaluploadprogress:e}),a(".uploadButton").dropzone({url:"?p="+b,clickable:!0,previewsContainer:"body",totaluploadprogress:e})})}(jQuery);<?php
exit;
}
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

if(isset($_POST["rm"])){
	header('Content-Type: application/json');
	if(is_dir($PATH)&&!is_link($PATH))
		echo '{"success":'.(rmdir($PATH)===true?'true':'false').'}';
	else
		echo '{"success":'.(unlink($PATH)===true?'true':'false').'}';
	exit;
}
elseif(isset($_POST["cp"])){
	header('Content-Type: application/json');
	if(file_exists($_POST["dest"]))
		echo '{"success":false,"message":"File already exists in destination."}';
	else
		echo '{"success":'.(copy($_POST["source"],$_POST["dest"])===true?'true':'false').'}';
	exit;
}
elseif(isset($_POST["mv"])){
	header('Content-Type: application/json');
	if(file_exists($_POST["dest"]))
		echo '{"success":false,"message":"File already exists in destination."}';
	else
		echo '{"success":'.(rename($_POST["source"],$_POST["dest"])===true?'true':'false').'}';
	exit;
}
elseif(isset($_POST["content"])){
	header('Content-Type: application/json');
	$content=$_POST["content"];
	file_put_contents($PATH,$content);
	echo '{"msg":"Your changes have been saved."}';
	exit;
}
elseif(isset($_POST["new"])){
	header('Content-Type: application/json');
	$type=$_POST["type"];
	if(file_exists($PATH))
		//echo '{"success":false,"message":""}';
		echo json_encode(array("success"=>false,"message"=>"File already exists."));
	else{
		if($type==='dir'){
			$r=mkdir($PATH);
		}
		elseif($type==='file'){
			$r=file_put_contents($PATH,'');
		}
		//echo '{"success":'.($r!==false?'true':'false').'}';
		echo json_encode(array("success"=>$r!==false,"message"=>"Error writing ".$PATH));
	}
	exit;
}
elseif(isset($_GET["d"])){
	// http://php.net/manual/en/function.readfile.php
	header('Content-Description: File Transfer');
	$fi=finfo_open(FILEINFO_MIME);
	header('Content-Type: '.finfo_file($fi,$PATH));
	finfo_close($fi);
	header('Content-Disposition: inline; filename='.basename($PATH));
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: private, max-age=0, must-revalidate');
	header('Content-Length: '.filesize($PATH));
	ob_clean();
	flush();
	readfile($PATH);
	exit;
}
elseif(isset($_FILES['file'])){
	$dest=$PATH.'/'.$_FILES['file']['name'];
	$tempFile=$_FILES['file']['tmp_name'];
	header('Content-Type: application/json');
	echo json_encode(array("success"=>move_uploaded_file($_FILES['file']['tmp_name'],$dest)===true));
	exit;
}

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
	<link rel="stylesheet" href="?css=1.0.1-master">
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
		<pre id=shellOutput>Common shell commands:
du -hd1|sort -h
fdupes -rq .
ps -eo pcpu,time,pid,args|tail -n +2|sort -nrk1
iotop -obn1</pre>
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
<script src="?js=1.0.1-master"></script>
<?php if(isset($_POST['shell']))echo '<script>$(function(){$(".shellButton").triggerHandler("click");});</script>'; // If was postback then shell form is already open so need to trigger click to init form events ?>
</body>
</html>
