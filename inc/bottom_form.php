<?php
  require_once("inc/footer.php");
?>
<script>
  <?php
    if(isset($dados)){
      foreach ($dados as $key => $campo){
        echo "valuetoform('$key', '" . str_replace("\r\n", "<br>", $campo) . "', '$tabela');";
      }
    }
    if (!isset($id))
      $id = "";
  ?>
  function valida_form(obj){
    $(".congela_para_enviar").attr('disabled', true);
    obj.disabled = true;
    if (!!window.valida_form_local){
      if (valida_form_local()){
        formeditado = false;
      } else {
        $(".congela_para_enviar").attr('disabled', false);
        obj.disabled = false;
        return;
      }
    }
    if (!$("#form_edit")[0].checkValidity()) {
      $("#form_edit")[0].reportValidity();
      $(".congela_para_enviar").attr('disabled', false);
      obj.disabled = false;
    } else {
      formeditado = false;
      $("#btn_submit").click();
    }
  }
</script>