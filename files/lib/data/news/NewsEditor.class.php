<?php
namespace cms\data\news;

use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Functions to edit a news.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsEditor extends DatabaseObjectEditor {
	/**
	 * {@inheritdoc}
	 */
	protected static $baseClass = 'cms\data\news\News';

	/**
	 * Updates the authors of this news to the given list of authors.
	 * 
	 * @param	int[]		$authorIDs
	 */
	public function updateAuthorIDs(array $authorIDs = array()) {
		// remove old authors
		$sql = 'DELETE FROM	cms'.WCF_N.'_news_author
			WHERE		newsID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->newsID));

		// assign new authors
		if (!empty($authorIDs)) {
			WCF::getDB()->beginTransaction();

			$sql = 'INSERT INTO	cms'.WCF_N.'_news_author
						(newsID, userID)
				VALUES		(?, ?)';
			$statement = WCF::getDB()->prepareStatement($sql);

			foreach ($authorIDs as $authorID) {
				$statement->execute(array($this->newsID, $authorID));
			}

			WCF::getDB()->commitTransaction();
		}
	}

	/**
	 * Updates the categories of this news to the given list of categories.
	 * 
	 * @param	int[]		$categoryIDs
	 */
	public function updateCategoryIDs(array $categoryIDs = array()) {
		// remove old assigns
		$sql = 'DELETE FROM	cms'.WCF_N.'_news_to_category
			WHERE		newsID = ?';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->newsID));

		// assign new categories
		if (!empty($categoryIDs)) {
			WCF::getDB()->beginTransaction();

			$sql = 'INSERT INTO	cms'.WCF_N.'_news_to_category
						(categoryID, newsID)
				VALUES		(?, ?)';
			$statement = WCF::getDB()->prepareStatement($sql);

			foreach ($categoryIDs as $categoryID) {
				$statement->execute(array($categoryID, $this->newsID));
			}

			WCF::getDB()->commitTransaction();
		}
	}
}
