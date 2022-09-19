
//validar CPF
function ValidarCPF(Objcpf) {
  var cpf = Objcpf.value;
  exp = /\.|\-/g
  cpf = cpf.toString().replace(exp, "");
  var digitoDigitado = eval(cpf.charAt(9) + cpf.charAt(10));
  var soma1 = 0, soma2 = 0;
  var vlr = 1;
  for (i = 0; i < 9; i++) {
    soma1 += eval(cpf.charAt(i) * vlr);
    soma2 += eval(cpf.charAt(i) * (vlr - 1));
    vlr++;
  }
  soma1 = soma1 % 11;
  soma1 = (soma1 == 10) ? 0 : soma1;
  soma2 = (soma2 + (soma1 * 9)) % 11;
  soma2 = (soma2 == 10) ? 0 : soma2;
  var digitoGerado = (soma1 * 10) + soma2;
  if (cpf === "00000000000" ||
      cpf === "11111111111" ||
      cpf === "22222222222" ||
      cpf === "33333333333" ||
      cpf === "44444444444" ||
      cpf === "55555555555" ||
      cpf === "66666666666" ||
      cpf === "77777777777" ||
      cpf === "88888888888" ||
      cpf === "99999999999" ||
      (digitoGerado != digitoDigitado && Objcpf.value.length > 0)) {
    toastr.warning('CPF inválido!');
    Objcpf.select();
    //Objcpf.value = "";
    return false;
  } else {
    return true;
  }
}

//Procurar CNPJ
function ProcurarCNPJ(cnpj) {
  if (!ValidarCNPJ(cnpj))
    return false;
  var nome = $(cnpj).data("nome");
  if (nome == undefined)
    nome = "nome";
  var nome_fantasia = $(cnpj).data("nome_fantasia");
  if (nome_fantasia == undefined)
    nome_fantasia = "nome_fantasia";
  var cep = $(cnpj).data("cep");
  if (cep == undefined)
    cep = "cep";
  var logradouro = $(cnpj).data("logradouro");
  if (logradouro == undefined)
    logradouro = "logradouro";
  var numero = $(cnpj).data("numero");
  if (numero == undefined)
    numero = "numero";
  var complemento = $(cnpj).data("complemento");
  if (complemento == undefined)
    complemento = "complemento";
  var bairro = $(cnpj).data("bairro");
  if (bairro == undefined)
    bairro = "bairro";
  var municipio = $(cnpj).data("municipio");
  if (municipio == undefined)
    municipio = "municipio";
  var uf = $(cnpj).data("uf");
  if (uf == undefined)
    uf = "uf";
  var cnae = $(cnpj).data("cnae");
  if (cnae == undefined)
    cnae = "cnae";
  var telefone = $(cnpj).data("telefone");
  if (telefone == undefined)
    telefone = "telefone";
  var email = $(cnpj).data("email");
  if (email == undefined)
    email = "email"
  nome = document.getElementById(nome);
  if (nome && nome.value == "" && cnpj.value != "") {
    nome_fantasia = document.getElementById(nome_fantasia);
    cep = document.getElementById(cep);
    logradouro = document.getElementById(logradouro);
    numero = document.getElementById(numero);
    complemento = document.getElementById(complemento);
    bairro = document.getElementById(bairro);
    municipio = document.getElementById(municipio);
    uf = document.getElementById(uf);
    cnae = document.getElementById(cnae);
    telefone = document.getElementById(telefone);
    email = document.getElementById(email);
    toastr.info('Pesquisando CNPJ!');
    $.ajax({
      url: 'https://receitaws.com.br/v1/cnpj/' + cnpj.value.replace(/[^0-9]/g, ''),
      dataType: 'jsonp',
      success: function (data) {
        if (data !== null) {
          if (nome !== null)
            nome.value = maiuscula(data.nome.substr(0, nome.maxLength));
          if (data.fantasia != "" && nome_fantasia !== null)
            nome_fantasia.value = maiuscula(data.fantasia.substr(0, nome_fantasia.maxLength));
          if (logradouro !== null)
            logradouro.value = maiuscula(data.logradouro.substr(0, logradouro.maxLength));
          if (numero !== null)
            numero.value = data.numero.substr(0, numero.maxLength);
          if (data.complemento != "" && complemento !== null)
            complemento.value = maiuscula(data.complemento.substr(0, complemento.maxLength));
          if (bairro !== null)
            bairro.value = maiuscula(data.bairro.substr(0, bairro.maxLength));
          if (municipio !== null)
            municipio.value = maiuscula(data.municipio.substr(0, municipio.maxLength));
          if (uf !== null)
            uf.value        = data.uf;
          if (cep !== null)
            cep.value = data.cep.substr(0, 2) + data.cep.substr(3, 7);
          if (data.telefone != "" && telefone !== null)
            telefone.value = data.telefone.substr(0, telefone.maxLength);
          if (data.email != "" && email !== null)
            email.value = data.email.substr(0, email.maxLength);
          if (data.atividade_principal[0].code != "" && cnae !== null) {
            cnae.value = data.atividade_principal[0].code;
            cnae.setAttribute('data-content', data.atividade_principal[0].text);
          }
          toastr.success('Encontrado: ' + nome.value);
        } else {
          toastr.warning('CNPJ não encontrado ou pesquisa indisponível!');
        }
      },
      error: function (status, erro, request) {
        console.log(status, erro, request);
      }
    });
  }
  return true;
}

//valida o CNPJ digitado
function ValidarCNPJ(ObjCnpj) {
  var cnpj = ObjCnpj.value;
  var valida = new Array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
  var dig1 = new Number;
  var dig2 = new Number;
  exp = /\.|\-|\//g
  cnpj = cnpj.toString().replace(exp, "");
  var digito = new Number(eval(cnpj.charAt(12) + cnpj.charAt(13)));
  for (i = 0; i < valida.length; i++) {
    dig1 += (i > 0 ? (cnpj.charAt(i - 1) * valida[i]) : 0);
    dig2 += cnpj.charAt(i) * valida[i];
  }
  dig1 = (((dig1 % 11) < 2) ? 0 : (11 - (dig1 % 11)));
  dig2 = (((dig2 % 11) < 2) ? 0 : (11 - (dig2 % 11)));
  if (((dig1 * 10) + dig2) != digito && ObjCnpj.value.length > 0) {
    toastr.warning('CNPJ inválido!');
    ObjCnpj.select();
    //ObjCnpj.value = "";
    return false;
  } else {
    return true;
  }
}

//Procurar CEP
function ProcurarCEP(cep) {
  if (!ValidarCEP(cep))
    return false;
  var logradouro = $(cep).data("logradouro");
  if (logradouro == undefined)
    logradouro = "logradouro";
  var numero = $(cep).data("numero");
  if (numero == undefined)
    numero = "numero";
  var complemento = $(cep).data("complemento");
  if (complemento == undefined)
    complemento = "complemento";
  var bairro = $(cep).data("bairro");
  if (bairro == undefined)
    bairro = "bairro";
  var municipio = $(cep).data("municipio");
  if (municipio == undefined)
    municipio = "municipio";
  var id_municipio = $(cep).data("id_municipio");
  if (id_municipio == undefined)
    id_municipio = "id_municipio";
  var uf = $(cep).data("uf");
  if (uf == undefined)
    uf = "uf";
  var logradouro = document.getElementById(logradouro);
  if (logradouro && logradouro.value == "" && cep.value != "") {
    var numero       = document.getElementById(numero);
    var bairro       = document.getElementById(bairro);
    var municipio    = document.getElementById(municipio);
    var id_municipio = document.getElementById(id_municipio);
    var uf           = document.getElementById(uf);
    toastr.info('Pesquisando CEP!');
    $.ajax({
      url: 'https://farlo.com.br/consulta_cep.php',
      type: 'POST',
      data: { cep: cep.value },
      dataType: 'jsonp',
      success: function (data) {
        if (data !== null && data.municipio != "") {
          if (data.logradouro !== undefined && logradouro !== null && data.logradouro !== "")
            logradouro.value = maiuscula(data.logradouro)
          if (data.bairro !== undefined && bairro !== null && data.bairro !== "")
            bairro.value = maiuscula(data.bairro);
          if (data.municipio !== undefined && municipio !== null)
            municipio.value = maiuscula(data.municipio);
          if (data.ibge !== undefined && id_municipio !== null)
            id_municipio.value = data.ibge;
          if (data.uf !== undefined && uf !== null)
            uf.value = data.uf;
          if (data.logradouro.length == 0 || data.logradouro === undefined) {
            toastr.info('CEP único!');
            logradouro.focus();
          } else {
            numero.focus();
          }
          toastr.success('Encontrado!');
        } else {
          toastr.warning('CEP não encontrado ou pesquisa indisponível!');
        }
      },
      error: function (status, erro, request) {
        console.log(status, erro, request);
      }
    });
  }
  return true;
}

//Validar CEP
function ValidarCEP(cep) {
  exp = /\d{5}\-\d{3}/
  if (!exp.test(cep.value) && cep.value.length > 0) {
    toastr.warning('O formato do CEP não confere com o padrão brasileiro!');
    cep.select();
    //cep.value = "";
    return false;
  }
  return true;
}

//validar Telefone
function ValidarTelefone(Objcpf) {
  var cpf = Objcpf.value;
  exp = /\.|\-/g
  if (Objcpf.value.length > 0 && Objcpf.value.length < 14) {
    toastr.warning('Telefone inválido!');
    Objcpf.select();
  }
}

//funcção sleep
function sleep(miliseconds) {
  var currentTime = new Date().getTime();
  while (currentTime + miliseconds >= new Date().getTime()) { }
}

function valuetoform(campo, valor, tabela){
  $("#" + campo).prop("name", tabela + "[" + campo + "]");
  campo = "#" + campo;
  switch ($(campo).prop('type')) {
    case "checkbox":
      if (valor == 1)
        $(campo).prop("checked", true);
      break;
    case "textarea":
      valor = valor.replaceAll("<br>", "\n")
      $(campo).html(valor);
      break;
    case "datetime-local":
      valor = valor.replaceAll(" ", "T")
      $(campo).val(valor);
      break;
    case "text":
      $(campo).val(valor.substr(0, $(campo).prop("maxLength")));
    default:
      $(campo).val(valor);
  }
}

function confirma(mensagem, obj){
  event.preventDefault();
  if (mensagem === undefined)
    mensagem = "Confirma?";
  bootbox.confirm(
    {
      message: mensagem,
      buttons:
      {
        confirm:
        {
          label: 'Sim',
          className: 'btn-success'
        },
        cancel:
        {
          label: 'Não',
          className: 'btn-danger'
        }
      },
      callback: function (result) {
        if (result){
          obj.onclick = "";
          obj.click();
        }
      }
    });
}

//Cria cookie
function sco(name, value, tempo, reload) {
  var date = new Date();
  var minutes = tempo;
  date.setTime(date.getTime() + (minutes * 60 * 1000));
  document.cookie = name + "=" + value + "; expires=" + date.toGMTString();
  if (reload)
    location.reload();
}

var indice_notificacao = 0;
function adiciona_notificacao(tipo, titulo, badge, texto, momento) {
  indice_notificacao ++;
  var vhtml = "<li class='unread' id='notificacao_" + indice_notificacao + "'>"
           + "  <div class='d-flex align-items-center show-child-on-hover'>"
           + "    <span class='d-flex flex-column flex-1'>"
           + "      <span class='name d-flex align-items-center'>" + titulo + " <span class='badge badge-success fw-n ml-1'>" + badge + "</span></span>"
           + "      <span class='msg-a fs-sm'>"
           + "         <br>" + texto
           + "      </span>"
           + "      <span class='fs-nano text-muted mt-1 text-right'>" + momento + "</span>"
           + "    </span>"
           //+ "    <div class='show-on-hover-parent position-absolute pos-right pos-bottom p-3'>"
           //+ "        <a href='#' class='text-muted' title='delete'><i class='fal fa-trash-alt'></i></a>"
           //+ "    </div>"
           + "  </div>"
           + "</li>";
  $("#notificacoes_" + tipo).append(vhtml);
  acerta_titulos_notificacoes();
}

function remove_notificacao(id) {
  $("#notificacao_" + id).remove();
  indice_notificacao--;
  acerta_titulos_notificacoes();
}

function acerta_titulos_notificacoes(){
  if (indice_notificacao > 0) {
    var plural = "ão";
    if (indice_notificacao > 1)
      plural = "ões";
    $("#notificacoes_badge").html(indice_notificacao);
    $("#notificacoes_badge").attr("hidden", false);
    $("#notificacoes_title").attr("title", "Você tem " + indice_notificacao + " notificaç" + plural + "!");
    $("#notificacoes_quantidade").html(indice_notificacao);
  } else {
    $("#notificacoes_badge").html("");
    $("#notificacoes_badge").attr("hidden", true);
    $("#notificacoes_title").attr("title", "Você não tem notificações!");
    $("#notificacoes_quantidade").html("");
  }
}

//Primeira leta maiúscula das palavras
function maiuscula(id) {
  if (id == "") {
    return;
  }
  //palavras para ser ignoradas
  var wordsToIgnore = ["DOS", "DAS", "Dos", "Das", "LTDA", "SA", "ME"],
    minusculas = ["E", "DA", "DE", "DO", "e", "da", "de", "do"],
    minLength = 0;
  //var str = $(id).val();
  var str = id;
  var getWords = function (str) {
    return str.match(/\S+\s*/g);
  }
  var words = getWords(id);
  $.each(words, function (i, word) {
    // somente continua se a palavra nao estiver na lista de ignorados
    if (wordsToIgnore.indexOf($.trim(word)) == -1 && $.trim(word).length > minLength && minusculas.indexOf($.trim(word)) == -1) {
      words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1).toLowerCase();
    } else if (minusculas.indexOf($.trim(word)) != -1) {
      words[i] = words[i].toLowerCase();
    } else {
      words[i] = words[i];
    }
  });
  //this.value = words.join("");
  return words.join("");
}

function arquivo_ia(obj, nome) {
  if (obj.value != "") {
    $("#btr_" + obj.name).attr("style", "");
    $("#dwl_" + obj.name).attr("style", "");
    var file = obj.files[0];
    var blob = new Blob([file]);
    var url = URL.createObjectURL(blob);
    var extensao = (obj.files[0].name).split('.').pop();
    $("#dwl_" + obj.name).attr("href", url);
    $("#dwl_" + obj.name).attr("download", nome + extensao);
    $("#dwl_" + obj.name).html(nome + extensao);
    $("#btia_" + obj.name).html("Alterar");
  }
}

function arquivo_remover(arquivo) {
  $('#fl_' + arquivo).val('');
  $('#' + arquivo).val('');
  $("#btr_" + arquivo).attr("style", "display:none");
  $("#dwl_" + arquivo).attr("style", "display:none");
  $("#btia_" + arquivo).html("Incluir");
}

function spinner_show(tag) {
  if (typeof spinner_off === "undefined") {
    element = document.getElementById(tag);
    //element.insertAdjacentHTML("afterend"
    //                         , "<div class='spinner-border' style='position: fixed; left: 50%; top: 50%;' role='status'>"
    //                         + "  <span class='sr-only'>Aguarde...</span>"
    //                         + "</div>");
    element.insertAdjacentHTML("afterend", "<i class='fa fa-spinner fa-spin-4x fa-4x text-warning' style='position: fixed; left: 50%; top: 50%;' title='Aguarde...'></i>");
  }
}