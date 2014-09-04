<?php
namespace cms\data\news;

use cms\data\category\NewsCategory;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Filters news by accessibilty
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class AccessibleNewsList extends ViewableNewsList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$sqlOrderBy
	 */
	public $sqlOrderBy = 'news.time DESC';

	public function __construct(){
		parent::__construct();

		// accessible news categories
		$accessibleCategoryIDs = NewsCategory::getAccessibleCategoryIDs();
		if (!empty($accessibleCategoryIDs)) $this->getConditionBuilder()->add('news.newsID IN (SELECT newsID FROM cms'.WCF_N.'_news_to_category WHERE categoryID IN (?))', array($accessibleCategoryIDs));
		else $this->getConditionBuilder()->add('1=0');

		//get default settings
		if (! WCF::getSession()->getPermission('mod.cms.news.canModerateNews')) $this->getConditionBuilder()->add('news.isDisabled = 0');
		if (! WCF::getSession()->getPermission('mod.cms.news.canModerateNews')) $this->getConditionBuilder()->add('news.isDeleted = 0');

		//can view delayed news
		if (! WCF::getSession()->getPermission('user.cms.news.canViewDelayedNews')) {
			$this->getConditionBuilder()->add('news.isDisabled = ?', array(
				0
			));
		}

		// language Filter
		if (LanguageFactory::getInstance()->multilingualismEnabled() && count(WCF::getUser()->getLanguageIDs())) {
			$this->getConditionBuilder()->add('(news.languageID IN (?) OR news.languageID IS NULL)', array(
				WCF::getUser()->getLanguageIDs()
			));
		}
	}
}
