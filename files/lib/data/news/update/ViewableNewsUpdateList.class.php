<?php
namespace cms\data\news\update;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ViewableNewsUpdateList extends NewsUpdateList {

	public $decoratorClassName = 'cms\data\news\update\ViewableNewsUpdate';

	public function __construct() {
		parent::__construct();

		// get user
		if (!empty($this->sqlSelects)) $this->sqlSelects .= ', ';
		$this->sqlSelects .= "user_table.*";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_user user_table ON (user_table.userID = news_update.userID)";
	}
}
