<?php
$_POST = (object) $_POST;
$diagonal_1 = ($_POST->l1c1 + $_POST->l2c2 + $_POST->l3c3);
$diagonal_2 = ($_POST->l1c3 + $_POST->l2c2 + $_POST->l3c1);
$diferenca  = ($diagonal_1 - $diagonal_2);
echo "A primeira diagonal resulta em: " . $_POST->l1c1 . " + " . $_POST->l2c2 . " + " . $_POST->l3c3 . " = " . $diagonal_1;
echo "<br>A segunda diagonal resulta em: " . $_POST->l1c3 . " + " . $_POST->l2c2 . " + " . $_POST->l3c1 . " = " . $diagonal_2;
echo "<br>A diferença entre os totais resulta em: " . $diagonal_1 . " - " . $diagonal_2 . " = " . $diferenca;
echo "<br><button type='button' onclick='window.history.back();'>Voltar</button>"
?>