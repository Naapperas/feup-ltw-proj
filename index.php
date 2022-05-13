<?php 
declare(strict_types=1);

require_once("./templates/components.php");
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        description: "Home page for XauFome.", 
        scripts: ["components/form.js"],
    ); ?>
    <body class="centered medium medium-spacing single column layout">
        <header class="appbar">
            <a href="." class="homepage-link"><h1 class="h6 color logo"></h1></a>
            <div class="button">
                <input class="textfield"
                    type="text"
                    placeholder="Restaurants, dishes, review score..."
                    id="search"
                    name="search"
                />
            </div>

            <?php createUserButtons(); ?>

        </header>

        <section class="card" id="cards">
            <h2 class="h6">Your favorites</h2>

            <article class="card">
                <header>
                    <img src="https://picsum.photos/40" alt="" class="avatar" />
                    <h3 class="h6">Title goes here</h3>
                    <span class="subtitle2 secondary">Secondary text</span>
                </header>
                <img
                    src="https://picsum.photos/316/194"
                    width="316"
                    height="194"
                    alt=""
                    class="full media"
                />
                <span class="body2 secondary">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor
                </span>
                <section class="actions">
                    <button class="button text">Action 1</button>
                    <button class="button text">Action 2</button>
                </section>
            </article>
            <article class="card elevated">
                <header>
                    <img src="https://picsum.photos/40" alt="" class="avatar" />
                    <h3 class="h6">Title goes here</h3>
                    <span class="subtitle2 secondary">Secondary text</span>
                </header>
                <img
                    src="https://picsum.photos/316/194"
                    width="316"
                    height="194"
                    alt=""
                    class="full media"
                />
                <span class="body2 secondary">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor
                </span>
                <section class="actions">
                    <button class="button text">Action 1</button>
                    <button class="button text">Action 2</button>
                </section>
            </article>
            <article class="card elevated">
                <header>
                    <img src="https://picsum.photos/40" alt="" class="avatar" />
                    <h3 class="h6">Title goes here</h3>
                    <span class="subtitle2 secondary">Secondary text</span>
                </header>
                <img
                    src="https://picsum.photos/316/194"
                    width="316"
                    height="194"
                    alt=""
                    class="full media"
                />
                <span class="body2 secondary">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor
                </span>
                <section class="actions">
                    <button class="button text">Action 1</button>
                    <button class="button text">Action 2</button>
                </section>
            </article>
            <article class="card elevated">
                <header>
                    <img src="https://picsum.photos/40" alt="" class="avatar" />
                    <h3 class="h6">Title goes here</h3>
                    <span class="subtitle2 secondary">Secondary text</span>
                </header>
                <img
                    src="https://picsum.photos/316/194"
                    width="316"
                    height="194"
                    alt=""
                    class="full media"
                />
                <span class="body2 secondary">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor
                </span>
                <section class="actions">
                    <button class="button text">Action 1</button>
                    <button class="button text">Action 2</button>
                </section>
            </article>
            <article class="card elevated">
                <header>
                    <img src="https://picsum.photos/40" alt="" class="avatar" />
                    <h3 class="h6">Title goes here</h3>
                    <span class="subtitle2 secondary">Secondary text</span>
                </header>
                <img
                    src="https://picsum.photos/316/194"
                    width="316"
                    height="194"
                    alt=""
                    class="full media"
                />
                <span class="body2 secondary">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor
                </span>
                <section class="actions">
                    <button class="button text">Action 1</button>
                    <button class="button text">Action 2</button>
                </section>
            </article>
            <article class="card elevated">
                <header>
                    <img src="https://picsum.photos/40" alt="" class="avatar" />
                    <h3 class="h6">Title goes here</h3>
                    <span class="subtitle2 secondary">Secondary text</span>
                </header>
                <img
                    src="https://picsum.photos/316/194"
                    width="316"
                    height="194"
                    alt=""
                    class="full media"
                />
                <span class="body2 secondary">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor
                </span>
                <section class="actions">
                    <button class="button text">Action 1</button>
                    <button class="button text">Action 2</button>
                </section>
            </article>
            <article class="card elevated">
                <header>
                    <img src="https://picsum.photos/40" alt="" class="avatar" />
                    <h3 class="h6">Title goes here</h3>
                    <span class="subtitle2 secondary">Secondary text</span>
                </header>
                <img
                    src="https://picsum.photos/316/194"
                    width="316"
                    height="194"
                    alt=""
                    class="full media"
                />
                <span class="body2 secondary">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor
                </span>
                <section class="actions">
                    <button class="button text">Action 1</button>
                    <button class="button text">Action 2</button>
                </section>
            </article>
            <article class="card elevated">
                <header>
                    <img src="https://picsum.photos/40" alt="" class="avatar" />
                    <h3 class="h6">Title goes here</h3>
                    <span class="subtitle2 secondary">Secondary text</span>
                </header>
                <img
                    src="https://picsum.photos/316/194"
                    width="316"
                    height="194"
                    alt=""
                    class="full media"
                />
                <span class="body2 secondary">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor
                </span>
                <section class="actions">
                    <button class="button text">Action 1</button>
                    <button class="button text">Action 2</button>
                </section>
            </article>
            <article class="card elevated">
                <header>
                    <img src="https://picsum.photos/40" alt="" class="avatar" />
                    <h3 class="h6">Title goes here</h3>
                    <span class="subtitle2 secondary">Secondary text</span>
                </header>
                <img
                    src="https://picsum.photos/316/194"
                    width="316"
                    height="194"
                    alt=""
                    class="full media"
                />
                <span class="body2 secondary">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor
                </span>
                <section class="actions">
                    <button class="button text">Action 1</button>
                    <button class="button text">Action 2</button>
                </section>
            </article>
            <article class="card elevated">
                <header>
                    <img src="https://picsum.photos/40" alt="" class="avatar" />
                    <h3 class="h6">Title goes here</h3>
                    <span class="subtitle2 secondary">Secondary text</span>
                </header>
                <img
                    src="https://picsum.photos/316/194"
                    width="316"
                    height="194"
                    alt=""
                    class="full media"
                />
                <span class="body2 secondary">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor
                </span>
                <section class="actions">
                    <button class="button text">Action 1</button>
                    <button class="button text">Action 2</button>
                </section>
            </article>
            <article class="card elevated">
                <header>
                    <img src="https://picsum.photos/40" alt="" class="avatar" />
                    <h3 class="h6">Title goes here</h3>
                    <span class="subtitle2 secondary">Secondary text</span>
                </header>
                <img
                    src="https://picsum.photos/316/194"
                    width="316"
                    height="194"
                    alt=""
                    class="full media"
                />
                <span class="body2 secondary">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor
                </span>
                <section class="actions">
                    <button class="button text">Action 1</button>
                    <button class="button text">Action 2</button>
                </section>
            </article>
        </section>
    </body>
</html>