PRAGMA FOREIGN_KEYS = ON;

DROP TRIGGER IF EXISTS "User_is_owner";
DROP TRIGGER IF EXISTS "Client_favorite_restaurant";
DROP TRIGGER IF EXISTS "User_is_driver";
DROP TRIGGER IF EXISTS "Client_favorite_dish";
DROP TRIGGER IF EXISTS "Client_review";

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
