<?php
namespace cms\form;

use cms\data\category\NewsCategory;
use cms\data\category\NewsCategoryNodeTree;
use cms\data\file\FileCache;
use cms\data\news\NewsAction;
use cms\data\news\NewsEditor;
use wcf\data\user\UserList;
use wcf\form\MessageForm;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\poll\PollManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows the news add form.
 *
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsAddForm extends MessageForm {

	public $action = 'add';

	/**
	 * Comma seperated list of news authors.
	 * 
	 * @var	string
	 */
	public $authors = '';

	/**
	 * List of all author ids based on the given usernames.
	 * 
	 * @var int[]
	 */
	public $authorIDs;

	public $categoryIDs = array();

	public $categoryList = array();

	public $activeMenuItem = 'cms.page.news';

	public $enableTracking = true;

	public $neededPermissions = array(
		'user.cms.news.canAddNews'
	);

	public $enableMultilingualism = true;

	public $attachmentObjectType = 'de.codequake.cms.news';

	public $imageID = 0;
	
	public $image = null;

	public $time = '';

	public $teaser = '';

	public $tags = array();

	public function readParameters() {
		parent::readParameters();

		// polls
		if (MODULE_POLL & WCF::getSession()->getPermission('user.cms.news.canStartPoll')) PollManager::getInstance()->setObject('de.codequake.cms.news', 0);
		if (isset($_REQUEST['categoryIDs']) && is_array($_REQUEST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_REQUEST['categoryIDs']);
	}

	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['authors'])) $this->authors = StringUtil::trim($_POST['authors']);
		if (isset($_POST['tags']) && is_array($_POST['tags'])) $this->tags = ArrayUtil::trim($_POST['tags']);
		if (isset($_POST['time'])) $this->time = $_POST['time'];
		if (isset($_POST['imageID'])) $this->imageID = intval($_POST['imageID']);
		if (isset($_POST['teaser'])) $this->teaser = StringUtil::trim($_POST['teaser']);

		if (MODULE_POLL && WCF::getSession()->getPermission('user.cms.news.canStartPoll')) PollManager::getInstance()->readFormParameters();
	}

	public function readData() {
		parent::readData();

		WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('cms.page.news'), LinkHandler::getInstance()->getLink('NewsOverview', array(
			'application' => 'cms'
		))));

		$excludedCategoryIDs = array_diff(NewsCategory::getAccessibleCategoryIDs(), NewsCategory::getAccessibleCategoryIDs(array(
			'canAddNews'
		)));
		$categoryTree = new NewsCategoryNodeTree('de.codequake.cms.category.news', 0, false, $excludedCategoryIDs);
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);

		if (empty($_POST)) {
			$dateTime = DateUtil::getDateTimeByTimestamp(TIME_NOW);
			$dateTime->setTimezone(WCF::getUser()->getTimeZone());
			$this->time = $dateTime->format('c');
		}
		else {
			$dateTime = DateUtil::getDateTimeByTimestamp(@strtotime($this->time));
			$dateTime->setTimezone(WCF::getUser()->getTimeZone());
			$this->time = $dateTime->format('c');
		}

		// default values
		if (empty($_POST)) {
			$this->authors = WCF::getUser()->username;

			// multilingualism
			if (!empty($this->availableContentLanguages)) {
				if ($this->languageID) {
					$language = LanguageFactory::getInstance()->getUserLanguage();
					$this->languageID = $language->languageID;
				}

				if (!isset($this->availableContentLanguages[$this->languageID])) {
					$languageIDs = array_keys($this->availableContentLanguages);
					$this->languageID = array_shift($languageIDs);
				}
			}
		}
	}

	public function validate() {
		parent::validate();

		// categories
		if (empty($this->categoryIDs)) {
			throw new UserInputException('categoryIDs');
		}

		foreach ($this->categoryIDs as $categoryID) {
			$category = CategoryHandler::getInstance()->getCategory($categoryID);
			if ($category === null) throw new UserInputException('categoryIDs');

			$category = new NewsCategory($category);
			if (! $category->isAccessible() || ! $category->getPermission('canAddNews')) throw new UserInputException('categoryIDs');
		}

		$this->validateAuthors();

		if (MODULE_POLL && WCF::getSession()->getPermission('user.cms.news.canStartPoll')) PollManager::getInstance()->validate();
	}

	/**
	 * Validates the list of authors.
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	public function validateAuthors() {
		if (empty($this->authors)) {
			throw new UserInputException('authors');
		}

		$usernames = ArrayUtil::trim(explode(',', $this->authors));

		// silently ignore duplicate usernames
		$usernames = array_unique($usernames);

		$userList = new UserList();
		$userList->getConditionBuilder()->add('user_table.username IN (?)', array($usernames));
		$userList->readObjects();

		if (count($usernames) !== count($userList->getObjects())) {
			throw new UserInputException('authors', 'notFound');
		}

		foreach ($userList as $user) {
			$this->authorIDs[] = $user->userID;
		}
	}

	public function save() {
		parent::save();

		if ($this->time != '') $dateTime = \DateTime::createFromFormat("Y-m-d H:i", $this->time, WCF::getUser()->getTimeZone());

		$data = array_merge($this->additionalFields, array(
			'languageID' => $this->languageID,
			'subject' => $this->subject,
			'time' => ($this->time != '') ? $dateTime->getTimestamp(): TIME_NOW,
			'teaser' => $this->teaser,
			'message' => $this->text,
			'isDisabled' => ($this->time != '' && $dateTime->getTimestamp() > TIME_NOW) ? 1 : 0,
			'enableBBCodes' => $this->enableBBCodes,
			'showSignature' => $this->showSignature,
			'enableHtml' => $this->enableHtml,
			'enableSmilies' => $this->enableSmilies,
			'imageID' => $this->imageID ?: null,
			'lastChangeTime' => TIME_NOW
		));

		$newsData = array(
			'attachmentHandler' => $this->attachmentHandler,
			'authorIDs' => $this->authorIDs,
			'categoryIDs' => $this->categoryIDs,
			'data' => $data,
			'tags' => $this->tags
		);

		$action = new NewsAction(array(), 'create', $newsData);
		$resultValues = $action->executeAction();

		// save polls
		if (WCF::getSession()->getPermission('user.cms.news.canStartPoll') && MODULE_POLL) {
			$pollID = PollManager::getInstance()->save($resultValues['returnValues']->newsID);
			if ($pollID) {
				$editor = new NewsEditor($resultValues['returnValues']);
				$editor->update(array(
					'pollID' => $pollID
				));

			}
		}

		$this->saved();

		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'object' => $resultValues['returnValues']
		)));
		exit;
	}

	public function assignVariables() {
		parent::assignVariables();

		if (WCF::getSession()->getPermission('user.cms.news.canStartPoll') && MODULE_POLL) PollManager::getInstance()->assignVariables();
		if ($this->imageID && $this->imageID != 0) $this->image = FileCache::getInstance()->getFile($this->imageID);

		WCF::getTPL()->assign(array(
			'authors' => $this->authors,
			'categoryList' => $this->categoryList,
			'categoryIDs' => $this->categoryIDs,
			'imageID' => $this->imageID,
			'image' => $this->image,
			'teaser' => $this->teaser,
			'time' => $this->time,
			'action' => $this->action,
			'tags' => $this->tags,
			'allowedFileExtensions' => explode("\n", StringUtil::unifyNewlines(WCF::getSession()->getPermission('user.cms.news.allowedAttachmentExtensions')))
		));
	}
}
