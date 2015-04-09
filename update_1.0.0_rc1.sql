DROP TABLE IF EXISTS cms1_news_image;

ALTER TABLE cms1_news CHANGE imageID imageID INT(10) NULL DEFAULT NULL;

--set new foreign key
ALTER TABLE cms1_news ADD FOREIGN KEY (imageID) REFERENCES cms1_file (fileID) ON DELETE SET NULL;