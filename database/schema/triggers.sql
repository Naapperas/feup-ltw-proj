PRAGMA FOREIGN_KEYS = ON;

DROP TRIGGER IF EXISTS "User_is_owner";
DROP TRIGGER IF EXISTS "Client_favorite_restaurant";
DROP TRIGGER IF EXISTS "User_is_driver";
DROP TRIGGER IF EXISTS "Client_favorite_dish";
DROP TRIGGER IF EXISTS "Client_review";
DROP TRIGGER IF EXISTS "Menu_add_dish";
DROP TRIGGER IF EXISTS "Menu_dish_same_restaurant";

-- Verify a user is an owner

CREATE TRIGGER IF NOT EXISTS "User_is_owner"
BEFORE INSERT ON "Restaurant"
WHEN NOT EXISTS (SELECT * FROM "User" WHERE "id" = "New"."owner" AND "is_owner" = 1)
BEGIN
    SELECT raise(ABORT, "Can't insert restaurant with non-owner user");
END;

-- Only clients can have a favorite restaurant

CREATE TRIGGER IF NOT EXISTS "Client_favorite_restaurant"
BEFORE INSERT ON "Favorite_restaurant"
WHEN NOT EXISTS (SELECT * FROM "User" WHERE "id" = "New"."client" AND "is_client" = 1)
BEGIN
    SELECT raise(ABORT, "Only clients can have favorite restaurants");
END;

-- Only drivers can deliver orders

CREATE TRIGGER IF NOT EXISTS "User_is_driver"
BEFORE INSERT ON "Order"
WHEN NOT EXISTS (SELECT * FROM "User" WHERE "id" = "New"."driver" AND "is_driver" = 1)
BEGIN
    SELECT raise(ABORT, "Only drivers can deliver orders");
END;

-- Only clients can have favorite dishes

CREATE TRIGGER IF NOT EXISTS "Client_favorite_dish"
BEFORE INSERT ON "Favorite_dish"
WHEN NOT EXISTS (SELECT * FROM "User" WHERE "id" = "New"."client" AND "is_client" = 1)
BEGIN
    SELECT raise(ABORT, "Only clients can have favorite dishes");
END;

-- Only clients can leave reviews

CREATE TRIGGER IF NOT EXISTS "Client_review"
BEFORE INSERT ON "Review"
WHEN NOT EXISTS (SELECT * FROM "User" WHERE "id" = "New"."client" AND "is_client" = 1)
BEGIN
    SELECT raise(ABORT, "Only clients can leave reviews");
END;

CREATE TRIGGER IF NOT EXISTS "Menu_dish_same_restaurant"
BEFORE INSERT ON "Dish_menu"
WHEN (SELECT restaurant FROM "Dish" WHERE id = NEW.dish) <> (SELECT restaurant FROM "Menu" WHERE id = NEW.menu)
BEGIN
    SELECT raise(ABORT, "Dish and Menu must belong to the same restaurant");
END;

CREATE TRIGGER IF NOT EXISTS "Menu_add_dish"
AFTER INSERT ON "Dish_menu"
BEGIN
    UPDATE Menu SET price = price + (SELECT price FROM "Dish" WHERE "id" = NEW.dish) WHERE id = NEW.menu;
END;
