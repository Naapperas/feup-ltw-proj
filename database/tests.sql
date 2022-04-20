-- note: this file is for testing purposes only

-- tests for trigger #1

INSERT INTO "User" VALUES (1, "nome", "pass", "addr", 1234, 1, 1, 1); -- owner
INSERT INTO "User" VALUES (2, "nome", "pass", "addr", 1234, 0, 1, 1); -- not owner

INSERT INTO "Restaurant" VALUES (1, "nomeres", "addrres", 1);
INSERT INTO "Restaurant" VALUES (2, "nomeres2", "addrres2", 2);

-- tests for trigger #2

INSERT INTO "User" VALUES (3, "nome", "pass", "addr", 1234, 1, 1, 1); -- client
INSERT INTO "User" VALUES (4, "nome", "pass", "addr", 1234, 1, 0, 1); -- not client

INSERT INTO "Restaurant" VALUES (3, "nomeres3", "addrres3", 1);

INSERT INTO "Favorite_restaurant" VALUES (3, 3); -- should pass
INSERT INTO "Favorite_restaurant" VALUES (4, 3); -- should fail

-- tests for trigger #3

INSERT INTO "User" VALUES (5, "nome", "pass", "addr", 1234, 1, 1, 1); -- driver
INSERT INTO "User" VALUES (6, "nome", "pass", "addr", 1234, 1, 1, 0); -- not driver

INSERT INTO "Order" VALUES (1, "state1", 0, 5); -- should pass
INSERT INTO "Order" VALUES (2, "state2", 0, 6); -- should fail

-- tests for trigger #4

INSERT INTO "User" VALUES (7, "nome", "pass", "addr", 1234, 1, 1, 1); -- client
INSERT INTO "User" VALUES (8, "nome", "pass", "addr", 1234, 1, 0, 1); -- not client

INSERT INTO "Restaurant" VALUES (4, "nomeres4", "addrres4", 7);

INSERT INTO "Dish" VALUES (1, "nome1", 10.0, 4);

INSERT INTO "Favorite_dish" VALUES (7, 1); -- should pass
INSERT INTO "Favorite_dish" VALUES (8, 1); -- should fail

-- tests for trigger #5

INSERT INTO "User" VALUES (9, "nome", "pass", "addr", 1234, 1, 1, 1); -- client
INSERT INTO "User" VALUES (10, "nome", "pass", "addr", 1234, 1, 0, 1); -- not client

INSERT INTO "Restaurant" VALUES (5, "nomerees5", "addrres5", 9);

INSERT INTO "Review" VALUES (1, 5, "desc", 9, 5); -- should pass
INSERT INTO "Review" VALUES (2, 4, "desc1", 10, 5); -- should fail
