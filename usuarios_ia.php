<?php
$_POST = (object)$_POST;
$tabela = "usuarios";
require_once("inc/header.php");
require_once("inc/top_form.php");
if ($id != -1) {
  $dados = array();
  $result = query("select u.*
                      from usuarios u
                     where u.external_id = '$id';");
  $linha = mysqli_fetch_object($result);
  $estrutura = mysqli_fetch_fields($result);
  foreach ($estrutura as $campo) {
    $dados[$campo->name] = (isset($linha->{$campo->name}))
      ? $linha->{$campo->name}
      : "";
  }
  $dados = (object)$dados;
  $id = "";
}

?>
<div class="text-center">
  <h2>
    <?= $operacao ?>
  </h2>
</div>
<div style="margin: 50px">
  <form action="usuarios_g.php" method="post">
    <input type="hidden" class="form-control" name="external_id" value="<?= $id ?>" id="external_id">
    <div class="form-row">
      <div class="col-md-3 mb-2">
        <label class="form-label obrigatorio" for="msisdn">Celular</label>
        <input type="text" class="form-control" name="msisdn" id="msisdn" onkeypress="mask(this, mphone);" required minlength="15" maxlength="15" autofocus>
      </div>
      <div class="col-md-3 mb-2">
        <label class="form-label obrigatorio" for="name">Nome</label>
        <input type="text" class="form-control" name="name" id="name" required maxlength="60">
      </div>
      <div class="col-md-3 mb-2">
        <label class="form-label obrigatorio" for="password">Senha</label>
        <input type="password" class="form-control" name="password" id="password" required maxlength="12">
      </div>
      <div class="col-md-3 mb-2">
        <label class="form-label" for="access_level">Tipo</label>
        <select class="form-control" name="access_level" id="access_level" required>
          <option value="" selected disabled>SELECIONE</option>
          <option value="pro">Pro</option>
          <option value="premium">Premium</option>
        </select>
      </div>
    </div>
    <button class='btn btn-warning mr-auto' type='button' onclick='location.href = document.referrer;' title='Cancelar'>Cancelar</button>
    <button class='btn btn-success ml-auto' type='submit' name='gravar' id='gravar' value='<?= $id ?>' title='Gravar'>Gravar</button>
  </form>
</div>
<?php
require_once("inc/bottom_form.php");
?>
<script>
  function mask(o, f) {
    setTimeout(function() {
      var v = mphone(o.value);
      if (v != o.value) {
        o.value = v;
      }
    }, 1);
  }

  function mphone(v) {
    var r = v.replace(/\D/g, "");
    r = r.replace(/^0/, "");
    if (r.length > 10) {
      r = r.replace(/^(\d\d)(\d{5})(\d{4}).*/, "($1) $2-$3");
    } else if (r.length > 5) {
      r = r.replace(/^(\d\d)(\d{4})(\d{0,4}).*/, "($1) $2-$3");
    } else if (r.length > 2) {
      r = r.replace(/^(\d\d)(\d{0,5})/, "($1) $2");
    } else {
      r = r.replace(/^(\d*)/, "($1");
    }
    return r;
  }
</script>