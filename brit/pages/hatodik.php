<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

protect_page();

?>

<p>Ez itt a 6. oldal</p>

<?php 

debug_r(scandir($corepath.'/pages'));
?>