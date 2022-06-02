PRAGMA FOREIGN_KEYS = ON;

DROP TRIGGER IF EXISTS "Menu_add_dish";
DROP TRIGGER IF EXISTS "Menu_dish_same_restaurant";
DROP TRIGGER IF EXISTS "Restaurant_update_score";

-- Dishes of a menu must belong to the menu's restaurant

CREATE TRIGGER IF NOT EXISTS "Menu_dish_same_restaurant"
BEFORE INSERT ON "Dish_menu"
WHEN (SELECT "restaurant" FROM "Dish" WHERE "id" = "New"."dish") <> (SELECT "restaurant" FROM "Menu" WHERE "id" = "New"."menu")
BEGIN
    SELECT raise(ABORT, "Dish and Menu must belong to the same restaurant");
END;

-- Update menu's price after a new dish is added to that menu

CREATE TRIGGER IF NOT EXISTS "Menu_add_dish"
AFTER INSERT ON "Dish_menu"
BEGIN
    UPDATE "Menu" SET "price" = "price" + (SELECT "price" FROM "Dish" WHERE "id" = "New"."dish") WHERE "id" = "New"."menu";
END;

-- UPdates Restaurant average score on Review creation

CREATE TRIGGER IF NOT EXISTS "Restaurant_update_score"
AFTER INSERT ON "Review"
BEGIN
    UPDATE "Restaurant" SET "score" = (SELECT avg("score") AS "score" FROM "Review" WHERE "restaurant" = "New"."restaurant") WHERE "id" = "New"."restaurant";
END;