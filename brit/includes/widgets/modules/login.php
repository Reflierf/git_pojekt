<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

logged_in_redirect();

?>

                <div id="login">
                    <div class="module">
                        <div class="module_head">Login</div>
                        <div class="module_body">
                            <form action="login.php" method="post">
                                <ul>
                                    <li><input type="text" id="user_name" name="user_name" placeholder="felhasználó név..."/></li>
                                    <li><input type="password" id="user_password" name="user_password" placeholder="jelszó..."/></li>
                                    <!-- <li><input type="submit" class="button buttonLong" id="user_login" name="user_login" value="Belépés"/></li> -->
                                    <button class="button buttonLong" type="submit" id="user_login" name="user_login"><i class="fa fa-sign-in" aria-hidden="true"></i> Belépés</button>
                                </ul>
                            </form>
                            <p>
                                <a href="index.php?page=register">Regisztráció</a><br />
                                Érvénytelen <a href="index.php?page=recover&mode=password">jelszó</a><br />
                                Érvénytelen <a href="index.php?page=recover&mode=username">felhasználó név</a>
                            </p>
                        </div>
                    </div>
                </div><!-- end login -->