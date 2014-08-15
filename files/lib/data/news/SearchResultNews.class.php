<?php
namespace cms\data\news;

use wcf\data\search\ISearchResultObject;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\request\LinkHandler;
use wcf\system\search\SearchResultTextParser;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class SearchResultNews extends ViewableNews implements ISearchResultObject {

	public $userProfile = null;

	public function getFormattedMessage() {
		return SearchResultTextParser::getInstance()->parse($this->getDecoratedObject()->getExcerpt());
	}

	public function getSubject() {
		return $this->subject;
	}

	public function getLink($query = '') {
		if ($query) {
			return LinkHandler::getInstance()->getLink('News', array(
				'application' => 'cms',
				'object' => $this->getDecoratedObject(),
				'highlight' => urlencode($query)
			));
		}
		return $this->getDecoratedObject()->getLink();
	}

	public function getTime() {
		return $this->time;
	}

	public function getObjectTypeName() {
		return 'de.codequake.cms.news';
	}

	public function getContainerTitle() {
		return '';
	}

	public function getContainerLink() {
		return '';
	}

	public function getUserProfile() {
		if ($this->userProfile === null) {
			$this->userProfile = new UserProfile(new User($this->userID));
		}

		return $this->userProfile;
	}
}
