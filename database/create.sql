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
DROP TABLE IF EXISTS "Restaurant_review";
DROP TABLE IF EXISTS "Favorite_dish";
DROP TABLE IF EXISTS "Favorite_restaurant";
DROP TABLE IF EXISTS "Dish_order";
DROP TABLE IF EXISTS "Dish_menu";

-- Classes

CREATE TABLE "Restaurant" (
    "id" INTEGER NOT NOT NULL,
    "name" text NOT NULL,
    "address" text NOT NULL,
    "owner", INTEGER NOT NULL,
    PRIMARY KEY("id"),
    FOREIGN KEY("owner") REFERENCES "User",
    CONSTRAINT "nome_unico" UNIQUE ("name"),
    CONSTRAINT "address_unico" UNIQUE ("address")
    CONSTRAINT "is_owner" CHECK(
        "owner"."is_owner" == 1
    )
)

CREATE TABLE "Menu" (
    "id" INTEGER NOT NULL,
    "name" text NOT NULL,
    "restaurant" INTEGER NOT NULL,
    PRIMARY KEY ("id"),
    FOREIGN KEY("restaurant") REFERENCES "Restaurant",
    CONSTRAINT "nome_unico" UNIQUE ("name")
)

CREATE TABLE "Category" (
    "id" INTEGER NOT NULL,
    "name" text NOT NULL,
    PRIMARY KEY("id"),
    CONSTRAINT "nome_unico" UNIQUE ("name")
)

CREATE TABLE "Dish" (
    "id" INTEGER NOT NULL,
    "name" text NOT NULL,
    "price" FLOAT NOT NULL,
    "restaurant" INTEGER NOT NULL,
    PRIMARY KEY("id"),
    FOREIGN KEY("restaurant") REFERENCES "Restaurant",
    CONSTRAINT "nome_unico" UNIQUE ("name")
    CONSTRAINT "preco_positivo" CHECK (
        "price" > 0
    )
)

CREATE TABLE "Review" (
    "id" INTEGER NOT NULL,
    "score" INTEGER NOT NULL,
    "text" text NOT NULL,
    "client" INTEGER NOT NULL,
    PRIMARY KEY("id"),
    FOREIGN KEY("client") REFERENCES "Client"
    CONSTRAINT "score_in_range" CHECK (
        "score" >= 0 AND "score" <= 5
    )
)

CREATE TABLE "User" (
    "id" INTEGER NOT NULL,
    "name" text NOT NULL,
    "password" text NOT NULL,
    "address" text NOT NULL,
    "phone number" INTEGER NOT NULL,
    "is_owner" BOOLEAN NOT NULL,
    "is_client" BOOLEAN NOT NULL,
    "is_driver" BOOLEAN NOT NULL,
    PRIMARY KEY("id"),
)

CREATE TABLE "Order" (
    "id" INTEGER NOT NULL,
    "state" text NOT NULL,
    "delivery" BOOLEAN NOT NULL,
    "driver" INTEGER NOT NULL,
    PRIMARY KEY("id"),
    FOREIGN KEY("driver") REFERENCES "User",
    CONSTRAINT "user_is_driver" CHECK(
        "User"."is_driver" == 1
    )
)

-- Many to many

CREATE TABLE "Dish_category" (
    "dish" INTEGER NOT NULL,
    "category" INTEGER NOT NULL,
    PRIMARY KEY("dish", "category"),
    FOREIGN KEY("dish") REFERENCES "Dish",
    FOREIGN KEY("category") REFERENCES "Category"
)

CREATE TABLE "Restaurant_category" (
    "restaurant" INTEGER NOT NULL,
    "category" INTEGER NOT NULL,
    PRIMARY KEY("restaurant", "category"),
    FOREIGN KEY("restaurant") REFERENCES "Restaurant",
    FOREIGN KEY("category") REFERENCES "Category"
)

CREATE TABLE "Restaurant_review" (
    "restaurant" INTEGER NOT NULL,
    "review" INTEGER NOT NULL,
    PRIMARY KEY("restaurant", "review"),
    FOREIGN KEY("restaurant") REFERENCES "Restaurant",
    FOREIGN KEY("review") REFERENCES "Review"
)

CREATE TABLE "Favorite_restaurant" (
    "client" INTEGER NOT NULL,
    "restaurant" INTEGER NOT NULL,
    PRIMARY KEY("client", "restaurant"),
    FOREIGN KEY("client") REFERENCES "User",
    FOREIGN KEY("restaurant") REFERENCES "Restaurant"
    CONSTRAINT "is_user" CHECK(
        "client"."is_client" == 1
    )
)

CREATE TABLE "Favorite_dish" (
    "client" INTEGER NOT NULL,
    "dish" INTEGER NOT NULL,
    PRIMARY KEY("client" , "dish"),
    FOREIGN KEY("client") REFERENCES "User",
    FOREIGN KEY("dish") REFERENCES "Dish"
    CONSTRAINT "is_client" CHECK (
        "client"."is_client" == 1
    )
)

CREATE TABLE "Dish_order" (
    "dish" INTEGER NOT NULL,
    "order" INTEGER NOT NULL,
    PRIMARY KEY("dish", "order"),
    FOREIGN KEY("dish") REFERENCES "Dish",
    FOREIGN KEY("order") REFERENCES "Order"
)

CREATE TABLE "Dish_menu" (
    "dish" INTEGER NOT NULL,
    "menu" INTEGER NOT NULL,
    PRIMARY KEY("dish", "menu"),
    FOREIGN KEY("dish") REFERENCES "Dish",
    FOREIGN KEY("menu") REFERENCES "Menu"
)
