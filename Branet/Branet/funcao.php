<?php

// criação do banco de dados e verificação se esta funcionando

$conexao = mysqli_connect('localhost', 'root', '');
$banco = mysqli_select_db($conexao, 'branet');

if (!$conexao) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "CREATE TABLE `branet`.`dados` 
        ( `id` INT NOT NULL AUTO_INCREMENT , 
        `nome` VARCHAR(50) NOT NULL , 
        `datanasc` DATE NOT NULL , 
        PRIMARY KEY (`id`)) 
        ENGINE = InnoDB; ";

mysqli_query($conexao, $sql);

//cadastro de dados

if (isset($_GET["cadastrar"])) {

    //pega os dados e insere no banco e reescreve a tela de cadastro para não ter a "Tela branca" de um botão submit.

    $nome = $_GET["nome"];
    $datanasc = $_GET["datanasc"];

    $sql = "INSERT INTO dados(nome, datanasc) VALUES ('$nome','$datanasc');";
    $registros = mysqli_query($conexao, $sql);

    return readfile("index.html");

}

//Consultar os dados

if(isset($_GET["consultar"])){

    //Seleciona os dados da tabela dados

    $sql = "SELECT * FROM dados;";
    $registros = mysqli_query($conexao, $sql);

    //Caso a consulta esteja correta cria a tabela com os dados

    if ($registros) {

        echo "<table border='1'>
                <thead>
                    <tr>
                        <td>Nome</td>
                        <td>Data de Nascimento</td> 
                        <td>Idade </td>
                        <td>Dias até o próximo aniversario</td>
                    <tr>
                </thead>
                
                <tbody>";

        //Laço para ter registros na tabela, continuar mostrando na tabela

        while ($result = mysqli_fetch_assoc($registros)) {

            //Pega a data atual e a data do Banco de dados, depois faz a subtração para descobrir a idade da pessoa.

            $dataAtual = date('Y/m/d');
            $dataBd = date_create($result['datanasc']);
            $dataBd2 = $result['datanasc'];

            $idade = date_diff($dataBd, date_create($dataAtual));

            //Cria duas arrays para poder fazer a conta dos dias restantes, pois eu aumento o ano da data do aniversario para verificar se ja passou ou não

            $dataAtual_arr = explode('/', $dataAtual);

            $dataBD_arr = explode('-', $dataBd2);

            $anoDoAniver = $dataAtual_arr[0] . "-" . $dataBD_arr[1] . "-" . $dataBD_arr[2];

            //Executa a função para calcular os dias

            $dias = calculodias($dataAtual, $anoDoAniver, $dataBD_arr);

            echo "<tr>
                    <td> $result[nome] </td>
                    <td> $result[datanasc] </td> 
                    <td>" . $idade->format("%y") . "</td>
                    <td>" . $dias . "</td> 
                </tr>";
        }

        echo "</tbody></table>";
    }
}


//Função para calcular os dias

function calculodias($data1, $data2, $dataBdArray)
{

    //Faço uma condição para ver se a data do aniversario ja passou

    if (strtotime($data2) < time()) {

        //Crio uma data para o tempo futuro e fazer a conta

        $data3 = date("Y/m/d", strtotime("+ 1 year"));

        //Crio um array da data futura

        $dataArrayAtual = explode("/", $data3);

        //Crio a data do aniversario do ano que vem e faço a conta para descobrir os dias restantes do ano que vem

        $data2 = $dataArrayAtual[0] . "-" . $dataBdArray[1] . "-" . $dataBdArray[2];

        $diff = strtotime($data2) - strtotime($data1);
        return abs(round($diff / 86400));
    } else {
        //caso a data seja maior que o dia atual, eu só faço a conta
        $diff = strtotime($data2) - strtotime($data1);
        return abs(round($diff / 86400));
    }
}
