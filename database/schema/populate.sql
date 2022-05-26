INSERT INTO "Restaurant" ("name", "address", "phone_number", "website", "opening_time", "closing_time", "owner")
VALUES ('McDonald''s', 'Estr. da Circunvalação 8114 8116, 4200-163 Porto', '225091784', 'https://www.mcdonalds.pt/', '08:00', '02:00', 11);

INSERT INTO "Category" ("name")
VALUES ('Vegetarian'), ('Vegan'), ('Gluten Free'), ('Asian'), ('Fast Food'),
('Burger'), ('Pizza'), ('Italian'), ('Sushi'), ('Healthy'), ('BBQ'), ('Portuguese'),
('Sandwich'), ('Desserts'), ('Poke'), ('Brazilian'), ('Kebab'), ('Chinese'),
('Comfort Food'), ('Mexican'), ('Juice and Smoothies'), ('Indian'), ('Chicken'),
('Bakery'), ('Pasta'), ('Deli'), ('Soup'), ('Hot Dog'), ('Wings'), ('Thai'),
('Salads'), ('Seafood'), ('Pastry'), ('Burritos'), ('American'), ('European'),
('Fish and Chips'), ('Ice Cream'), ('Coffee and Tea'), ('Middle Eastern'),
('Halal'), ('Japanese'), ('Turkish'), ('Pub'), ('Spanish'), ('Hawaiian'),
('South American'), ('Greek'), ('Mediterranean'), ('Falafel');

INSERT INTO "Dish" ("name", "price", "restaurant")
VALUES ('Kansas Steakhouse Double', 7.90, 1), ('Kansas Steakhouse Single', 6.50, 1),
('Kansas Steakhouse Chicken', 6.50, 1), ('Rustic Chicken Chutney Manga', 7.50, 1),
('Rustic Chicken Mostarda e Mel', 7.50, 1), ('Big Tasty Double', 7.90, 1),
('Big Tasty Single', 6.50, 1), ('CBO', 7.00, 1), ('McRoyal Bacon', 5.50, 1),
('McRoyal Deluxe', 5.50, 1), ('McRoyal Cheese', 5.50, 1), ('Big Mac', 4.70, 1),
('Double Cheeseburger', 4.35, 1), ('McChicken', 4.35, 1), ('Filet-o-Fish', 4.35, 1);
