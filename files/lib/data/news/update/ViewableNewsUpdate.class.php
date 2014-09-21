<?php
namespace cms\data\news\update;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\data\DatabaseObjectDecorator;

/**
 * Represents a viewable news update
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ViewableNewsUpdate extends DatabaseObjectDecorator {
	protected static $baseClass = 'cms\data\news\update\NewsUpdate';

	protected $userProfile = null;

	public function getUserProfile() {
		if ($this->userProfile === null) {
			$this->userProfile = new UserProfile(new User(null, $this->getDecoratedObject()->data));
		}

		return $this->userProfile;
	}
}
