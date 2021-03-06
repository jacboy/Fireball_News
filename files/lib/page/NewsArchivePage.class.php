<?php
namespace cms\page;

use wcf\page\SortablePage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsArchivePage extends SortablePage {

	public $activeMenuItem = 'cms.page.news.archive';

	public $enableTracking = true;

	public $itemsPerPage = CMS_NEWS_PER_PAGE;

	public $limit = 10;

	public $categoryList = null;

	public $defaultSortField = 'time';

	public $defaultSortOrder = 'DESC';
	
	public $objectListClassName = 'cms\data\news\AccessibleNewsList';

	public $validSortFields = array(
		'subject',
		'time',
		'clicks'
	);

	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'allowSpidersToIndexThisPage' => true,
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.codequake.cms.news'))
		));
	}

	public function getObjectType() {
		return 'de.codequake.cms.news';
	}
}
