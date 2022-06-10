PRAGMA FOREIGN_KEYS = ON;

DROP TRIGGER IF EXISTS "Menu_dish_same_restaurant";
DROP TRIGGER IF EXISTS "Restaurant_update_score";
DROP TRIGGER IF EXISTS "Remove_dish";
DROP TRIGGER IF EXISTS "Remove_restaurant";
DROP TRIGGER IF EXISTS "Remove_menu";

-- Dishes of a menu must belong to the menu's restaurant

CREATE TRIGGER IF NOT EXISTS "Menu_dish_same_restaurant"
BEFORE INSERT ON "Dish_menu"
WHEN (SELECT "restaurant" FROM "Dish" WHERE "id" = "New"."dish") <> (SELECT "restaurant" FROM "Menu" WHERE "id" = "New"."menu")
BEGIN
    SELECT raise(ABORT, "Dish and Menu must belong to the same restaurant");
END;

-- UPdates Restaurant average score on Review creation

CREATE TRIGGER IF NOT EXISTS "Restaurant_update_score"
AFTER INSERT ON "Review"
BEGIN
    UPDATE "Restaurant" SET "score" = (SELECT avg("score") AS "score" FROM "Review" WHERE "restaurant" = "New"."restaurant") WHERE "id" = "New"."restaurant";
END;

-- Deleting dishes should remove them from all favorites

CREATE TRIGGER IF NOT EXISTS "Remove_dish"
BEFORE DELETE ON "Dish"
BEGIN
    DELETE FROM "Favorite_dish" WHERE "dish" = "Old"."id";
    DELETE FROM "Dish_menu" WHERE "dish" = "Old"."id";
    DELETE FROM "Dish_order" WHERE "dish" = "Old"."id";
END;

-- Deleting menus should remove them from all favorites

CREATE TRIGGER IF NOT EXISTS "Remove_menu"
BEFORE DELETE ON "Menu"
BEGIN
    DELETE FROM "Menu_order" WHERE "menu" = "Old"."id";
END;

-- Deleting restaurants should remove them from all favorites

CREATE TRIGGER IF NOT EXISTS "Remove_restaurant"
BEFORE DELETE ON "Restaurant"
BEGIN
    DELETE FROM "Favorite_restaurant" WHERE "menu" = "Old"."id";
END;
