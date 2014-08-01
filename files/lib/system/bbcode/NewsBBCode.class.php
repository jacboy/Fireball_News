<?php
namespace cms\system\bbcode;

use cms\data\news\ViewableNews;
use wcf\system\bbcode\AbstractBBCode;
use wcf\system\bbcode\BBCodeParser;
use wcf\system\WCF;

/**
 * handles the news bbcode
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class NewsBBCode extends AbstractBBCode {

	public function getParsedTag(array $openingTag, $content, array $closingTag, BBCodeParser $parser) {
		//get id attribute
		if (isset($openingTag['attributes'][0])) {
			$newsID = $openingTag['attributes'][0];
		}
		$news = ViewableNews::getNews($newsID);

		if ($news === null) return '';

		WCF::getTPL()->assign(array(
			'_news' => $news
		));

		return WCF::getTPL()->fetch('newsBBCodeTag', 'cms');
	}
}
