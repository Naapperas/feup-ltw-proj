PRAGMA FOREIGN_KEYS = ON;

-- Classes

DROP TABLE IF EXISTS "Restaurant";
DROP TABLE IF EXISTS "Menu";
DROP TABLE IF EXISTS "Category";
DROP TABLE IF EXISTS "Dish";
DROP TABLE IF EXISTS "Order";
DROP TABLE IF EXISTS "Review";
DROP TABLE IF EXISTS "User";

-- Many to many

DROP TABLE IF EXISTS "Dish_category";
DROP TABLE IF EXISTS "Restaurant_category";
DROP TABLE IF EXISTS "Favorite_dish";
DROP TABLE IF EXISTS "Favorite_restaurant";
DROP TABLE IF EXISTS "Dish_order";
DROP TABLE IF EXISTS "Dish_menu";

-- Classes

CREATE TABLE "Restaurant" (
    "id" INTEGER NOT NULL,
    "name" TEXT NOT NULL,
    "address" TEXT NOT NULL,
    "owner" INTEGER NOT NULL,
    PRIMARY KEY("id"),
    FOREIGN KEY("owner") REFERENCES "User",
    CONSTRAINT "unique_address" UNIQUE ("address")
);

CREATE TABLE "Menu" (
    "id" INTEGER NOT NULL,
    "name" TEXT NOT NULL,
    "restaurant" INTEGER NOT NULL,
    "price" INTEGER DEFAULT 0,
    PRIMARY KEY ("id"),
    FOREIGN KEY("restaurant") REFERENCES "Restaurant",
    CONSTRAINT "unique_restaurant_name" UNIQUE ("name", "restaurant"),
    CONSTRAINT "positive_price" CHECK ("price" >= 0)
);

CREATE TABLE "Category" (
    "id" INTEGER NOT NULL,
    "name" TEXT NOT NULL,
    PRIMARY KEY("id"),
    CONSTRAINT "unique_name" UNIQUE ("name")
);

CREATE TABLE "Dish" (
    "id" INTEGER NOT NULL,
    "name" TEXT NOT NULL,
    "price" REAL NOT NULL,
    "restaurant" INTEGER NOT NULL,
    PRIMARY KEY("id"),
    FOREIGN KEY("restaurant") REFERENCES "Restaurant",
    CONSTRAINT "unique_restaurant_name" UNIQUE ("name", "restaurant")
    CONSTRAINT "positive_price" CHECK (
        "price" > 0
    )
);

CREATE TABLE "Review" (
    "id" INTEGER NOT NULL,
    "score" INTEGER NOT NULL,
    "text" TEXT NOT NULL,
    "client" INTEGER NOT NULL,
    "restaurant" INTEGER NOT NULL,
    PRIMARY KEY("id"),
    FOREIGN KEY("client") REFERENCES "User",
    FOREIGN KEY("restaurant") REFERENCES "Restaurant",
    CONSTRAINT "score_in_range" CHECK (
        "score" >= 0 AND "score" <= 5
    )
);

CREATE TABLE "User" (
    "id" INTEGER NOT NULL,
    "name" TEXT NOT NULL,
    "email" TEXT NOT NULL,
    "password" TEXT NOT NULL,
    "address" TEXT NOT NULL,
    "phone_number" TEXT NOT NULL,
    "is_owner" BOOLEAN NOT NULL,
    "is_client" BOOLEAN NOT NULL,
    "is_driver" BOOLEAN NOT NULL,
    PRIMARY KEY("id"),
    CONSTRAINT "unique_username" UNIQUE("name"),
    CONSTRAINT "unique_email" UNIQUE("email"),
    CONSTRAINT "unique_phone_number" UNIQUE("phone_number")
);

CREATE TABLE "Order" (
    "id" INTEGER NOT NULL,
    "state" TEXT NOT NULL,
    "delivery" BOOLEAN NOT NULL,
    "user_to_deliver" INTEGER NOT NULL,
    "driver" INTEGER NOT NULL,
    PRIMARY KEY("id"),
    FOREIGN KEY("driver") REFERENCES "User",
    FOREIGN KEY ("user_to_deliver") REFERENCES "User"
);

-- Many to many

CREATE TABLE "Dish_category" (
    "dish" INTEGER NOT NULL,
    "category" INTEGER NOT NULL,
    PRIMARY KEY("dish", "category"),
    FOREIGN KEY("dish") REFERENCES "Dish",
    FOREIGN KEY("category") REFERENCES "Category"
);

CREATE TABLE "Restaurant_category" (
    "restaurant" INTEGER NOT NULL,
    "category" INTEGER NOT NULL,
    PRIMARY KEY("restaurant", "category"),
    FOREIGN KEY("restaurant") REFERENCES "Restaurant",
    FOREIGN KEY("category") REFERENCES "Category"
);

CREATE TABLE "Favorite_restaurant" (
    "client" INTEGER NOT NULL,
    "restaurant" INTEGER NOT NULL,
    PRIMARY KEY("client", "restaurant"),
    FOREIGN KEY("client") REFERENCES "User",
    FOREIGN KEY("restaurant") REFERENCES "Restaurant"
);

CREATE TABLE "Favorite_dish" (
    "client" INTEGER NOT NULL,
    "dish" INTEGER NOT NULL,
    PRIMARY KEY("client" , "dish"),
    FOREIGN KEY("client") REFERENCES "User",
    FOREIGN KEY("dish") REFERENCES "Dish"
);

CREATE TABLE "Dish_order" (
    "dish" INTEGER NOT NULL,
    "order" INTEGER NOT NULL,
    PRIMARY KEY("dish", "order"),
    FOREIGN KEY("dish") REFERENCES "Dish",
    FOREIGN KEY("order") REFERENCES "Order"
);

CREATE TABLE "Dish_menu" (
    "dish" INTEGER NOT NULL,
    "menu" INTEGER NOT NULL,
    PRIMARY KEY("dish", "menu"),
    FOREIGN KEY("dish") REFERENCES "Dish",
    FOREIGN KEY("menu") REFERENCES "Menu"
);
