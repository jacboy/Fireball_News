<?php
namespace cms\data\news\update;

use cms\data\CMSDatabaseObject;
use wcf\system\bbcode\MessageParser;
use wcf\system\WCF;
use wcf\util\StringUtil;
/**
 * Represents a news update
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class NewsUpdate extends CMSDatabaseObject implements IMessage {

	protected static $databaseTableName = 'news_update';

	protected static $databaseTableIndexName = 'updateID';

	public function __construct($id, $row = null, $object = null) {
		if ($id !== null) {
			$sql = "SELECT *
					FROM " . static::getDatabaseTableName() . "
					WHERE (" . static::getDatabaseTableIndexName() . " = ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$id
			));
			$row = $statement->fetchArray();

			if ($row === false) $row = array();
		}

		parent::__construct(null, $row, $object);
	}

	public function getMessage() {
		return $this->message;
	}

	public function getFormattedMessage() {
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse($this->getMessage(), 1, 0, 1);
	}

	public function getExcerpt($maxLength = 255) {
		MessageParser::getInstance()->setOutputType('text/simplified-html');
		$message = MessageParser::getInstance()->parse($this->getMessage(), 1, 0, 1);
		return StringUtil::truncateHTML($message, $maxLength);
	}

	public function getTitle() {
		return $this->title;
	}

	public function getUserID() {
		return $this->userID;
	}

	public function getUsername() {
		return $this->username;
	}

}
