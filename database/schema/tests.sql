-- note: this file is for testing purposes only

.mode column
.headers on

-- tests for trigger #1

INSERT INTO "User" VALUES (1, "nome1", "email1", "pass", "addr", "1", "oi"); -- owner
INSERT INTO "User" VALUES (2, "nome2", "email2", "pass", "addr", "12", "oi"); -- not owner

INSERT INTO "Restaurant" VALUES (1, "nomeres", "address", 1);
INSERT INTO "Restaurant" VALUES (2, "nomeres2", "address2", 2);

-- tests for trigger #2

INSERT INTO "User" VALUES (3, "nome3", "email3", "pass", "addr", "123", "oi"); -- client
INSERT INTO "User" VALUES (4, "nome4", "email4", "pass", "addr", "1234", "oi"); -- not client

INSERT INTO "Restaurant" VALUES (3, "nomeres3", "address3", 1);

INSERT INTO "Favorite_restaurant" VALUES (3, 3); -- should pass
INSERT INTO "Favorite_restaurant" VALUES (4, 3); -- should fail

-- tests for trigger #3

INSERT INTO "User" VALUES (5, "nome5", "email5", "pass", "addr", "12345", "oi"); -- driver
INSERT INTO "User" VALUES (6, "nome6", "email6", "pass", "addr", "123456", "oi"); -- not driver

INSERT INTO "Order" VALUES (1, "state1", 0, 6, 5); -- should pass
INSERT INTO "Order" VALUES (2, "state2", 0, 6, 6); -- should fail

-- tests for trigger #4

INSERT INTO "User" VALUES (7, "nome7", "email7", "pass", "addr", "1234567", "oi"); -- client
INSERT INTO "User" VALUES (8, "nome8", "email8", "pass", "addr", "12345678", "oi"); -- not client

INSERT INTO "Restaurant" VALUES (4, "nomeres4", "address4", 7);

INSERT INTO "Dish" VALUES (1, "nome1", 10.0, 4);

INSERT INTO "Favorite_dish" VALUES (7, 1); -- should pass
INSERT INTO "Favorite_dish" VALUES (8, 1); -- should fail

-- tests for trigger #5

INSERT INTO "User" VALUES (9, "nome9", "email9", "pass", "addr", "12340", "oi"); -- client
INSERT INTO "User" VALUES (10, "nome10", "email10", "pass", "addr", "01234", "oi"); -- not client

INSERT INTO "Restaurant" VALUES (5, "nomerees5", "adrress5", 9);

INSERT INTO "Review" VALUES (1, 5, "desc", 9, 5); -- should pass
INSERT INTO "Review" VALUES (2, 4, "desc1", 10, 5); -- should fail

-- tests for trigger #6

INSERT INTO Menu VALUES (1, "BrUHH", 4, 0);

INSERT INTO Dish_menu VALUES (1, 1); -- should pass

INSERT INTO Menu VALUES (2, "BrUHH", 1, 0);
INSERT INTO Dish_menu VALUES (1, 2); -- should fail

-- tests for trigger #8

INSERT INTO "Order" VALUES (3, "state", 0, 1, 5); -- should pass
INSERT INTO "Order" VALUES (4, "state", 0, 5, 5); -- shoud fail
