<?php
	include 'conn.php';
	$logErros = array(
		"empty" => 1,
		"invalidFormat" => 0,
		"havePhoneErrors" => 0,
		"haveEmailErrors" => 0,
	);


	if (isset($_FILES["arquivo"])) {
		if ($_FILES["arquivo"]["error"] == 0 && $_FILES["arquivo"]["type"] == 'text/csv') {
			$logErros['empty'] = 0;

			$campanha = isset($_POST['campanha']) ? $_POST['campanha'] : '';
			$file_name = $_FILES["arquivo"]["name"];
			$file_tmp = $_FILES["arquivo"]["tmp_name"];

			//Movo o arquivo para um diretorio temporario
			move_uploaded_file($file_tmp, "arquivos/" . $file_name);

			$handle = fopen("arquivos/" . $file_name, "r");

			$csv_data = array();

			while (($line = fgets($handle)) !== false) {
				// Remove quebras de linha (\n e \r) da linha
				$line = str_replace(array("\r", "\n"), '', $line);

				// Abre o arquivo CSV para leitura
				$handle_csv = fopen('php://memory', 'r+');
				fwrite($handle_csv, $line);
				rewind($handle_csv);

				// Lê os dados do CSV
				$data = fgetcsv($handle_csv, 1000, ";");

				// Processa os dados como desejado
				$csv_data[] = $data;

				// Fecha o arquivo CSV após a leitura
				fclose($handle_csv);
			}
		//Fecho o diretorio temporario
		fclose($handle);

		} elseif ($_FILES["arquivo"]["error"] == 4) {
			$logErros['empty'] = 1;

		} elseif ($_FILES["arquivo"]["type"] != 'text/csv') {
			$logErros['invalidFormat'] = 1;

		} else {
			end;
		}
	}

	//Se o arquivo estiver preenchido, percorro ele.
	if (isset($csv_data)) {
		foreach ($csv_data as $key => $value) {
			if (isset($value[3])) {

				$contatoTel = $value[3];
				$contatoEmail = $value[2];

				//Verifica o telefone
				//Se está vazio
				if ($contatoTel == '' || $contatoTel == NULL) {
					$logErros['havePhoneErrors'] += 1;

					//Se está no formato desejado
				} elseif (!validarTelefone($contatoTel)) {
					$logErros['havePhoneErrors'] += 1;
				}

				//Verifica o e-mail
				//Se está vazio
				if($contatoEmail == '' || $contatoEmail == NULL) {
					$logErros['haveEmailErrors'] += 1;
				} elseif (!validarEmail($contatoEmail)) {
					$logErros['haveEmailErrors'] += 1;
				}
			}

		}

		//EM CASO DE ERROS, NÃO SALVAR DADOS NO BANCO
		if (array_sum($logErros)) {
			
		} else {
		//INPUT NO ANCO AQUI	

			// campanha 
			// cep 
			// cidade 
			// email 
			// endereco 
			// ID 
			// nascimento 
			// nome 
			// sobrenome 
			// telefone 
			foreach ($csv_data as $key => $value) {

				//Caso o telefone atenda os requisitos, removo os caracteres indesejados
				$str_indesejados = ["(", ")", " ", "-"];
				$value[3] =  str_replace($str_indesejados, '', $value[3]);

				//Altero o formato da data para que seja salva no banco de dados no padrão americano
				$data_obj = DateTime::createFromFormat('d/m/Y',$value[7]);
				$value[7] = $data_obj->format('Y-m-d');

				$myquery = $mysqli->prepare("INSERT INTO contatos (nome, sobrenome, email, telefone, endereco, cidade, cep, nascimento, campanha) VALUES(?,?,?,?,?,?,?,?,?)");

				if($myquery === false){
					die("Erro na preparação da consulta" . $myquery->error);
				}

				$myquery->bind_param("sssssssss",$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6],$value[7],$campanha);

				$resultadoConexao = $myquery->execute();

				if($resultadoConexao === false){
					die("Erro na execução da consulta: ". $myquery->error);
				}

				$myquery->close();
			}
		

	
		}
	}

$jsonErros = json_encode($logErros);

function validarTelefone($telefone)
{

	//Aqui uso uma expressão regular que verifica os seguintes critérios:
	//Se possui DDD com 2 digitos após o 55
	//Se possui 5 dígitos antes do hífen
	//Se possui 4 dígitos após o hífen

	if (isset($telefone)) {
		$regex = '/^55\(\d{2}\)\d{5}-\d{4}$/';

		if (preg_match($regex, $telefone)) {
			return true;
		} else {
			return false;
		}
	}

}

function validarEmail($email){
	if (isset($email)) {

		//Com esta expressão regular, verifico se o e-mail está no seguinte padrão:
			//teste@teste.com
			//teste@teste.com.br
			//Caso esteja em qualquer outro formato, será dado como inválido
		$regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|com\.br)$/';

		if (preg_match($regex, $email)) {
			return true;
		} else {
			return false;
		}

	}
}

?>

<script>

	let logErrors = <?php echo $jsonErros ?>

	if (logErrors['invalidFormat']) {
		confirm('Formato inválido, selecione um arquivo CSV')

	//Abaixo aviso quando há erros nos emails e telefones
	} else if (logErrors['havePhoneErrors'] && logErrors['haveEmailErrors']) {
		let confirm = alert("Atenção, há telefones e e-mails vazios ou no formato incorreto. Gentileza corrigir. \n Erros de telefone: " + logErrors['havePhoneErrors'] + ', erros de e-mails: ' + logErrors['haveEmailErrors'])

	//Abaixo aviso quando há erros somente nos telefones
	} else if(logErrors['havePhoneErrors']){
		let confirm = alert("Atenção, há telefones vazios ou no formato incorreto. Gentileza corrigir. \n Erros: " + logErrors['havePhoneErrors'])

	//Abaixo aviso quando há erros somente nos e-mails
	} else if(logErrors['haveEmailErrors']){
		let confirm = alert("Atenção, há emails vazios ou no formato incorreto. Gentileza corrigir. \n Erros: " + logErrors['haveEmailErrors'])

	} else if (logErrors['invalidFormat'] == 0 && logErrors['empty'] == 0) {
		confirm('Arquivo enviado com sucesso.')
		//window.location.href = "list.php";
	}


</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Campanha ASC Brazil</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
	<link href="styles.css" rel="stylesheet" />
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-primary bg-gradient">
		<div class="container-fluid">
			<a class="navbar-brand text-white" href="#">ASC Brazil</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse"
				data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
				aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mb-2 mb-lg-0 right">
					<li class="nav-item">
						<a class="nav-link active text-white" aria-current="page" href="">
							Cadastro de Contatos
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-white" href="list.php">
							Lista Contatos
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<main>
		<div class="container center">
			<div id="form-card">
				<form method="POST" enctype="multipart/form-data" name="myform" action="">
	
					<label id="id_campanha" for="">Identificação da Campanha: </label>
					<div class="input-group mb-3">
						<input type="text" required="true" class="form-control" name="campanha" id="camp_desc"
							aria-describedby="basic-addon3" placeholder="Ex: Campanha Promoção de Agosto 2024">
					</div>
	
					<div class="input-group">
						<div class="">
							<input type="file" required="true" name="arquivo" class="" id="">
							<label class="file-input"  for="fileInput"></label>
						</div>
					</div>
	
					<button type="submit" class="btn btn-primary">Enviar</button>
				</form>
			</div>



		</div>
	</main>

	<footer>

	</footer>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
		crossorigin="anonymous"></script>
</body>

</html>