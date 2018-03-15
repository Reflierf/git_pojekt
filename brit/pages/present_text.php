<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */



?>
<p><br /><br />
$szovegkiiras = substr("$contents", 0, 500);<br />

A szöveg úgy végződik pl, hogy "Miután a szaklap leközö", a végére raktam ...-ot, de mennyivel szebb lenne, ha egész szóval végezné, nem egy töredékkel.
Ötlet?<br />
<br /><br />
VálaszPrivát üzenet<br />
Elvágod 500-nál, (substr) onnan visszafele megkeresed az első szóközt a végétől visszafele (strrpos), a megadott helyen elvágod az eredetit (vagy az 500-ast) és utána teszed a "...
</p>

<br /><br />

<?php

echo "
	<\!DOCTYPE html><br />
    <\html><br />
	<\body><br />
	<\?php<br />
	echo strrpos(\"I love php, I love php too!\",\"php\");<br />
	?><br />

	<\/body><br />
	<\/html><br />
";

?>

