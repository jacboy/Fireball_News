<?php
use cms\data\file\FileAction;
use cms\data\news\image\NewsImageList;
use cms\data\news\NewsEditor;
use cms\data\news\NewsList;
use wcf\data\category\CategoryAction;
use wcf\system\category\CategoryHandler;
use wcf\system\WCF;
use wcf\util\FileUtil;
/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

//category object type
$objectType = CategoryHandler::getInstance()->getObjectTypeByName('de.codequake.cms.file');

$objectAction = new CategoryAction(array(), 'create', array(
		'data' => array(
				'description' => '',
				'isDisabled' => 0,
				'objectTypeID' => $objectType->objectTypeID,
				'parentCategoryID' => null,
				'showOrder' => null,
				'title' => 'news images'
			)	
		));
		
$objectAction->executeAction();
$returnValues = $objectAction->getReturnValues();
$categoryID = $returnValues['returnValues']->categoryID;

//get old news images
$list = new NewsImageList();
$list->readObjects();


$oldIDs = array();
foreach ($list->getObjects() as $image) {
	//get file hash
	$fileHash = sha1_file(CMS_DIR. 'images/news/' . $image->filename);
	$folder = substr($fileHash, 0, 2);
	
	//get size
	$size = filesize(CMS_DIR. 'images/news/' . $image->filename);
	
	//mime type
	$mime = FileUtil::getMimeType(CMS_DIR. 'images/news/' . $image->filename);
	
	//create db entry
	$action  = new FileAction(array(), 'create', array(
		'data' => array(
			'title' => $image->getTitle(),
			'filesize' => $size,
			'fileType' => $mime,
			'fileHash' => $fileHash,
			'uploadTime' => TIME_NOW
		)
	));
	$action->executeAction();
	$returnValues = $action->getReturnValues();
	
	//set old IDs
	$oldIDs[$image->imageID] = $returnValues['returnValues']->fileID;
	
	if (!is_dir(CMS_DIR . 'files/' . $folder)) FileUtil::makePath(CMS_DIR . 'files/' . $folder);
	copy (CMS_DIR. 'images/news/' .$image->filename, CMS_DIR . 'files/' . $folder . '/' . $returnValues['returnValues']->fileID . '-' . $fileHash);
	@unlink(CMS_DIR. 'images/news/' .$image->filename);
	
	//insert into news image category
	$sql = "INSERT INTO cms".WCF_N."_file_to_category VALUES (?, ?)";
	$statement = WCF::getDB()->prepareStatement($sql);
	
	$statement->execute(array($returnValues['returnValues']->fileID, $categoryID));
}

//loop through news
$list = new NewsList();
$list->readObjects();

foreach ($list->getObjects() as $news) {
	//check if image is set ( no foreign key is given :( )
	if ($news->imageID !== 0 && isset($oldIDs[$news->imageID])) {
		$editor = new NewsEditor($news);
		$editor->update(array('imageID' => $oldIDs[$news->imageID]));
	}
}

