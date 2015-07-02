<?php
namespace cms\form;

use cms\data\news\News;
use cms\data\news\NewsAction;
use cms\data\news\NewsEditor;
use wcf\form\MessageForm;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\exception\IllegalLinkException;
use wcf\system\poll\PollManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;

/**
 * Shows the news edit form.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsEditForm extends NewsAddForm {

	public $newsID = 0;

	public $news = null;

	public $templateName = 'newsAdd';

	public $action = 'edit';

	public $tags = array();

	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['id'])) $this->newsID = intval($_REQUEST['id']);
		if ($this->newsID == 0) throw new IllegalLinkException();

		// set attachment object id
		$this->attachmentObjectID = $this->newsID;

	}

	public function readData() {
		parent::readData();
		$this->news = new News($this->newsID);

		if (WCF::getSession()->getPermission('user.cms.news.canStartPoll') && MODULE_POLL) PollManager::getInstance()->setObject('de.codequake.cms.news', $this->news->newsID, $this->news->pollID);

		$time = $this->news->time;
		$dateTime = DateUtil::getDateTimeByTimestamp($time);
		$dateTime->setTimezone(WCF::getUser()->getTimeZone());
		$this->time = $dateTime->format('c');

		$this->subject = $this->news->subject;
		$this->teaser = $this->news->teaser;
		$this->text = $this->news->message;
		$this->enableBBCodes = $this->news->enableBBCodes;
		$this->enableHtml = $this->news->enableHtml;
		$this->enableSmilies = $this->news->enableSmilies;
		$this->imageID = $this->news->imageID;

		$usernames = array();
		foreach ($this->news->getAuthors() as $author) {
			$usernames[] = $author->username;
		}
		$this->authors = implode(', ', $usernames);

		if (CMS_NEWS_COMMENTS) {
			$this->enableComments = (bool) $this->news->enableComments;
		}

		WCF::getBreadcrumbs()->add(new Breadcrumb($this->news->subject, LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'object' => $this->news
		))));

		foreach ($this->news->getCategories() as $category) {
			$this->categoryIDs[] = $category->categoryID;
		}

		// tagging
		if (MODULE_TAGGING) {
			$tags = $this->news->getTags();
			foreach ($tags as $tag) {
				$this->tags[] = $tag->name;
			}
		}
	}

	public function save() {
		MessageForm::save();

		if ($this->time != '') $dateTime = \DateTime::createFromFormat("Y-m-d H:i", $this->time, WCF::getUser()->getTimeZone());

		$data = array(
			'subject' => $this->subject,
			'message' => $this->text,
			'teaser' => $this->teaser,
			'time' => ($this->time != '') ? $dateTime->getTimestamp(): TIME_NOW,
			'enableBBCodes' => $this->enableBBCodes,
			'showSignature' => $this->showSignature,
			'enableHtml' => $this->enableHtml,
			'imageID' => $this->imageID ?: null,
			'enableSmilies' => $this->enableSmilies,
			'lastChangeTime' => TIME_NOW,
			'isDisabled' => ($this->time != '' && $dateTime->getTimestamp() > TIME_NOW) ? 1 : 0,
			'lastEditor' => WCF::getUser()->username,
			'lastEditorID' => WCF::getUser()->userID
		);

		if (CMS_NEWS_COMMENTS) {
			$data['enableComments'] = $this->enableComments ? 1 : 0;
		}

		$newsData = array(
			'attachmentHandler' => $this->attachmentHandler,
			'authorIDs' => $this->authorIDs,
			'categoryIDs' => $this->categoryIDs,
			'data' => $data,
			'tags' => $this->tags,
		);

		$action = new NewsAction(array(
			$this->newsID
		), 'update', $newsData);
		$resultValues = $action->executeAction();
		$this->saved();
		// re-define after saving
		$this->news = new News($this->newsID);

		if (WCF::getSession()->getPermission('user.cms.news.canStartPoll') && MODULE_POLL) {
			$pollID = PollManager::getInstance()->save($this->news->newsID);
			if ($pollID && $pollID != $this->news->pollID) {
				$editor = new NewsEditor($this->news);
				$editor->update(array(
					'pollID' => $pollID
				));

			} else if (! $pollID && $this->news->pollID) {
				$editor = new NewsEditor($this->news);
				$editor->update(array(
					'pollID' => null
				));

			}
		}

		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'object' => $this->news
		)));
		exit;
	}

	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'news' => $this->news,
			'newsID' => $this->newsID
		));
	}
}
