<?php
namespace cms\data\news\update;
use cms\data\news\News;
use wcf\data\AbstractDatabaseObjectAction;
use cms\data\news\NewsEditor;

/**
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsUpdateAction extends AbstractDatabaseObjectAction {
	protected $className = 'cms\data\news\update\NewsUpdateEditor';
	protected $permissionsCreate = array('user.cms.news.canAddNews');
	protected $permissionsUpdate = array('user.cms.news.canAddNews');
	protected $permissionsDelete = array('user.cms.news.canAddNews');

	public function create() {
		$newsUpdate = $parent::create();

		//change news edit values
		$news = new News($newsUpdate->newsID);
		$update = array(
			'lastChangeTime' => $newsUpdate->time,
			'lastEditorID' => $newsUpdate->userID,
			'lastEditor' => $newsUpdate->username
		);
		$editor = new NewsEditor($news);
		$editor->update($update);

		return $newsUpdate;
	}
}
