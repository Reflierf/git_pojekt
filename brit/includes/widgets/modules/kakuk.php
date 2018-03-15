<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if (isset($_POST['gender']) && !empty($_POST['gender'])) {
    if ($_POST['gender'] == 'yes') {
        header("Location: index.php?page=szavaz&gender=$_POST[gender]");
    }
}


?>

                <div id="kakuk">
                    <div class="module">
                        <div class="module_head">Tetszem neked?</div>
                        <div class="module_body">
                            <div class="sexy">
                                <img src="img/Brittany018.jpg" class="img-thumbnail"/>
                            </div>
                            <form action="" method="post">
                                <div class="r_dio">
                                    <div><input type="radio" name="gender" value="yes"/> Igen</div>
                                    <div><input type="radio" name="gender" value="no"/> Nem (Te buzi vagy?)</div>
                                    <div><input type="radio" name="gender" value="nemigen"/> Sajnos nem nem látok. :(</div>
                                    <!-- <div><input type="submit" class="button buttonLong" id="szavaz" name="szavaz" value="Szavazok"/></div> -->
                                    <div><button class="button buttonLong" type="submit" id="vote" name="vote"><i class="fa fa-comment" aria-hidden="true"></i> Szavazok</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- end login -->