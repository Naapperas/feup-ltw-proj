PRAGMA FOREIGN_KEYS = ON;

-- Verify is a user is an owner

CREATE TRIGGER IF NOT EXISTS "User_is_owner"
BEFORE INSERT ON "Restaurant"
FOR EACH ROW
WHEN EXISTS (
    SELECT "User"."is_owner" FROM "User" WHERE (
        "User"."id" = "New"."owner" AND "User"."is_owner" = 1
    )
)
BEGIN
    INSERT INTO "Restaurant" VALUES ("New"."id", "New"."name", "New"."address", "New"."owner");
END;

-- Only clients can have a favorite restaurant

CREATE TRIGGER IF NOT EXISTS "Client_favorite_restaurant"
BEFORE INSERT ON "Favorite_restaurant"
FOR EACH ROW
WHEN EXISTS (
    SELECT "User"."is_client" FROM "User", "Favorite_restaurant" WHERE (
        "User"."is_client" = 1 AND "User"."id" = "New"."client"
    )
)
BEGIN
    INSERT INTO "Favorite_restaurant" ("client") VALUES ("New"."client");
END;

-- Only drivers can deliver orders

CREATE TRIGGER IF NOT EXISTS "User_is_driver"
BEFORE INSERT ON "Order"
FOR EACH ROW
WHEN EXISTS (
    SELECT "User"."is_driver" FROM "User", "Order" WHERE (
        "User"."is_driver" = 1 AND "User"."id" = "New"."driver"
    )
)
BEGIN
    INSERT INTO "Order" ("driver") VALUES ("New"."driver");
END;

-- Only clients can have favorite dishes

CREATE TRIGGER IF NOT EXISTS "Client_favorite_dish"
BEFORE INSERT ON "Favorite_dish"
FOR EACH ROW
WHEN EXISTS(
    SELECT "User"."is_client" FROM "User", "Order" WHERE (
        "User"."is_client" = 1 AND "User"."id" = "New"."client"
    )
)
BEGIN
    INSERT INTO "Favorite_dish" VALUES ("New"."client");
END;
