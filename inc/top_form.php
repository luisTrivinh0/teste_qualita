<?php
  if (!isset($sh_main))
    $sh_main = $tabela . ".php";
  $id = '';
  $disabled = "";
  if (isset($_POST->incluir)){
    $id = -1;
    $operacao = "Incluir";
  } elseif (isset($_POST->alterar)){
    $id = $_POST->alterar;
    $operacao = "Alterar";
  }
?>
<style>
  .maiuscula{
    text-transform:uppercase
  }
</style>