<?php
include('inc/header.php');
$tr = '';
$result = query("SELECT u.*
                     FROM usuarios u;");
while ($linha = mysqli_fetch_object($result)) {
  $botao = '';
  if($linha->access_level == 'pro'){
    $botao = "<button type='submit' formaction='usuarios_g.php' class='btn btn-info' name='upgrade' value='$linha->external_id'>Upgrade</button>";
  }else{
    $botao = "<button type='submit' formaction='usuarios_g.php' class='btn btn-danger' name='downgrade' value='$linha->external_id'>Downgrade</button>";
  }
  $tr .= "<tr>
             <td class='text-center'>$linha->external_id</td>
             <td>$linha->msisdn</td>
             <td>$linha->name</td>
             <td>$linha->access_level</td>
             <td class='text-center'>
               $botao
             </td>
           </tr>";
}
?>
<div>
  <center>
    <h3>Usuários</h3>
  </center>
</div>
<form action="correspondencias_ia.php" method="post">
  <table class="table table-bordered">
    <thead class="thead-dark">
      <tr>
        <th class='text-center' scope="col">ID</th>
        <th class='text-center' scope="col">Telefone</th>
        <th class='text-center' scope="col">Nome</th>
        <th class='text-center' scope="col">Acesso</th>
        <th class='text-center' scope="col" class="text-center"><button class='btn btn-success mr-auto' type='submit' formaction='usuarios_ia.php' value='-1' name='incluir' title="Incluir registro."><?= $incluir ?></button></th>
      </tr>
    </thead>
    <tbody>
      <?= $tr ?>
    </tbody>
  </table>
</form>
<?php
include('inc/footer.php');
?>