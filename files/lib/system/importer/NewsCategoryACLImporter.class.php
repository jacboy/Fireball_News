<?php
namespace cms\system\importer;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractACLImporter;

/**
 * @author	Florian Gail
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCategoryACLImporter extends AbstractACLImporter {
	/**
	 * @see	\wcf\system\importer\AbstractACLImporter::$objectTypeName
	 */
	protected $objectTypeName = 'de.codequake.cms.category.news';

	/**
	 * Creates a new CategoryACLImporter object.
	 */
	public function __construct() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.acl', 'de.codequake.cms.category.news');
		$this->objectTypeID = $objectType->objectTypeID;

		parent::__construct();
	}
}
