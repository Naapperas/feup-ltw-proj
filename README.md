# XauFome

## Features

- [x] Register/Login/Logout
- [x] Edit profile (including image)
- [x] Add/Remove restaurants
- [x] Edit restaurant (including image)
- [x] Add/Delete dishes
- [x] Edit dishes (including images)
- [x] Add/Delete menus
- [x] Edit menus (including images)
- [x] List reviews
- [x] Respond to reviews
- [x] List orders
- [x] Change order state
- [x] Search restaurants, dishes, menus
- [x] Filter search by restaurant score and dish price
- [x] Order dishes/menus
- [x] Mark restaurants/dishes as favorites
- [x] Review a restaurant
- [x] Light and dark Modes
- [x] REST API

### REST API features

| endpoint                         | method | feature                                     |
| -------------------------------- | ------ | ------------------------------------------- |
| `/api`                           | GET    | Health check                                |
| `/api/cart`                      | GET    | Get items in cart                           |
| `/api/cart`                      | POST   | Add/remove items from cart                  |
| `/api/category`                  | GET    | Get one or all categories                   |
| `/api/dish`                      | GET    | Get one or all dishes                       |
| `/api/dish`                      | POST   | Edit/create a dish                          |
| `/api/dish`                      | DELETE | Delete a dish                               |
| `/api/dish/categories`           | GET    | Get a dish's categories                     |
| `/api/dish/categories`           | PUT    | Add a category to a dish                    |
| `/api/dish/categories`           | DELETE | Remove a category from a dish               |
| `/api/login`                     | POST   | Login                                       |
| `/api/login`                     | DELETE | Logout                                      |
| `/api/menu`                      | GET    | Get one or all menus                        |
| `/api/menu`                      | POST   | Edit/create a menu                          |
| `/api/menu`                      | DELETE | Delete a menu                               |
| `/api/menu/dishes`               | GET    | Get a menu's dishes                         |
| `/api/menu/dishes`               | PUT    | Add a dish to a menu                        |
| `/api/menu/dishes`               | DELETE | Remove a dish from a menu                   |
| `/api/register`                  | POST   | Register                                    |
| `/api/restaurant`                | GET    | Get one or all restaurants                  |
| `/api/restaurant`                | POST   | Edit/create a restaurant                    |
| `/api/restaurant`                | DELETE | Delete a restaurant                         |
| `/api/restaurant/categories`     | GET    | Get a restaurant's categories               |
| `/api/restaurant/categories`     | PUT    | Add a category to a restaurant              |
| `/api/restaurant/categories`     | DELETE | Remove a category from a restaurant         |
| `/api/restaurant/dishes`         | GET    | Get a restaurant's dishes                   |
| `/api/restaurant/menus`          | GET    | Get a restaurant's menus                    |
| `/api/restaurant/orders`         | GET    | Get a restaurant's orders                   |
| `/api/restaurant/orders`         | POST   | Change the state of an order                |
| `/api/restaurant/reviews`        | GET    | Get a restaurant's reviews                  |
| `/api/review`                    | GET    | Get one or all reviews                      |
| `/api/review`                    | POST   | Post a review                               |
| `/api/review/response`           | GET    | Get a review response                       |
| `/api/review/response`           | POST   | Reply to a review                           |
| `/api/search`                    | GET    | Search dishes/menus/restaurants             |
| `/api/user`                      | GET    | Get one or all users                        |
| `/api/user`                      | POST   | Edit a user                                 |
| `/api/user`                      | DELETE | Delete a user                               |
| `/api/user/favorite_dishes`      | GET    | Get a user's favorite dishes                |
| `/api/user/favorite_dishes`      | PUT    | Add a dish to a user's favorites            |
| `/api/user/favorite_dishes`      | DELETE | Remove a dish from a user's favorites       |
| `/api/user/favorite_dishes`      | POST   | Toggle a dish from a user's favorites       |
| `/api/user/favorite_restaurants` | GET    | Get a user's favorite restaurants           |
| `/api/user/favorite_restaurants` | PUT    | Add a restaurant to a user's favorites      |
| `/api/user/favorite_restaurants` | DELETE | Remove a restaurant from a user's favorites |
| `/api/user/favorite_restaurants` | POST   | Toggle a restaurant from a user's favorites |
| `/api/user/orders`               | GET    | Get a user's past orders                    |
| `/api/user/restaurants`          | GET    | Get a user's restaurants                    |

## Credentials

user/12345678

owner/12345678
