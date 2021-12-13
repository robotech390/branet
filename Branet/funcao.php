<?php

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

if (isset($_GET["cadastrar"])) {

    $nome = $_GET["nome"];
    $datanasc = $_GET["datanasc"];

    $sql = "INSERT INTO dados(nome, datanasc) VALUES ('$nome','$datanasc');";
    $registros = mysqli_query($conexao, $sql);

    return readfile("index.html");

}

if(isset($_GET["consultar"])){

    $sql = "SELECT * FROM dados;";
    $registros = mysqli_query($conexao, $sql);

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

        while ($result = mysqli_fetch_assoc($registros)) {

            $dataAtual = date('Y/m/d');
            $dataBd = date_create($result['datanasc']);
            $dataBd2 = $result['datanasc'];

            //-----------idade---------------------

            $idade = date_diff($dataBd, date_create($dataAtual));

            //-----------dias restantes do aniver------------------

            $dataAtual_arr = explode('/', $dataAtual);

            $dataBD_arr = explode('-', $dataBd2);

            $anoDoAniver = $dataAtual_arr[0] . "-" . $dataBD_arr[1] . "-" . $dataBD_arr[2];

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


function calculodias($data1, $data2, $dataBdArray)
{

    if (strtotime($data2) < time()) {

        $data3 = date("Y/m/d", strtotime("+ 1 year"));

        $dataArrayAtual = explode("/", $data3);

        $data2 = $dataArrayAtual[0] . "-" . $dataBdArray[1] . "-" . $dataBdArray[2];

        $diff = strtotime($data2) - strtotime($data1);
        return abs(round($diff / 86400));
    } else {
        $diff = strtotime($data2) - strtotime($data1);
        return abs(round($diff / 86400));
    }
}
