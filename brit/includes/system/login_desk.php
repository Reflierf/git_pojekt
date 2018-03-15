<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */



?>

            <header>
                <p class="page_title">Regisztráció</p>
            </header>
            
            <section>
                <form action="login.php" method="post">
                    <ul class="bevitel">
                        <li><input type="text" id="user_name" name="user_name" placeholder="felhasználó név..."/></li>
                        <li><input type="password" id="user_password" name="user_password" placeholder="jelszó..."/></li>
                        <!-- <li><input type="submit" class="button buttonLong" id="user_login" name="user_login" value="Belépés"/></li> -->
                        <button class="button buttonLong" type="submit" id="user_login" name="user_login"><i class="fa fa-sign-in" aria-hidden="true"></i> Belépés</button>
                    </ul>
                </form>
                <p style="width: 70%; margin: 5px auto;">
                    <a href="index.php?page=register">Regisztráció</a><br />
                    Érvénytelen <a href="index.php?page=recover&mode=password">jelszó</a><br />
                    Érvénytelen <a href="index.php?page=recover&mode=username">felhasználó név</a>
                </p>
            </section>