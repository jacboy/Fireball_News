<?php
namespace cms\page;

use cms\system\counter\VisitCountHandler;
use cms\data\news\News;
use cms\data\news\NewsAction;
use cms\data\news\NewsEditor;
use cms\data\news\NewsList;
use cms\data\news\ViewableNews;
use wcf\page\AbstractPage;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\comment\CommentHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\like\LikeHandler;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\MetaTagHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsPage extends AbstractPage {

	public $activeMenuItem = 'cms.page.news';

	public $enableTracking = true;

	public $newsID = 0;

	public $news = null;

	public $commentObjectTypeID = 0;

	public $commentManager = null;

	public $commentList = null;

	public $likeData = array();

	public $tags = array();

	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->newsID = intval($_REQUEST['id']);
		$this->news = ViewableNews::getNews($this->newsID);
		if ($this->news === null) {
			throw new IllegalLinkException();
		}

		// validate permissions
		if (!$this->news->canRead()) {
			throw new PermissionDeniedException();
		}
	}

	public function readData() {
		parent::readData();

		VisitCountHandler::getInstance()->count();
		WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('cms.page.news'), LinkHandler::getInstance()->getLink('NewsOverview', array(
			'application' => 'cms'
		))));

		if (CMS_NEWS_COMMENTS && $this->news->enableComments) {
			$this->commentObjectTypeID = CommentHandler::getInstance()->getObjectTypeID('de.codequake.cms.news.comment');
			$this->commentManager = CommentHandler::getInstance()->getObjectType($this->commentObjectTypeID)->getProcessor();
			$this->commentList = CommentHandler::getInstance()->getCommentList($this->commentManager, $this->commentObjectTypeID, $this->newsID);
		}

		$newsEditor = new NewsEditor($this->news->getDecoratedObject());
		$newsEditor->update(array(
			'clicks' => $this->news->clicks + 1
		));

		// get Tags
		if (MODULE_TAGGING) {
			$this->tags = $this->news->getTags();
		}
		if ($this->news->teaser != '') MetaTagHandler::getInstance()->addTag('description', 'description', $this->news->teaser);
		else MetaTagHandler::getInstance()->addTag('description', 'description', StringUtil::decodeHTML(StringUtil::stripHTML($this->news->getExcerpt())));

		if (! empty($this->tags)) MetaTagHandler::getInstance()->addTag('keywords', 'keywords', implode(',', $this->tags));
		MetaTagHandler::getInstance()->addTag('og:title', 'og:title', $this->news->subject . ' - ' . WCF::getLanguage()->get(PAGE_TITLE), true);
		MetaTagHandler::getInstance()->addTag('og:url', 'og:url', LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'object' => $this->news->getDecoratedObject()
		)), true);
		MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'article', true);
		if ($this->news->getImage() != null) MetaTagHandler::getInstance()->addTag('og:image', 'og:image', $this->news->getImage()->getLink(), true);
		// TODO: if ($this->news->getUserProfile()->facebook != '') MetaTagHandler::getInstance()->addTag('article:author', 'article:author', 'https://facebook.com/' . $this->news->getUserProfile()->facebook, true);
		if (FACEBOOK_PUBLIC_KEY != '') MetaTagHandler::getInstance()->addTag('fb:app_id', 'fb:app_id', FACEBOOK_PUBLIC_KEY, true);
		MetaTagHandler::getInstance()->addTag('og:description', 'og:description', StringUtil::decodeHTML(StringUtil::stripHTML($this->news->getExcerpt())), true);

		if ($this->news->isNew()) {
			$newsAction = new NewsAction(array(
				$this->news->getDecoratedObject()
			), 'markAsRead', array(
				'viewableNews' => $this->news
			));
			$newsAction->executeAction();
		}

		// fetch likes
		if (MODULE_LIKE) {
			$objectType = LikeHandler::getInstance()->getObjectType('de.codequake.cms.likeableNews');
			LikeHandler::getInstance()->loadLikeObjects($objectType, array(
				$this->newsID
			));
			$this->likeData = LikeHandler::getInstance()->getLikeObjects($objectType);
		}
	}

	public function assignVariables() {
		parent::assignVariables();

		DashboardHandler::getInstance()->loadBoxes('de.codequake.cms.news.news', $this);

		WCF::getTPL()->assign(array(
			'newsID' => $this->newsID,
			'news' => $this->news,
			'newsLikeData' => $this->likeData,
			'tags' => $this->tags,
			'attachmentList' => $this->news->getAttachments(),
			'allowSpidersToIndexThisPage' => true,
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.codequake.cms.news.news'),
			'sidebarName' => 'de.codequake.cms.news.news'
		));

		if (CMS_NEWS_COMMENTS && $this->enableComments) {
			WCF::getTPL()->assign(array(
				'commentCanAdd' => (WCF::getUser()->userID && WCF::getSession()->getPermission('user.cms.news.canAddComment')),
				'commentList' => $this->commentList,
				'commentObjectTypeID' => $this->commentObjectTypeID,
				'lastCommentTime' => ($this->commentList ? $this->commentList->getMinCommentTime() : 0),
				'likeData' => ((MODULE_LIKE && $this->commentList) ? $this->commentList->getLikeData() : array()),
			));
		}
	}

	public function getObjectType() {
		return 'de.codequake.cms.news';
	}

	public function getObjectID() {
		return $this->newsID;
	}
}
