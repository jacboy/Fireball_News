
--news
DROP TABLE IF EXISTS cms1_news;
CREATE TABLE cms1_news (
	newsID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	userID INT(10),
	username VARCHAR(255),
	subject VARCHAR(255),
	teaser MEDIUMTEXT,
	message MEDIUMTEXT,
	time INT(10) NOT NULL DEFAULT 0,
	attachments INT(10) NOT NULL DEFAULT 0,
	pollID INT(10),
	languageID INT(10),
	clicks INT(10) NOT NULL DEFAULT 0,
	comments SMALLINT(5) NOT NULL DEFAULT 0,
	imageID INT(10) NOT NULL DEFALUT 0,
	enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
	enableHtml TINYINT(1) NOT NULL DEFAULT 0,
	enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
	showSignature TINYINT (1) NOT NULL DEFAULT 0,
	isDisabled TINYINT(1) NOT NULL DEFAULT 0,
	isDeleted TINYINT(1) NOT NULL DEFAULT 0,
	deleteTime INT(10) NOT NULL DEFAULT 0,
	lastChangeTime INT(10) NOT NULL DEFAULT 0,
	lastEditor VARCHAR (255) NOT NULL DEFAULT '',
	lastEditorID INT(10) NOT NULL DEFAULT 0,
	ipAddress VARCHAR(39) NOT NULL DEFAULT '',
	cumulativeLikes INT(10) NOT NULL DEFAULT 0
);

--news updates
DROP TABLE IF EXISTS cms1_news_update;
CREATE TABLE cms1_news_update (
	updateID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	newsID INT(10),
	title VARCHAR(255) NOT NULL,
	message VARCHAR(255) NOT NULL,
	userID INT(10),
	username VARCHAR(255),
	time INT(20) NOT NULL DEFAULT 0
);

--news images
DROP TABLE IF EXISTS cms1_news_image;
CREATE TABLE cms1_news_image (
	imageID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(255) NOT NULL,
	filename VARCHAR(255) NOT NULL
);

--news to category
DROP TABLE IF EXISTS cms1_news_to_category;
CREATE TABLE cms1_news_to_category (
	categoryID INT(10) NOT NULL,
	newsID INT(10) NOT NULL,

	PRIMARY KEY (categoryID, newsID)
);

--foreign keys
ALTER TABLE cms1_news ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE cms1_news ADD FOREIGN KEY (languageID) REFERENCES wcf1_language (languageID) ON DELETE SET NULL;
ALTER TABLE cms1_news ADD FOREIGN KEY (pollID) REFERENCES wcf1_poll (pollID) ON DELETE SET NULL;

ALTER TABLE cms1_news_update ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE cms1_news_update ADD FOREIGN KEY (newsID) REFERENCES cms1_news (newsID) ON DELETE SET NULL;

ALTER TABLE cms1_news_to_category ADD FOREIGN KEY (categoryID) REFERENCES wcf1_category (categoryID) ON DELETE CASCADE;
ALTER TABLE cms1_news_to_category ADD FOREIGN KEY (newsID) REFERENCES cms1_news (newsID) ON DELETE CASCADE;
