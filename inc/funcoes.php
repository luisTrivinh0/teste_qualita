<?php
  define('DB_SERVIDOR', '127.0.0.1:3307');
  define('DB_USER', 'root');
  define('DB_PASS', '');
  define('DATA_BASE', 'teste-qualita');

  function redirecionar($destino){
    echo "<script language=javascript> location.href = '$destino'; </script>";
    exit();
  }

 function dbcon(){
    $conexao = @mysqli_connect(DB_SERVIDOR, DB_USER, DB_PASS, DATA_BASE) or lerror("Falha na conexão com o banco de dados: " . mysqli_connect_error());
    mysqli_query($conexao, "SET NAMES 'utf8'");
    mysqli_query($conexao, "SET character_set_connection=utf8");
    mysqli_query($conexao, "SET character_set_client=utf8");
    mysqli_query($conexao, "SET character_set_results=utf8");
    mysqli_query($conexao, "SET SESSION group_concat_max_len = 10240");
    return $conexao;
  }

  function query($query){
    if ($query == "")
      return null;
    $conexao = dbcon();
    $resultado = mysqli_query($conexao, $query);
    return $resultado;
  }

  //Registra e avisa de erros no sistema
    function print_t($varray = array()){
    $html = "<table border='1px'>
               <thead>
                 <tr>
                   <th>key</th>
                   <th>value</th>
                 </tr>
               </thead>
               <tbody>";
    if (is_object($varray)) $varray = (array)$varray;
    if (is_array($varray)){
      foreach($varray as $key => $value){
        $tipo = gettype($value);
        if (is_array($value) or is_object($value)){
          $value = print_t($value);
        } else {
          if ($tipo == "boolean")
            $value = ($value) ? "true" : "false";
          if ($value === null)
            $value = "null";
          $value = htmlentities($value);
        };
        $html .= "<tr>
                    <td>$key ($tipo)</td>
                    <td>$value</td>
                  </tr>";
      }
    }
    $html .= "</tbody>
            </table>";
    return $html;
  }
  function gerar_senha($minusculas = 4, $numeros = 4, $maiusculas = 0, $simbolos = 0){
    /*
      FUNÇÃO: Gera senha aleatória respeitando os parâmetros (políticas) definidas
      DATA: 23/08/2021
      EXEMPLO:
        $nova_senha = gerar_senha(3, 2, 2, 1);
    */
    $mi = "abcdefghijklmnopqrstuvyxwz";
    $nu = "0123456789";
    $ma = "ABCDEFGHIJKLMNOPQRSTUVYXWZ";
    $si = "!@#$%&*()[]{}_-+=";
    $senha = "";
    if ($minusculas > 0)
      $senha .= substr(str_shuffle($mi), 0, $minusculas);
    if ($numeros > 0)
      $senha .= substr(str_shuffle($nu), 0, $numeros);
    if ($maiusculas > 0)
      $senha .= substr(str_shuffle($ma), 0, $maiusculas);
    if ($simbolos > 0)
      $senha .= substr(str_shuffle($si), 0, $simbolos);
    return str_shuffle($senha);
  }

  function create_key($a = null){
    if ($a == null){
      $key = rand(10000, 99999);
      $mutacao = ($key + $key) ^ 2;
      $mutacao = md5($mutacao);
      return($key . $mutacao);
    }
  }

  //função provisória até o alinhamento do webservice - Édnei - 14/10/2021
  function create_key_ws($expo = 2){
    $key_t = intdiv(time(), pow(60, $expo));
    $key = rand(10000, 99999);
    $mutacao = $key_t + $key;
    $mutacao = md5($mutacao);
    return($key . $mutacao);
  }

  function check_key($key){
    $mutacao = substr($key, 5);
    $key = substr($key, 0, 5);
    if (!is_numeric($key))
      return false;
    $mutacaox = ($key + $key) ^ 2;
    $mutacaox = md5($mutacaox);
    return($mutacao == $mutacaox);
  }

  function validateDate($date, $format = 'Y-m-d H:i:s'){
    /*
      FUNÇÃO: Verifica se string é uma data válida
      DATA: 2020, 11/06/2021
      EXEMPLO:
        if (validadateDate("2021-06-11", "Y-m-d")) ...
    */
    if (gettype($date) != "string")
      return(false);
    $d = date_create_from_format($format, $date);
    return($d && $d->format($format) == $date);
  }

  //------------------------------------------------------------------------------
  //Função para converter data para string
  function dtoc($data_antiga = null){
    if (is_null($data_antiga)) $data_antiga = ctod();
    $data_nova = false;
    if (strlen($data_antiga) == 10){
      $data_nova = date('d/m/Y', strtotime($data_antiga));
    } elseif (strlen($data_antiga) > 0){
      $data_nova = date('d/m/Y H:i:s', strtotime($data_antiga));
    }
    return $data_nova;
  }
  //------------------------------------------------------------------------------
  //Função para converter string para data
  function ctod($data_antiga = null){
    if (is_null($data_antiga)) $data_antiga = date('d/m/Y H:i:s');
    $data_nova = false;
    sscanf( $data_antiga, "%d/%d/%d %s", $d, $m, $y, $h);
    if (strlen($data_antiga) == 10){
      $data_nova = date("Y-m-d", strtotime("$y-$m-$d"));
    } elseif (strlen($data_antiga) > 0){
      $data_nova = date("Y-m-d H:i:s", strtotime("$y-$m-$d $h"));
    }
    return $data_nova;
  }

  //------------------------------------------------------------------------------
  //Acrescentar botões nos forms
  function botoes($botoes, $personal = null){
    $acao = "";
    if ($botoes == "Incluir" or $botoes == "Alterar"){
      $botoes = "C,G";
    } elseif ($botoes == "Consultar"){
      $botoes = "F";
    }
    $abotoes = explode(",", $botoes);
    $botoes = "";
    foreach ($abotoes as $botao){
      $botao = strtoupper(trim($botao));
      if ($botao == "C")
        $botoes .= "<button class='btn btn-warning mr-auto congela_para_enviar' type='button' id='btn_cancelar' onclick='location.href = document.referrer;' title='Fecha sem gravar os dados'><i class='fal fa-window-close'></i> Cancelar</button>";
      if ($botao == "G")
        $botoes .= "<button class='btn btn-success ml-auto congela_para_enviar' type='button' id='btn_gravar' onclick='valida_form(this);' title='Grava os dados e fecha'><i class='fal fa-save'></i> Gravar</button>
                    <button style='display:none;' id='btn_submit' type='submit' id='btn_gravar'></button>";
      if ($botao == "F")
        $botoes .= "<button class='btn btn-info mr-auto congela_para_enviar' type='button' id='btn_fechar' onclick='location.href = document.referrer;' title='Fecha'>Fechar</button>";
      if ($botao == "P")
        $botoes .= "<button class='btn btn-info ml-auto congela_para_enviar' type='button' id='btn_imprimir' onclick='window.print();' title='Imprime'><i class='fal fa-print'></i> Imprimir</button>";
      if ($botao == "PERSONAL")
        $botoes .= $personal;
    }
    return $botoes;
  }

  //------------------------------------------------------------------------------
  //Acertar campos array para gravação no banco de dados
  function acerta_array($acampos){
    if (is_object($acampos))
      $acampos = (array)$acampos;
    foreach($acampos as $key => $campo){
      if(is_array($campo)){
        $acampos[$key] = acerta_array($campo);
      } else {
        $acampos[$key] = acerta_texto($campo);
      }
    }
    return $acampos;
  }

  //------------------------------------------------------------------------------
  //Acertar campos texto para gravação no banco de dados
  function acerta_texto($string, $case = ""){
    if ($string === null)
      $string = "";
    if (substr($string, 0, 5) == "*sql="){
      $string = substr($string, 5);
    } else {
      $string = str_replace("'", "`", $string);
      if ($case == "M")
        $string = mb_strtoupper($string, mb_internal_encoding());
      $string = ($string === NULL or "$string" == "") ? "null" : "'" . addslashes(trim($string)) . "'";
    }
    return $string;
  }

  //------------------------------------------------------------------------------
  //Montagem de query para insert ou update
  function montaqueryg($tabela, $dados, $nid = null, $id = null){
    $campos  =
    $valores =
    $update  = array();
    $dados = (object)acerta_array($dados);
    foreach ($dados as $key => $value){
      $campos[]  = $key;
      $valores[] = $value;
      $update[]  = "$key = $value";
    }
    $campos  = implode(",", $campos );
    $valores = implode(",", $valores);
    $update  = implode(",", $update );
    if ($id == null){
      $query = "insert into $tabela ($campos) values ($valores) on duplicate key update $update;";
    } else {
      $query = "update $tabela set $update where $nid = '$id';";
    }
    return $query;
  }

 //Verificar se link está acessivel
  function curl_info($url){
      $ch = curl_init();
      curl_setopt( $ch, CURLOPT_URL, $url );
      curl_setopt( $ch, CURLOPT_HEADER, 1);
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
      curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 3 );
      curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
      $content = curl_exec( $ch );
      $info = curl_getinfo( $ch );
      return ($info['http_code'] == 200);
  }

  //Função para gerar lista Combobox de UF's
  function ufs($vuf = "SELECIONAR", $requerido = false){
    $ufs["SELECIONAR"] = "";
    $ufs["AC"] = "AC";
    $ufs["AL"] = "AL";
    $ufs["AM"] = "AM";
    $ufs["AP"] = "AP";
    $ufs["BA"] = "BA";
    $ufs["CE"] = "CE";
    $ufs["DF"] = "DF";
    $ufs["ES"] = "ES";
    $ufs["GO"] = "GO";
    $ufs["MA"] = "MA";
    $ufs["MG"] = "MG";
    $ufs["MS"] = "MS";
    $ufs["MT"] = "MT";
    $ufs["PA"] = "PA";
    $ufs["PB"] = "PB";
    $ufs["PE"] = "PE";
    $ufs["PI"] = "PI";
    $ufs["PR"] = "PR";
    $ufs["RJ"] = "RJ";
    $ufs["RN"] = "RN";
    $ufs["RO"] = "RO";
    $ufs["RR"] = "RR";
    $ufs["RS"] = "RS";
    $ufs["SC"] = "SC";
    $ufs["SP"] = "SP";
    $ufs["SE"] = "SE";
    $ufs["TO"] = "TO";
    $ufs["EX"] = "EX";
    $lista_ufs = "";
    foreach ($ufs as $key => $iuf) {
      $lista_ufs .= "<option value='$iuf'>$key</option>";
    }
    return $lista_ufs;
  }

  //Primeira letra maiúscula
  function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("de", "da", "dos", "das", "do", "I", "II", "III", "IV", "V", "VI")){
    /*
     * Exceptions in lower case are words you don't want converted
     * Exceptions all in upper case are any words you don't want converted to title case
     *   but should be converted to upper case, e.g.:
     *   king henry viii or king henry Viii should be King Henry VIII
     */
    $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
    foreach ($delimiters as $dlnr => $delimiter) {
      $words = explode($delimiter, $string);
      $newwords = array();
      foreach ($words as $wordnr => $word) {
        if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
          // check exceptions list for any words that should be in upper case
          $word = mb_strtoupper($word, "UTF-8");
        } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
          // check exceptions list for any words that should be in upper case
          $word = mb_strtolower($word, "UTF-8");
        } elseif (!in_array($word, $exceptions)) {
          // convert to uppercase (non-utf8 only)
          $word = ucfirst($word);
        }
        array_push($newwords, $word);
      }
      $string = join($delimiter, $newwords);
    }
    return $string;
  }

  function number_format_float($number){
    /*
      FUNÇÃO: Retorna número com decimal flutuante formatado
      DATA: 2020 - 07/12/2021
      EXEMPLO:
      <td><?=number_format_float($peso)?></td>
    */
    $number += 0;
    $decimais = 0;
    $elementos = explode(".", $number);
    if (count($elementos) == 2){
      $decimais = strlen($elementos[1]);
    } else {
      $elementos = explode(",", $number);
      if (count($elementos) == 2)
        $decimais = strlen($elementos[1]);
    }
    return(number_format($number, $decimais, ",", "."));
  }

  function tamanho_arquivo($arquivo) {
    /*
      FUNÇÃO: Retorna uma string contendo o tamanho do arquivo com a grandeza adequada
      DATA: 2021
      EXEMPLO:
        <td><?=tamanho_arquivo($arquivo)?></td>
    */
    if (!file_exists($arquivo))
      return("Indisponível");
    $tamanhoarquivo = filesize($arquivo);
    /* Medidas */
    $medidas = array('KB', 'MB', 'GB', 'TB');
    /* Se for menor que 1KB arredonda para 1KB */
    if($tamanhoarquivo < 999){
    //    $tamanhoarquivo = 1000;
      return $tamanhoarquivo . " bytes";
    }
    for ($i = 0; $tamanhoarquivo > 999; $i++){
      $tamanhoarquivo /= 1024;
    }
    return round($tamanhoarquivo) . " " . $medidas[$i - 1];
  }

  function array_to_xml( $data, &$xml_data) {
    /*
      FUNÇÃO: Converte Array para XML
      DATA: 11/06/2021, 12/06/2021
      EXEMPLO:
        $data = array('total_stud' => 500);
        $xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        array_to_xml($data, $xml_data);
        $result = $xml_data->asXML('teste_xml.xml');
      OBSERVAÇÕES: Para o processo inverso utilizar: $array = (array)$xml;
    */
    foreach($data as $key => $value) {
      if(is_numeric($key))
        $key = 'item_'. $key; //dealing with <0/>..<n/> issues
      if(is_array($value)) {
        $subnode = $xml_data->addChild($key);
        array_to_xml($value, $subnode);
      } else {
        $xml_data->addChild("$key", htmlspecialchars("$value"));
      }
    }
  }

  function xml_to_array($xml){
    /*
      FUNÇÃO: Converte XML para Array
      DATA: 11/06/2021
      EXEMPLO:
        $xml = simplexml_load_file('teste_xml.xml');
        $array = xml_to_array($xml);
    */
    $json = str_replace("{}", "null", json_encode($xml));
    $array = json_decode($json, TRUE);
    return($array);
  }

  function tira_acentos($string = ""){
    /*
      FUNÇÃO: Remover acentuação de strings
      DATA: X, 11/06/2021
      EXEMPLO:
        $string_sem_acento = tira_acentos("Édnei");
    */
    $mapa_caraceteres_acentuados = array("/(á|à|ã|â|ä)/"
                                       , "/(Á|À|Ã|Â|Ä)/"
                                       , "/(é|è|ê|ë)/"
                                       , "/(É|È|Ê|Ë)/"
                                       , "/(í|ì|î|ï)/"
                                       , "/(Í|Ì|Î|Ï)/"
                                       , "/(ó|ò|õ|ô|ö)/"
                                       , "/(Ó|Ò|Õ|Ô|Ö)/"
                                       , "/(ú|ù|û|ü)/"
                                       , "/(Ú|Ù|Û|Ü)/"
                                       , "/(ñ)/"
                                       , "/(Ñ)/"
                                       , "/(ç)/"
                                       , "/(Ç)/");
    $mapa_caraceteres_nao_acentuados = explode(" ","a A e E i I o O u U n N c C");
    $string = preg_replace($mapa_caraceteres_acentuados, $mapa_caraceteres_nao_acentuados, $string);
    return($string);
  }

  function sanitize_filename($file_name){
    /*
      FUNÇÃO: Sanitizar (adequar) o nome do arquivo
      DATA: 11/06/2021
      EXEMPLO:
        $nome_sanitizado = sanitize_filename("Bagunça / Organizada 11/06/2021.xls");
    */
    $file_name = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file_name);
    $file_name = mb_ereg_replace("([\.]{2,})", '', $file_name);
    return($file_name);
  }

  function copiar_diretorio($diretorio, $destino){
    /*
      FUNÇÃO: Copia diretório (pasta) e todo o conteúdo dentro dele
      DATA: 11/06/2021
      EXEMPLO:
        copiar_diretorio("/pastaa", "/pastab");
    */
    if ($destino[strlen($destino) - 1] == '/')
      $destino = substr($destino, 0, -1);
    if (!is_dir($destino))
      mkdir($destino, 0755);
    $folder = opendir($diretorio);
    while ($item = readdir($folder)){
      if ($item == '.' || $item == '..')
        continue;
      if (is_dir("{$diretorio}/{$item}")){
        copiar_diretorio("{$diretorio}/{$item}", "{$destino}/{$item}");
      } else {
        copy("{$diretorio}/{$item}", "{$destino}/{$item}");
      }
    }
  }

  function delTree($dir) {
    /*
      FUNÇÃO: Apaga o diretório e todo o seu conteúdo
      DATA: 11/06/2021
      EXEMPLO:
        delTree("/pastab");
    */
    if (!file_exists($dir))
      return(false);
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return(rmdir($dir));
  }

  function marca_palavras($texto, $palavras, $marcador_inicial = "<u>", $marcador_final = "</u>"){
    /*
      FUNÇÃO: Marca palavras dentro de um texto
      DATA: 03/08/2021
      EXEMPLO:
        marca_palavras("Texto para teste de marcação de palavras", array('teste', 'de palavras'), "<b>", "</b>");
    */
    $palavras_pattern = array();
    foreach($palavras as $palavra){
      $palavra_pattern = "";
      foreach(str_split(mb_strtolower(tira_acentos($palavra))) as $letra){
        switch ($letra){
          case "a" :
            $letra = '[aáàâãæ]';
          break;
          case "e" :
            $letra = '[eéèêë]';
          break;
          case "i" :
            $letra = '[iíìîï]';
          break;
          case "o" :
            $letra = '[oóòõôö]';
          break;
          case "u" :
            $letra = '[uúùûü]';
          break;
          case "c" :
            $letra = '[cç]';
          break;
          case "n" :
            $letra = '[nñ]';
        }
        $palavra_pattern .= $letra;
      }
      $palavras_pattern[] = "/" . $palavra_pattern . "/iu";
    }
    $texto = preg_replace($palavras_pattern, $marcador_inicial . "$0" . $marcador_final, $texto, -1);
    return($texto);
  }

  function mascara_string($mascara, $string){
    /*
      FUNÇÃO: Aplica máscara de formatação em uma string
      DATA: 24/09/2021
      EXEMPLO:
        echo mascara_string("#####-###", "18550000"); //18550-000
    */
    //$string = str_replace(" ", "", $string);
    for($i = 0; $i < strlen($string); $i ++){
      $mascara[strpos($mascara, "#")] = $string[$i];
    }
    return $mascara;
  }

  function getClientIP(){
    /*
      FUNÇÃO: Obtém o IP (IPV4 e IPV6) do cliente
      DATA: 01/09/2021
      EXEMPLO:
        getClientIP();
    */
    if (getenv('HTTP_CLIENT_IP')){
      $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')){
      $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_X_FORWARDED')){
      $ip = getenv('HTTP_X_FORWARDED');
    } elseif (getenv('HTTP_FORWARDED_FOR')){
      $ip = getenv('HTTP_FORWARDED_FOR');
    } elseif (getenv('HTTP_FORWARDED')){
      $ip = getenv('HTTP_FORWARDED');
    } elseif (getenv('REMOTE_ADDR')) {
      $ip = getenv('REMOTE_ADDR');
    } else {
      $ip = "Não Identificado";
    }
    $ip = array_values(array_filter(explode(',', $ip)));
    return reset($ip);
  }

  function diferenca_datas($data_inicio, $data_fim = "", $format = "%a"){
    /*
      FUNÇÃO: Calcula a diferença entre datas
      DATA: 15/10/2021
      EXEMPLO:
        diferenca_datas("2021-10-13", "2021-10-20"); // 7
    */
    if ($data_fim == "")
      $data_fim = date("Y-m-d H:i:s");
    $datetime1 = date_create($data_inicio);
    $datetime2 = date_create($data_fim);
    $interval = date_diff($datetime1, $datetime2);
    $diferenca = ($interval->invert)
               ? -($interval->format($format))
               : $interval->format($format);
    return($diferenca);
  }

function validaCPF($cpf) {
    /*
      FUNÇÃO: Valida o Nº do CPF
      DATA: 02/09/2021
      EXEMPLO:
        validaCPF($variavel);
        retorna CPF formatado
      Luis
    */
    //Deixa somente números
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);
    if (strlen($cpf) != 11)
      return false;
    //Verifica repetições dos digitos. Ex.: 22222222222
    if (preg_match('/(\d)\1{10}/', $cpf))
      return false;
    for ($t = 9; $t < 11; $t++) {
      for ($d = 0, $c = 0; $c < $t; $c++) {
        $d += $cpf[$c] * (($t + 1) - $c);
      }
      $d = ((10 * $d) % 11) % 10;
      if ($cpf[$c] != $d) {
        return false;
      }
    }
    return (substr($cpf, 0, 3) . "." .
            substr($cpf, 2, 3) . "." .
            substr($cpf, 5, 3) . "-" .
            substr($cpf, 8, 3));
  }

  function validaCNPJ($cnpj){
    /*
      FUNÇÃO: Valida o Nº do CNPJ
      DATA: 02/09/2021
      EXEMPLO:
        validaCNPJ($variavel);
        retorna CNPJ formatado
      Luis
    */
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
	  // Valida tamanho
    if (strlen($cnpj) != 14)
      return false;
    // Verifica se todos os digitos são iguais
    if (preg_match('/(\d)\1{13}/', $cnpj))
      return false;
    // Valida primeiro dígito verificador
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++){
      $soma += $cnpj[$i] * $j;
      $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
      return false;
    // Valida segundo dígito verificador
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++){
      $soma += $cnpj[$i] * $j;
      $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    return (substr($cnpj, 0, 2) . "." .
            substr($cnpj, 2, 3) . "." .
            substr($cnpj, 5, 3) . "/" .
            substr($cnpj, 8, 4) . "-" .
            substr($cnpj, 12, 2));
  }

  function validaTelefone($telefone){
    /*
      FUNÇÃO: Valida o Nº do Telefone
      DATA: 02/09/2021
      EXEMPLO:
        validaTelefone($variavel);
        retorna Telefone formatado (Comercial ou Celular)
      Luis
    */
    $telefone = preg_replace('/[^0-9]/is', '', $telefone);
    if (strlen($telefone) == 10)
      return (substr($telefone, 0, 0) . "(" .
              substr($telefone, 0, 2) . ") " .
              substr($telefone, 2, 4) . "-" .
              substr($telefone, 6, 4));

    if (strlen($telefone) == 11)
      return (substr($telefone, 0, 0) . "(" .
              substr($telefone, 0, 2) . ") " .
              substr($telefone, 2, 5) . "-" .
              substr($telefone, 7, 4));

  }

  function validaCEP($cep){
    /*
      FUNÇÃO: Valida o Nº do CEP
      DATA: 03/09/2021
      EXEMPLO:
        validaCEP($variavel);
        retorna CEP formatado
      Luis
    */
    $cep = preg_replace('/[^0-9]/is', '', $cep);
    if (strlen($cep) != 8)
      return false;

    return(substr($cep, 0, 5) . "-" .
           substr($cep, 5, 3));
  }

  function validaUF($uf){
    /*
      FUNÇÃO: Valida o UF
      DATA: 16/09/2021
      EXEMPLO:
        validaUF($variavel);
        retorna true//false
      Luis
    */
      return(in_array(strtoupper($uf), array(''
                                           , 'AC'
                                           , 'AL'
                                           , 'AP'
                                           , 'AM'
                                           , 'BA'
                                           , 'CE'
                                           , 'DF'
                                           , 'ES'
                                           , 'GO'
                                           , 'MA'
                                           , 'MT'
                                           , 'MS'
                                           , 'MG'
                                           , 'PA'
                                           , 'PB'
                                           , 'PR'
                                           , 'PE'
                                           , 'PI'
                                           , 'RJ'
                                           , 'RN'
                                           , 'RS'
                                           , 'RO'
                                           , 'RR'
                                           , 'SC'
                                           , 'SP'
                                           , 'SE'
                                           , 'TO')));
  }

  function catch_g($e) {
    /*
      FUNÇÃO: Captura dados dos erros PHP, registra nos logs de erros e envia e-mail
      DATA: 16/08/2021
      EXEMPLO:
        try {
          ...
        } catch (Exception | Error $e) {
          catch_g($e);
        }
    */
    $mensagem = "Erro ou Exceção capturada:<br>";
    $mensagem .= "Mensagem: " . $e->getMessage() . "<br>";
    $mensagem .= "Código: " . $e->getCode() . "<br>";
    $mensagem .= "Arquivo: " . $e->getFile() . "<br>";
    $mensagem .= "Linha: " . $e->getLine() . "<br>";
    $mensagem .= "Trace: " . $e->getTraceAsString();
    lerror($mensagem);
  }

?>