--news updates
DROP TABLE IF EXISTS cms1_news_update;
CREATE TABLE cms1_news_update (
	updateID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	newsID INT(10) NOT NULL,
	title VARCHAR(255) NOT NULL,
	message VARCHAR(255) NOT NULL,
	userID INT(10),
	username VARCHAR(255),
	time INT(20) NOT NULL DEFAULT 0,
	enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
	enableHtml TINYINT(1) NOT NULL DEFAULT 0,
	enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
);

ALTER TABLE cms1_news_update ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE cms1_news_update ADD FOREIGN KEY (newsID) REFERENCES cms1_news (newsID) ON DELETE SET NULL;
