<?php
namespace cms\system\importer;
use cms\data\news\News;
use cms\data\news\NewsEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractAttachmentImporter;
use wcf\system\importer\ImportHandler;

/**
 * @author	Florian Gail
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsAttachmentImporter extends AbstractAttachmentImporter {
	/**
	 * @see	\wcf\system\importer\AbstractACLImporter::$objectTypeName
	 */
	protected $objectTypeName = 'de.codequake.cms.category.news';
	
	/**
	 * Creates a new NewsAttachmentImporter object.
	 */
	public function __construct() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.attachment.objectType', 'de.codequake.cms.news');
		$this->objectTypeID = $objectType->objectTypeID;
	}
	
	/**
	 * @see	\wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = array()) {
		$data['objectID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.news', $data['objectID']);
		if (!$data['objectID'])
			return 0;
		
		$attachmentID = parent::import($oldID, $data, $additionalData);
		if ($attachmentID && $attachmentID != $oldID) {
			// fix embedded attachments
			$news = new News($data['objectID']);
			
			if (($newMessage = $this->fixEmbeddedAttachments($news->message, $oldID, $attachmentID)) !== false) {
				$editor = new NewsEditor($news);
				$editor->update(array(
					'message' => $newMessage
				));
			}
		}
		
		return $attachmentID;
	}
}