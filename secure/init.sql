BEGIN TRANSACTION;

-- tags > albums > album_tags

--TAGS: genre
CREATE TABLE `tags` (
    `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `genre` TEXT UNIQUE NOT NULL
);

--tags seed data
INSERT INTO `tags` (genre) VALUES ('Indie'); --1
INSERT INTO `tags` (genre) VALUES ('Pop'); --2
INSERT INTO `tags` (genre) VALUES ('Alternative'); --3
INSERT INTO `tags` (genre) VALUES ('Modern/Alt Rock'); --4
INSERT INTO `tags` (genre) VALUES ('Rock'); --5
INSERT INTO `tags` (genre) VALUES ('Contemporary R&B'); --6
INSERT INTO `tags` (genre) VALUES ('Neo Soul'); --7
INSERT INTO `tags` (genre) VALUES ('Hip Hop'); --8

--ALBUMS: title, file extension, citation
--artist_id
CREATE TABLE `albums` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`artist`	TEXT NOT NULL,
	`title`	TEXT NOT NULL,
    `file_ext` TEXT NOT NULL,
    `citation` TEXT NOT NULL
);

--ALBUM TAGS
--album_id, tag_id
CREATE TABLE `album_tags` (
    `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `album_id` INTEGER NOT NULL,
    `tag_id` INTEGER
);


--seed data
--1
INSERT INTO `albums` (artist,title,file_ext, citation) VALUES ('The Cranberries',"Everybody Else Is Doing It, So Why Can't We?",'jpg', 'https://consequenceofsound.net/wp-content/uploads/2012/01/the-cranberries-everybody-else-is-doing-it-so-why-cant-we-front.jpg');
-- Source: https://consequenceofsound.net/wp-content/uploads/2012/01/the-cranberries-everybody-else-is-doing-it-so-why-cant-we-front.jpg
INSERT INTO `album_tags` (album_id,tag_id) VALUES (last_insert_rowid(),1); -- indie

--2
INSERT INTO `albums` (artist,title,file_ext, citation) VALUES ('Peach Pit',"Sweet FA",'jpg', 'https://i.pinimg.com/originals/24/64/8b/24648b077fad74c819aa11bf7747910c.jpg'); --peach
-- Source: https://i.pinimg.com/originals/24/64/8b/24648b077fad74c819aa11bf7747910c.jpg
INSERT INTO `album_tags` (album_id,tag_id) VALUES (last_insert_rowid(),1); -- indie mod (multi)
INSERT INTO `album_tags` (album_id,tag_id) VALUES (2,4);

--3
INSERT INTO `albums` (artist,title,file_ext, citation) VALUES ('Japanese Breakfast',"Psychopomp",'jpg', 'https://upload.wikimedia.org/wikipedia/en/1/18/Japanese_Breakfast_-_Psychopomp.jpg'); --breakfast
--Source: https://upload.wikimedia.org/wikipedia/en/1/18/Japanese_Breakfast_-_Psychopomp.jpg
INSERT INTO `album_tags` (album_id,tag_id) VALUES (3,1); -- indie pop (multi)
INSERT INTO `album_tags` (album_id,tag_id) VALUES (3,2);

--4
INSERT INTO `albums` (artist,title,file_ext, citation) VALUES ('Rex Orange County',"Pony",'jpg', 'https://images-na.ssl-images-amazon.com/images/I/61t%2BSkvUg0L._SL1200_.jpg'); --rex
--Source: https://images-na.ssl-images-amazon.com/images/I/61t%2BSkvUg0L._SL1200_.jpg
INSERT INTO `album_tags` (album_id,tag_id) VALUES (4,2); -- pop alt neo (multi)
INSERT INTO `album_tags` (album_id,tag_id) VALUES (4,3);
INSERT INTO `album_tags` (album_id,tag_id) VALUES (4,7);

--5
INSERT INTO `albums` (artist,title,file_ext, citation) VALUES ('Ezra Furman',"Perpetual Motion People",'png', 'https://media.pitchfork.com/photos/5929af3a5e6ef95969321d84/1:1/w_600/15e7c082.png'); --ezra
--Source: https://media.pitchfork.com/photos/5929af3a5e6ef95969321d84/1:1/w_600/15e7c082.png
INSERT INTO `album_tags` (album_id,tag_id) VALUES (5,2); -- mod pop (multi)
INSERT INTO `album_tags` (album_id,tag_id) VALUES (5,4);

--6
INSERT INTO `albums` (artist,title,file_ext, citation) VALUES ('Frank Ocean',"Blonde",'jpeg', 'https://upload.wikimedia.org/wikipedia/en/a/a0/Blonde_-_Frank_Ocean.jpeg'); --frank
--Source: https://upload.wikimedia.org/wikipedia/en/a/a0/Blonde_-_Frank_Ocean.jpeg
INSERT INTO `album_tags` (album_id,tag_id) VALUES (6,6); -- r&b pop alt neo hiphop(3+ tags)
INSERT INTO `album_tags` (album_id,tag_id) VALUES (6,2);
INSERT INTO `album_tags` (album_id,tag_id) VALUES (6,3);
INSERT INTO `album_tags` (album_id,tag_id) VALUES (6,7);
INSERT INTO `album_tags` (album_id,tag_id) VALUES (6,8);

--7
INSERT INTO `albums` (artist,title,file_ext, citation) VALUES ('The Head and the Heart',"Living Mirage",'jpg', 'https://images-na.ssl-images-amazon.com/images/I/81VYErT7S5L._SL1425_.jpg'); --head
--Source: https://images-na.ssl-images-amazon.com/images/I/81VYErT7S5L._SL1425_.jpg
INSERT INTO `album_tags` (album_id,tag_id) VALUES (7,1); -- indie mod pop (3+ tags)
INSERT INTO `album_tags` (album_id,tag_id) VALUES (7,2);
INSERT INTO `album_tags` (album_id,tag_id) VALUES (7,3);

--8
INSERT INTO `albums` (artist,title,file_ext, citation) VALUES ('Fleetwood Mac',"Fleetwood Mac",'png', 'https://upload.wikimedia.org/wikipedia/en/f/fb/FMacRumours.PNG'); --fmac
--Source: https://upload.wikimedia.org/wikipedia/en/f/fb/FMacRumours.PNG
INSERT INTO `album_tags` (album_id,tag_id) VALUES (8,5); -- rock

--9
INSERT INTO `albums` (artist,title,file_ext, citation) VALUES ('Harry Styles',"Fine Line",'jpeg', 'https://video-images.vice.com/_uncategorized/1572958347277-EIi0CiOWwAI_9l0.jpeg'); --harry
--Source: https://video-images.vice.com/_uncategorized/1572958347277-EIi0CiOWwAI_9l0.jpeg
INSERT INTO `album_tags` (album_id,tag_id) VALUES (9,2); -- pop

--10
INSERT INTO `albums` (artist,title,file_ext, citation) VALUES ('alt-J',"An Awesome Wave",'png', 'https://upload.wikimedia.org/wikipedia/en/d/d0/Alt-J_-_An_Awesome_Wave.png'); --alt
--Source: https://upload.wikimedia.org/wikipedia/en/d/d0/Alt-J_-_An_Awesome_Wave.png
INSERT INTO `album_tags` (album_id,tag_id) VALUES (10,3); -- alt

--11
INSERT INTO `albums` (artist,title,file_ext, citation) VALUES ('Jhené Aiko',"Chilombo",'png', 'https://upload.wikimedia.org/wikipedia/en/1/15/Jhen%C3%A9_Aiko_-_Chilombo.png');
-- Source: https://upload.wikimedia.org/wikipedia/en/1/15/Jhen%C3%A9_Aiko_-_Chilombo.png
INSERT INTO `album_tags` (album_id,tag_id) VALUES (last_insert_rowid(),6);
INSERT INTO `album_tags` (album_id,tag_id) VALUES (11,2);
INSERT INTO `album_tags` (album_id,tag_id) VALUES (11,3);
INSERT INTO `album_tags` (album_id,tag_id) VALUES (11,7);
INSERT INTO `album_tags` (album_id,tag_id) VALUES (11,8);

COMMIT;
