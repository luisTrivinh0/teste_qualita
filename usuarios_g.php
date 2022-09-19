<?php
  require_once("inc/g_header.php");
  if (isset($_POST->gravar)){
    $_POST->msisdn = "+55" . trim(str_replace("(", "", str_replace(")", "", str_replace("-", "", str_replace(" ", "", $_POST->msisdn)))));
    $senha = md5($_POST->password);
    query("INSERT INTO
            usuarios( msisdn
                    , name
                    , access_level
                    , password)
             VALUES ('$_POST->msisdn'
                   , '$_POST->name'
                   , '$_POST->access_level'
                   , '$senha');");
    $id = query('SELECT external_id FROM usuarios WHERE msisdn = "'.$_POST->msisdn.'"');
    $id = mysqli_fetch_object($id);
    $url = 'https://api.mlearn.mobi/integrator/qualifica/users';
    $ch = curl_init($url);
    $data = array(
        "msisdn"       => $_POST->msisdn,
	      "name"         => $_POST->name,
	      "access_level" => $_POST->access_level,
	      "password"     => $_POST->password,
        "external_id"  => $id->external_id

      );
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type:application/json',
      'Authorization:Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI0IiwianRpIjoiMDY3YTEyMGZmMzg0YWIwYzdjN2UxZmJjYTllZDUwY2JhMGEyMDY4YzQzNGJlNDE4MTJiMmM3NTA5ZWE3NDk5ZGM1ZjllOWVlNjc5ZGM2MzkiLCJpYXQiOjE2NDUxMDQ5NDkuMDA4NzE3LCJuYmYiOjE2NDUxMDQ5NDkuMDA4NzIxLCJleHAiOjE2NzY2NDA5NDguOTk5NzAyLCJzdWIiOiIiLCJzY29wZXMiOlsicmVhZDptYW5hZ2VfdXNlcnMiLCJ3cml0ZTptYW5hZ2VfdXNlcnMiXX0.M3i1zErf6fBqIrI8ddJkXN443hdj0W7MrIP2A1GlmoWLGTo_05Z0P0sDs0f_YgnpxgiwUdC_X-8P39LxUeBmHv5CouQSxdZlQTQwvPkB1rFrAmqoPKYtnP8843TAB90gGAa9AzrQMhlcN5DWqr_LrNy7UNetccloRyNTIPHE6KVgJaUNQVmn3jPcAe4z8qX16-IK-vh-yKRu-PHRU0DY9ukcfUMjg28W1hKkwBNRZ3e5PYFIs32fe21U-rjINytgujRey8B1TVh63jWD3Yqpqt9ChD1ml2i4ksH_h7bKp8xLUUkv0IY9_36s8Y53eVI4cUJb44DDFcPdkwuP2BQaXPzaTKabZhIOWVPG7byqJGuh1wsh-O4PTf5Raa8IxPqLsuTXn9yXdGhOGRwW8pzToDxXfWFhTSiDcOTOg3tsGmWkNc6dscK0xUPndgLXMhBrr6wf0vXDCDlneUTmmftogvusHpFL-dGpS2mgxoAkk78fgJ04E9P6fXreAYRgH8L4D1E7w1d2RB-0kIYf_-nLKHnR1Q_cVYs4BRKaY2E-ijQ7PQk9HtV3Na5ytUHnbjATG_jHe0Ou4CWNSv2y8uYxMk1_R2tN_C-TsOUaXUHLWHB7Avq_883lVwWf0Bu7CwgXnuH0iUdwWeRsXR7CnQxVEokCq_AT3ZmlVbSUKBemBIk',
      'service-id:qualifica',
      'app-users-group-id:1'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
  } else if(isset($_POST->upgrade)){
    query('UPDATE usuarios SET access_level = "premium" WHERE external_id = '. $_POST->upgrade.';');
  }else if(isset($_POST->downgrade)){
    query('UPDATE usuarios SET access_level = "pro" WHERE external_id = ' . $_POST->downgrade . ';');
  }
  header("Location: index.php");
?>