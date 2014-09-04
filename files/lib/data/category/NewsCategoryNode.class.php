<?php
namespace cms\data\category;

use wcf\data\category\CategoryNode;

/**
 * Represents a news category node.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCategoryNode extends CategoryNode {

	protected static $baseClass = 'cms\data\category\NewsCategory';

	protected $unreadNews = null;

	protected $news = null;

	public function getUnreadNews() {
		if ($this->unreadNews === null) $this->unreadNews = NewsCategoryCache::getInstance()->getUnreadNews($this->categoryID);
		return $this->unreadNews;
	}

	public function getNews() {
		if ($this->news === null) $this->news = NewsCategoryCache::getInstance()->getNews($this->categoryID);
		return $this->news;
	}
}
