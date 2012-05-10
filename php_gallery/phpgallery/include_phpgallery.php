<?php require_once "include_cutesoft.php" ?>
<?php require_once "include_imaging_gd.php" ?>
<?php require_once "phpuploader/include_phpuploader.php" ?>
<?php

error_reporting(E_ALL ^ E_NOTICE);

if(!@$_SESSION)session_start();

class PhpGallery extends CuteSoftLibrary
{
	public $GalleryFolder="defaultalbum";
	
	public $AllowEdit=false;
	public $AllowPostComment=false;
	public $AllowShowComment=true;
	
	public $LogonUserID=null;
	public $LogonUserName=null;
	
	public $Layout="SlideShow";
	public $Editor="Explorer";
	public $Slider="NewWin";
	public $Viewer="LightBox";
	public $Popup="Default";
	public $Theme="Classic";
	
	public $Width=800;
	public $Height=480;
	
	public $Culture="en";
	
	public $Uploader;
	
	public $ClientID="PhpGalleryControl";
	
	var $GalleryClient;

	function PhpGallery()
	{
		$this->Uploader=new PhpUploader();
		$this->Uploader->MultipleFilesUpload=true;
		$this->Uploader->AllowedFileExtensions="jpeg,jpg,gif,png";	//,bmp
		$this->GalleryClient=dirname(dirname($this->Uploader->ResourceDirectory))."/resources";
		$this->Uploader->LicenseUrl="$this->GalleryClient/load.php?type=license&_temp=".time();
	}
	
	function AppendScript($code,$path)
	{
		$code=$code."<script type='text/javascript' charset='UTF-8' src='$this->GalleryClient/";
		$code=$code.$path;
		$code=$code."'></script>\r\n";
		return $code;
	}
	function AppendStyle($code,$path)
	{
		$f=dirname(__FILE__)."/resources/$path";
		
		if(!file_exists($f))
			return $code;

		$code=$code."<link rel='stylesheet' href='$this->GalleryClient/";
		
		$code=$code.$path;
		
		$code=$code."'/>\r\n";

		return $code;
	}
	function GetString()
	{	
		$code="\r\n";
		
		$code=$code."<script type='text/javascript'>";
		$code=$code."var GalleryAjaxData={Controls:[{ClientID:'";
		$code=$code.$this->ClientID;
		$code=$code."',UniqueID:'";
		$code=$code.$this->ClientID;
		$code=$code."',Methods:[{Name:'DeletePhotoComment',ParameterCount:3},{Name:'DeleteCategoryComment',ParameterCount:2},{Name:'AddPhotoComment',ParameterCount:4},{Name:'AddCategoryComment',ParameterCount:3},{Name:'GetCategoryData',ParameterCount:1},{Name:'GetAllCategoryData',ParameterCount:0},{Name:'UpdatePhoto',ParameterCount:4},{Name:'UpdateCategory',ParameterCount:2},{Name:'DeletePhoto',ParameterCount:2},{Name:'DeleteCategory',ParameterCount:1},{Name:'CreateCategory',ParameterCount:1},{Name:'UploadFiles',ParameterCount:1}]}]};";
		$code=$code."</script>\r\n";

		
		$code=$this->AppendScript($code,"load.php?type=culture&culture=$this->Culture");
		$code=$this->AppendScript($code,"Core/GalleryAjax.js");
		$code=$this->AppendScript($code,"Core/GalleryLibrary.js");
		$code=$this->AppendScript($code,"Share/LibraryExt.js");
		$code=$this->AppendScript($code,"Core/GalleryMenu.js");
		$code=$this->AppendScript($code,"Core/GalleryBrowser.js");
		$code=$this->AppendScript($code,"Layout/$this->Layout/Code.js");
		$code=$this->AppendScript($code,"Slider/$this->Slider/Code.js");
		$code=$this->AppendScript($code,"Viewer/$this->Viewer/Code.js");
		$code=$this->AppendScript($code,"Popup/$this->Popup/Code.js");
		$code=$this->AppendScript($code,"Theme/$this->Theme/Code.js");
		
		if($this->AllowEdit)
		{
			$code=$this->AppendScript($code,"Editor/$this->Editor/Code.js");
		}
		
		$code=$this->AppendStyle($code,"Gallery.css");
		$code=$this->AppendStyle($code,"Theme/$this->Theme/Style.css");
		$code=$this->AppendStyle($code,"Culture/$this->Culture/Style.css");
		$code=$this->AppendStyle($code,"Layout/$this->Layout/Style.css");
		$code=$this->AppendStyle($code,"Viewer/$this->Viewer/Style.css");
		$code=$this->AppendStyle($code,"Slider/$this->Slider/Style.css");
		$code=$this->AppendStyle($code,"Popup/$this->Popup/Style.css");
		
		if($this->AllowEdit)
		{
			$code=$this->AppendStyle($code,"Editor/$this->Editor/Style.css");
		}
		
		$code=$code."<div id='$this->ClientID' style='width:{$this->Width}px;height:{$this->Height}px;'>";
		$code=$code."\r\n<!--Start Layout Template '$this->Layout'-->\r\n";
		$tfile=dirname(__FILE__)."/resources/Layout/$this->Layout/Template.htm";
		$hfile=fopen($tfile,"r");
		$dfile=fread($hfile,filesize($tfile));
		fclose($hfile);		
		$code=$code.$dfile;
		$code=$code."\r\n<!--End Template-->\r\n";
		if($this->AllowEdit)
		{
			$code=$code."\r\n<!--Start Ajax Uploader For Gallery-->";
			$code=$code."<span id='{$this->ClientID}_UploaderContainer' style='display:none'>";
			$code=$code.$this->Uploader->GetString();
			$code=$code."</span>";
			$code=$code."\r\n<!--End Uploader-->\r\n";
		}
		$code=$code."</div>\r\n";
		
		$code=$code."<script type='text/javascript'>";
		$code=$code."var thegallerybrowser=new GalleryBrowser(";
		$code=$code."{ClientID:'$this->ClientID'";
		
		$code=$code.",UniqueID:'$this->ClientID'";
		
		if(this.AllowEdit)
		{
			$code=$code.",UploaderContainerID:'{$this->ClientID}_UploaderContainer'";
			$code=$code.",UploaderClientID:'".$this->Uploader->Name."'";
			$code=$code.",UploaderUniqueID:'".$this->Uploader->Name."'";
		}
		
		$code=$code.",Folder:'$this->GalleryClient/'";
		$code=$code.",LoadHandler:'load.php'";
		$code=$code.",Theme:'$this->Theme'";
		$code=$code.",Culture:'$this->Culture'";
		$code=$code.",Layout:'$this->Layout'";
		$code=$code.",Slider:'$this->Slider'";
		$code=$code.",Viewer:'$this->Viewer'";
		$code=$code.",Popup:'$this->Popup'";
		$code=$code.",Editor:'$this->Editor'";
		
		$code=$code.",LogonUserID:'$this->LogonUserID'";
		$code=$code.",LogonUserName:'$this->LogonUserName'";
		
		$code=$code.",AllowEdit:".($this->AllowEdit?"1":"null");
		$code=$code.",AllowPostComment:".($this->AllowPostComment?"1":"null");
		$code=$code.",AllowShowComment:".($this->AllowShowComment?"1":"null");

		//TODO:
		$code=$code."\r\n,ScriptOption:{}";
		
		$code=$code."\r\n,Categories:";
		$code=$code.$this->ToJSON($this->Ajax_GetAllCategoryData());
		$code=$code."})";
		$code=$code."</script>";
		
		$code=$code."\r\n";
		
		return $code;
	}
	
	function Render()
	{
		echo $this->GetString();
	}
	
	
	
	//call
	function InvokeAjaxMethod($context)
	{
		switch($context->Method)
		{
			case "GetAllCategoryData":
				return $this->Ajax_GetAllCategoryData();
			case "GetCategoryData":
				return $this->Ajax_GetCategoryData($context->Arguments[0]);
			case "CreateCategory":
				return $this->Ajax_CreateCategory($context->Arguments[0]);
			case "DeleteCategory":
				return $this->Ajax_DeleteCategory($context->Arguments[0]);
			case "UpdateCategory":
				return $this->Ajax_UpdateCategory($context->Arguments[0],$context->Arguments[1]);
			case "AddCategoryComment":
				return $this->Ajax_AddCategoryComment($context->Arguments[0],$context->Arguments[1],$context->Arguments[2]);
			case "DeleteCategoryComment":
				return $this->Ajax_DeleteCategoryComment($context->Arguments[0],$context->Arguments[1]);
			case "DeletePhoto":
				return $this->Ajax_DeletePhoto($context->Arguments[0],$context->Arguments[1]);
			case "UpdatePhoto":
				return $this->Ajax_UpdatePhoto($context->Arguments[0],$context->Arguments[1],$context->Arguments[2],$context->Arguments[3]);
			case "AddPhotoComment":
				return $this->Ajax_AddPhotoComment($context->Arguments[0],$context->Arguments[1],$context->Arguments[2],$context->Arguments[3]);
			case "DeletePhotoComment":
				return $this->Ajax_DeletePhotoComment($context->Arguments[0],$context->Arguments[1],$context->Arguments[2]);
			case "UploadFiles":
				return $this->Ajax_UploadFiles($context->Arguments[0]);
			default:
				throw(new Exception("Invalid method:$context->Method"));
		}
	}
	
	function RequireEditPermission()
	{
		if(!$this->AllowEdit)
			throw(new Exception("Do not allow editing"));
	}
	
	function Ajax_CreateCategory($title)
	{
		$this->RequireEditPermission();
		return $this->Provider_CreateCategory($title);
	}
	function Ajax_DeleteCategory($categoryid)
	{
		$this->RequireEditPermission();
		return $this->Provider_DeleteCategory($categoryid);
	}
	function Ajax_UpdateCategory($categoryid,$title)
	{
		$this->RequireEditPermission();
		return $this->Provider_UpdateCategory($categoryid,$title);
	}
	function Ajax_AddCategoryComment($categoryid,$content,$guestname)
	{
		if(!$this->AllowPostComment)throw(new Exception("Do not allow post comment"));
		return $this->Provider_AddCategoryComment($categoryid,$content,$guestname);
	}
	function Ajax_DeleteCategoryComment($categoryid,$commentid)
	{
		$this->RequireEditPermission();
		return $this->Provider_DeleteCategoryComment($categoryid,$commentid);
	}
	function Ajax_DeletePhoto($categoryid,$photoid)
	{
		$this->RequireEditPermission();
		return $this->Provider_DeletePhoto($categoryid,$photoid);
	}
	function Ajax_UpdatePhoto($categoryid,$photoid,$title,$comment)
	{
		$this->RequireEditPermission();
		return $this->Provider_UpdatePhoto($categoryid,$photoid,$title,$comment);
	}
	function Ajax_AddPhotoComment($categoryid,$photoid,$content,$guestname)
	{
		if(!$this->AllowPostComment)throw(new Exception("Do not allow post comment"));
		return $this->Provider_AddPhotoComment($categoryid,$photoid,$content,$guestname);
	}
	function Ajax_DeletePhotoComment($categoryid,$photoid,$commentid)
	{
		$this->RequireEditPermission();
		return $this->Provider_DeletePhotoComment($categoryid,$photoid,$commentid);
	}
	function Ajax_GetAllCategoryData()
	{
		$arr=array();
		array_push($arr,$this->_GetCategoryDataImpl(null));
		foreach($this->Provider_GetCategoryArray() as $categoryid)
		{
			array_push($arr,$this->_GetCategoryDataImpl($categoryid));
		}
		return $arr;
	}
	function Ajax_GetCategoryData($categoryid)
	{
		return $this->_GetCategoryDataImpl($categoryid);
	}
	function _GetCategoryDataImpl($categoryid)
	{
		$info=$this->Provider_GetCategoryInfo($categoryid);
		$photos=array();
		foreach($this->Provider_GetPhotoArray($categoryid) as $photoid)
		{
			$pinfo=$this->Provider_GetPhotoInfo($categoryid,$photoid);
			$photo=array();
			$photo["CategoryID"]=$pinfo["CategoryID"];
			$photo["PhotoID"]=$pinfo["PhotoID"];
			$photo["Title"]=$pinfo["Title"];
			$photo["Comment"]=$pinfo["Comment"];
			$photo["Width"]=$pinfo["Width"];
			$photo["Height"]=$pinfo["Height"];
			$photo["Size"]=$pinfo["Size"];
			$photo["Time"]=$pinfo["Time"];
			$photo["Url"]=$this->MakeAbsolute($pinfo["Url"]);
			$photo["Thumbnail"]=$this->MakeAbsolute($pinfo["Thumbnail"]);
			if($this->AllowShowComment)
			{
				$photo["Comments"]=$this->Provider_GetPhotoComments($categoryid,$photoid);
			}
			array_push($photos,$photo);
		}
		$info["Photos"]=$photos;
		if($this->AllowShowComment)
		{
			$info["Comments"]=$this->Provider_GetCategoryComments($categoryid);
		}
		return $info;
	}
	
	function Ajax_UploadFiles($categoryid)
	{
		$this->Provider_CheckFilePart($categoryid);
		$folder=$this->GalleryFolder;
		if($categoryid!=null)
			$folder="$this->GalleryFolder/category".$categoryid;
			
		$guidlist=$_POST[$this->Uploader->Name];
		
		if($guidlist==null||strlen($guidlist)==0)
			throw(new Exception("no files."));
		
		$results=array();
		$guidarray=explode("/",$guidlist);
		foreach($guidarray as $fileguid)
		{
			$mvcfile=$this->Uploader->GetUploadedFile($fileguid);
			if(!$mvcfile)
				continue;
			
			$photoid=$mvcfile->FileName;
			for($index=0;true;$index++)
			{
				if($index>0)
				{
					$photoid=$this->GetFileNameWithoutExtension($mvcfile->FileName)."($index)".$this->GetExtension($mvcfile->FileName);
				}
				$path="$folder/$photoid";
				if($this->FileExists($path))
					continue;
				$mvcfile->MoveTo($path);
				break;
			}

			$result=array();
			$result["FileGuid"]=$fileguid;
			$result["FileName"]=$mvcfile->FileName;
			$result["PhotoID"]=$photoid;
			array_push($results,$result);
		}
		return $results;
	}
	
	function Provider__LoadAlbumDoc($create)
	{
		$configfile="$this->GalleryFolder/album.config";
		$doc=new DOMDocument();
		if(!$this->FileExists($configfile))
		{
			if(!$create)
				return null;
			$doc->loadXML("<album/>");
			$root=$doc->childNodes->item(0);
			$root->setAttribute("name","(Default Album)");
			$doc->save($configfile);
		}
		else
		{
			$doc->load($configfile);
		}
		return $doc;
	}
	function Provider__SaveAlbumDoc($doc)
	{
		$configfile="$this->GalleryFolder/album.config";
		$doc->save($configfile);
	}
	function Provider__LoadCategoryDoc($categoryid,$create)
	{
		$this->Provider_CheckFilePart($categoryid);
		$folder=$this->GalleryFolder;
		if($categoryid!=null)
			$folder="$this->GalleryFolder/category".$categoryid;
		$doc=new DOMDocument();
		$configfile="$folder/category.config";
		if(!$this->FileExists($configfile))
		{
			if(!$create)
				return null;
			$doc->loadXML("<category/>");
			$doc->save($configfile);
		}
		else
		{
			$doc->load($configfile);
		}
		return $doc;
	}
	function Provider__SaveCategoryDoc($categoryid,$doc)
	{
		$this->Provider_CheckFilePart($categoryid);
		$folder=$this->GalleryFolder;
		if($categoryid!=null)
			$folder="$this->GalleryFolder/category".$categoryid;
		$configfile="$folder/category.config";
		$doc->save($configfile);
	}

	function Provider_GetCategoryArray()
	{
		$doc=$this->Provider__LoadAlbumDoc(false);
		if(!$doc)
			return array();
		$arr=array();
		$dom=$doc->childNodes->item(0);
		$nodes=$dom->childNodes;
		for($i=0;$i<$nodes->length;$i++)
		{
			$node=$nodes->item($i);
			if($node->nodeType!=1)continue;
			if($node->nodeName=="category")
			{
				array_push($arr,$node->getAttribute("id"));
			}
		}
		return $arr;
	}

	function Provider_CreateCategory($category)
	{
		$doc=$this->Provider__LoadAlbumDoc(true);
		$dom=$doc->childNodes->item(0);
		$nextid=$dom->getAttribute("nextcategoryid");
		if($nextid==null||$nextid=="")
			$nextid="1";
		$dom->setAttribute("nextcategoryid",$nextid+1);

		$xe=$doc->createElement("category");
				
		$xe->setAttribute("id",$nextid);
		$dom->appendChild($xe);
		
		$cdoc=new DOMDocument();
		$cdoc->loadXML("<category/>");
		$cdom=$cdoc->childNodes->item(0);
		$cdom->setAttribute("id",$nextid);
		$cdom->setAttribute("title",$category);
		$this->CreateDirectory("$this->GalleryFolder/category{$nextid}");
		$cdoc->save("$this->GalleryFolder/category{$nextid}/category.config");
		
		$this->Provider__SaveAlbumDoc($doc);
		return nextid;
	}
	function Provider_DeleteCategory($categoryid)
	{
		$this->Provider_CheckFilePart($categoryid);
		
		$folder=$this->GalleryFolder;
		if($categoryid!=null)
			$folder="$this->GalleryFolder/category".$categoryid;
	
		$photos=$this->Provider_GetPhotoArray($categoryid);
		foreach($photos as $photoid)
		{
			$this->Provider_DeletePhoto($categoryid,$photoid);
		}
		
		$doc=$this->Provider__LoadAlbumDoc(true);
		$dom=$doc->childNodes->item(0);
		for($i=0;$i<$dom->childNodes->length;$i++)
		{
			$node=$dom->childNodes->item($i);
			if($node->nodeName!="category")continue;
			if($node->getAttribute("id")==$categoryid)
			{
				$dom->removeChild($node);
				$this->Provider__SaveAlbumDoc($doc);
				break;
			}
		}
		
		if($categoryid!=null)
		{
			$this->DeleteDirectory($folder);
		}
		else
		{
			$configfile="$folder/category.config";
			$this->DeleteFile($configfile);
		}
	}
	function Provider_GetCategoryInfo($categoryid)
	{
		$info=array();
		$info["CategoryID"]=$categoryid;
		$doc=$this->Provider__LoadCategoryDoc($categoryid,false);
		if($doc!=null)
		{
			$dom=$doc->childNodes->item(0);
			
			$info["Title"]=$dom->getAttribute("title");
			$info["Description"]=$dom->getAttribute("description");
		}
		return $info;
	}
	function Provider_UpdateCategory($categoryid,$title)
	{
		$doc=$this->Provider__LoadCategoryDoc($categoryid,true);
		$dom=$doc->childNodes->item(0);
		$dom->setAttribute("title",$title);
		$this->Provider__SaveCategoryDoc($categoryid,$doc);
	}
	
	function Provider_GetCategoryComments($categoryid)
	{
		$doc=$this->Provider__LoadCategoryDoc($categoryid,false);
		if(!$doc)
			return array();
		
		$dom=$doc->childNodes->item(0);
		
		$comments=array();
		$ns=$dom->childNodes;
		for($i=0;$i<$ns->length;$i++)
		{
			$node=$ns->item($i);
			if($node->nodeName!="comment")
				continue;
			$comment=array();
			$comment["CommentID"]=$node->getAttribute("commentid");
			$comment["SenderID"]=$node->getAttribute("userid");
			$comment["SenderName"]=$node->getAttribute("username");
			$comment["Content"]=$node->getAttribute("content");
			//$comment["IPAddress"]=$node->getAttribute("ipaddress");
			$comment["Time"]=new CuteSoftDateTime($node->getAttribute("datetime"));
			array_push($comments,$comment);
		}
		return $comments;
	}
	function Provider_AddCategoryComment($categoryid,$content,$guestname)
	{
		$doc=$this->Provider__LoadCategoryDoc($categoryid,true);
		
		$dom=$doc->childNodes->item(0);
		
		$username=$this->LogonUserName;
		if($username==null||$username=="")
			$username=$guestname;
		
		$xe=$doc->createElement("comment");
		$xe->setAttribute("commentid",$this->NewGuid());
		$xe->setAttribute("userid",$this->LogonUserID);
		$xe->setAttribute("username",$username);
		$xe->setAttribute("content",$content);
		$xe->setAttribute("ipaddress",$_SERVER['REMOTE_ADDR']);
		$xe->setAttribute("datetime",microtime(true));
		$dom->appendChild($xe);
		
		$this->Provider__SaveCategoryDoc($categoryid,$doc);
	}
	function Provider_DeleteCategoryComment($categoryid,$commentid)
	{
		$doc=$this->Provider__LoadCategoryDoc($categoryid,true);
		
		$dom=$doc->childNodes->item(0);
		
		$ns=$dom->childNodes;
		for($i=0;$i<$ns->length;$i++)
		{
			$node=$ns->item($i);
			if($node->nodeName!="comment")continue;
			if($node->getAttribute("commentid")==$commentid)
			{
				$dom->removeChild($node);
				$this->Provider__SaveCategoryDoc($categoryid,$doc);
				return;
			}
		}
	}
	
	function Provider_GetPhotoArray($categoryid)
	{
		$this->Provider_CheckFilePart($categoryid);
		$folder=$this->GalleryFolder;
		if($categoryid!=null)
			$folder="$this->GalleryFolder/category".$categoryid;
		$arr=array();
		foreach($this->GetFiles($folder,"*.*") as $file)
		{
			if(strpos($file,".thumbnail."))
				continue;
			$lowerext=strtolower($this->GetExtension($file));
			switch($lowerext)
			{
				//case ".bmp":
				case ".png":
				case ".gif":
				case ".jpg":
				case ".jpeg":
					array_push($arr,$this->GetBaseName($file));
					break;
				default:
					break;
			}
		}
		return $arr;
	}
	
	function Provider_DeletePhoto($categoryid,$photoid)
	{
		$this->Provider_CheckFilePart($categoryid);
		$this->Provider_CheckFilePart($photoid);
		$folder=$this->GalleryFolder;
		if($categoryid!=null)
			$folder="$this->GalleryFolder/category".$categoryid;
		$photopath="$folder/$photoid";
		$configfile="$folder/$photoid.config";
		$thumbpath=$folder."/".$this->GetFileNameWithoutExtension($photoid).".thumbnail.jpg";
		$this->DeleteFile($thumbpath);
		$this->DeleteFile($configfile);
		$this->DeleteFile($photopath);
	}

	function Provider_UpdatePhoto($categoryid,$photoid,$title,$comment)
	{
		$this->Provider_CheckFilePart($categoryid);
		$this->Provider_CheckFilePart($photoid);
		$folder=$this->GalleryFolder;
		if($categoryid!=null)
			$folder="$this->GalleryFolder/category".$categoryid;
		$photopath="$folder/$photoid";
		$configfile="$folder/$photoid.config";
		$doc=new DOMDocument();
		$root=null;
		if($this->FileExists($configfile))
		{
			$doc->load($configfile);
			$root=$doc->childNodes->item(0);
		}
		else
		{
			$dim=PhpGallery_GetPhotoDimensions($photopath);
			$doc->loadXML("<photo/>");
			$root=$doc->childNodes->item(0);
			$root->setAttribute("id",$photoid);
			$root->setAttribute("categoryid",$categoryid);
			$root->setAttribute("width",$dim["Width"]);
			$root->setAttribute("height",$dim["Height"]);
			$root->setAttribute("filesize",filesize($photopath));
			$root->setAttribute("ipaddress","");
			$root->setAttribute("datetime",filectime($photopath));
		}
		$root->setAttribute("title",$title);
		$root->setAttribute("comment",$comment);
		$doc->save($configfile);
	}
	
	function Provider_GetPhotoInfo($categoryid,$photoid)
	{
		$this->Provider_CheckFilePart($categoryid);
		$this->Provider_CheckFilePart($photoid);
		$folder=$this->GalleryFolder;
		if($categoryid!=null)
			$folder="$this->GalleryFolder/category".$categoryid;
		$photopath="$folder/$photoid";
		$configfile="$folder/$photoid.config";
		if(!$this->FileExists($configfile))
			$this->Provider_UpdatePhoto($categoryid,$photoid,$photoid,"");
		$dom=dom_import_simplexml(simplexml_load_file($configfile));
		$info=array();
		$info["CategoryID"]=$categoryid;
		$info["PhotoID"]=$photoid;
		$info["Title"]=$dom->getAttribute("title");
		$info["Comment"]=$dom->getAttribute("comment");
		$info["Width"]=$dom->getAttribute("width");
		$info["Height"]=$dom->getAttribute("height");
		$info["Size"]=$dom->getAttribute("filesize");
		$info["Time"]=new CuteSoftDateTime($dom->getAttribute("time"));
		$info["Url"]=$photopath;
		$info["Thumbnail"]=$this->Provider_GetPhotoThumbnail($folder,$photoid);
		return $info;
	}
	function Provider_GetPhotoThumbnail($folder,$photoid)
	{
		$thumbpath=$folder."/".$this->GetFileNameWithoutExtension($photoid).".thumbnail.jpg";
		if(!$this->FileExists($thumbpath))
		{
			PhpGallery_GenerateThumbnail("$folder/$photoid",$thumbpath,128,128);
		}
		return $thumbpath;
	}
	function Provider_GetPhotoComments($categoryid,$photoid)
	{
		$this->Provider_CheckFilePart($categoryid);
		$this->Provider_CheckFilePart($photoid);
		
		$folder=$this->GalleryFolder;
		if($categoryid!=null)
			$folder="$this->GalleryFolder/category".$categoryid;
		$photopath="$folder/$photoid";
		$configfile="$folder/$photoid.config";
		if(!$this->FileExists($configfile))
			$this->Provider_UpdatePhoto($categoryid,$photoid,$photoid,"");
		
		$doc=new DOMDocument();
		$doc->load($configfile);
		$dom=$doc->childNodes->item(0);
		
		$comments=array();
		$ns=$dom->childNodes;
		for($i=0;$i<$ns->length;$i++)
		{
			$node=$ns->item($i);
			if($node->nodeName!="comment")
				continue;
			$comment=array();
			$comment["CommentID"]=$node->getAttribute("commentid");
			$comment["SenderID"]=$node->getAttribute("userid");
			$comment["SenderName"]=$node->getAttribute("username");
			$comment["Content"]=$node->getAttribute("content");
			//$comment["IPAddress"]=$node->getAttribute("ipaddress");
			$comment["Time"]=new CuteSoftDateTime($node->getAttribute("datetime"));
			array_push($comments,$comment);
		}
		return $comments;
	}
	function Provider_AddPhotoComment($categoryid,$photoid,$content,$guestname)
	{
		$this->Provider_CheckFilePart($categoryid);
		$this->Provider_CheckFilePart($photoid);
		
		$folder=$this->GalleryFolder;
		if($categoryid!=null)
			$folder="$this->GalleryFolder/category".$categoryid;
		$photopath="$folder/$photoid";
		$configfile="$folder/$photoid.config";
		if(!$this->FileExists($configfile))
			$this->Provider_UpdatePhoto($categoryid,$photoid,$photoid,"");
		
		$doc=new DOMDocument();
		$doc->load($configfile);
		$dom=$doc->childNodes->item(0);
		
		$username=$this->LogonUserName;
		if($username==null||$username=="")
			$username=$guestname;
		
		$xe=$doc->createElement("comment");
		$xe->setAttribute("commentid",$this->NewGuid());
		$xe->setAttribute("userid",$this->LogonUserID);
		$xe->setAttribute("username",$this->LogonUserName);
		$xe->setAttribute("content",$content);
		$xe->setAttribute("ipaddress",$_SERVER['REMOTE_ADDR']);
		$xe->setAttribute("datetime",microtime(true));
		$dom->appendChild($xe);
		$doc->save($configfile);
	}
	function Provider_DeletePhotoComment($categoryid,$photoid,$commentid)
	{
		$this->Provider_CheckFilePart($categoryid);
		$this->Provider_CheckFilePart($photoid);
		
		$folder=$this->GalleryFolder;
		if($categoryid!=null)
			$folder="$this->GalleryFolder/category".$categoryid;
		$photopath="$folder/$photoid";
		$configfile="$folder/$photoid.config";
		if(!$this->FileExists($configfile))
			$this->Provider_UpdatePhoto($categoryid,$photoid,$photoid,"");
		
		$doc=new DOMDocument();
		$doc->load($configfile);
		$dom=$doc->childNodes->item(0);
		
		$ns=$dom->childNodes;
		for($i=0;$i<$ns->length;$i++)
		{
			$node=$ns->item($i);
			if($node->nodeName!="comment")continue;
			if($node->getAttribute("commentid")==$commentid)
			{
				$dom->removeChild($node);
				$doc->save($configfile);
				return;
			}
		}
	}
	
	function Provider_CheckFilePart($val)
	{
		if(!$val)return;
		if(preg_replace("[\\*\\?\\\\/|]","",$val)!=$val)
			throw(new Exception("Invalid value : $val"));
	}
}



?>