INSERT INTO books(title, descr)
values
('Bible','A bible'),
('Do Androids Dream of Electric Sheep?','Some book from intenet'),
('Are You There, Vodka?','???')
;

INSERT INTO genres(title, descr)
values
('Fiction','For fiction books'),
('Religion', NULL),
('IT', NULL),
('SCI-FI', NULL),
('Comedy','?')
;

INSERT INTO authors(first_name, last_name)
values
('People',''),
('Reptiloids',''),
('Philip', 'K. Dick'),
('Chelsea','Handler')
;

INSERT INTO genres_books(genre_id, book_id)
values
(2,1),
(3,2),
(4,2),
(5,3)
;

INSERT INTO authors_books(author_id, book_id)
values
(1,1),
(1,2),
(3,2),
(2,3),
(4,3)
;