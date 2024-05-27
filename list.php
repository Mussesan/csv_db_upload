<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Campanha ASC Brazil</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
	<link href="list-styles.css" rel="stylesheet" />

	<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
		crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

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
						<a class="nav-link active text-white" aria-current="page" href="index.php">
							Cadastro de Contatos
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-white" href="">
							Lista Contatos
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<main>
		<div class="container center">

			<div id="search-div">
				<div class="search-card">
					<label for="">Pesquisar Campanha:</label>
					<input type="text" id="txtBusca" placeholder="Filtrar por campanha...">
				</div>
			</div>

			<table class="table table-striped">
				<thead>
					<tr>
						<th scope="col"></th>
						<th scope="col">Campanha</th>
						<th scope="col">Nome</th>
						<th scope="col">Sobrenome</th>
						<th scope="col">Email</th>
						<th scope="col">Telefone</th>
						<th scope="col">Endereço</th>
						<th scope="col">Cidade</th>
						<th scope="col">CEP</th>
						<th scope="col">Nascimento</th>
					</tr>
				</thead>
				<tbody id="result">

				</tbody>
			</table>





		</div>
	</main>

	<footer>

	</footer>

	<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chama a função de busca ao carregar o documento
            fetchContatos('');

            // Adiciona o evento keyup ao campo de busca
            document.getElementById('txtBusca').addEventListener('keyup', function() {
                fetchContatos(this.value);
            });
        });

        function fetchContatos(txtBusca) {
            fetch('http://localhost/ascbrasil/api/public/v1/get_campanha/', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ txtBusca: txtBusca })
            })
            .then(response => response.json())
            .then(res => {
                let tabelaContatos = '';

                if (res.data.result) {
                    res.data.values.forEach(contato => {
                        let dataNascimento = formataData(contato[8]);

                        tabelaContatos += `
                            <tr>
                                <td>${contato[0]}</td>
                                <td>${contato[9]}</td>
                                <td>${contato[1]}</td>
                                <td>${contato[2]}</td>
                                <td>${contato[3]}</td>
                                <td>${contato[4]}</td>
                                <td>${contato[5]}</td>
                                <td>${contato[6]}</td>
                                <td>${contato[7]}</td>
                                <td>${dataNascimento}</td>
                            </tr>
                        `;
                    });
                    document.getElementById('result').innerHTML = tabelaContatos;
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Função para formatar data
        function formataData(data) {
            let dataObj = new Date(data);
            let dia = dataObj.getDate().toString().padStart(2, '0');
            let mes = (dataObj.getMonth() + 1).toString().padStart(2, '0');
            let ano = dataObj.getFullYear();
            return `${dia}/${mes}/${ano}`;
        }
    </script>

</body>

</html>