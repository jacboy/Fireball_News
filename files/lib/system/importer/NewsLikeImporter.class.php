<?php
namespace cms\system\importer;
use wcf\data\like\Like;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractLikeImporter;
use wcf\system\importer\ImportHandler;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class NewsLikeImporter extends AbstractLikeImporter {

	public function __construct() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.like.likeableObject', 'de.codequake.cms.likeableNews');
		$this->objectTypeID = $objectType->objectTypeID;
	}

	public function import($oldID, array $data, array $additionalData = array()) {
		$data['objectID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.news', $data['objectID']);

		return parent::import($oldID, $data);
	}
}
