<?php

require_once '../../Config.inc.php';
$create = new Create;
$read = new Read;
$delete = new Delete;

session_start();

$sair = filter_input(INPUT_GET, 'sair');

if (!(isset($_SESSION['id']))) {
    session_destroy();
}

$idCliente = $_SESSION['id'] ?? null;
if ($sair == "sair") {
    session_destroy();
    header("Location: ../produto/homeController.php");
}

$arrayVazio = [];
$arrayCliente = [];
$arrayCarrinho = [];

View::load("../templates/navbarProduto.tpl.html");
$read->exeRead("cliente", "WHERE id = :id", "id={$idCliente}");
$array = $read->getResult();
$nomeCliente = $array[0]['nome'] ?? null;
unset($array[0]['nome']);
$array[0]['nomeCliente'] = $nomeCliente;
$array[0]['email'] = $array[0]['email'] ?? null;
$read->fullRead("SELECT produto.id, produto.nome AS 'nomeProduto', carrinho.quantidade, produto.preco, produto.categoria FROM carrinho INNER JOIN produto ON carrinho.idProduto = produto.id WHERE carrinho.idCliente = :idCliente", "idCliente={$idCliente}");
$arrayCarrinho = $read->getResult();
$html = '';
foreach ($arrayCarrinho as $value) {
    $value['nomeProduto'] = strlen($value['nomeProduto']) > 25 ? substr($value['nomeProduto'], 0, strrpos(substr($value['nomeProduto'], 0, 25), ' ')) . '...' : $value['nomeProduto'];
    $html .= View::show($value);
}
$arr['navbarProduto'] = $html;
$array[0]['navbarProduto'] = $arr['navbarProduto'];
if (isset($_SESSION['id'])) {
    $array[0]['displayAccount'] = "block";
    $array[0]['displayLogin'] = "none";
} else {
    $array[0]['displayAccount'] = "none";
    $array[0]['displayLogin'] = "block";
}
View::load("../templates/navbar.tpl.html");
$navbar = View::show($array[0]);


View::load("./view/templates/listaFavorito.tpl.html");
$read->fullRead("SELECT produto.id, produto.nome AS 'nomeProduto', produto.preco, produto.categoria, produto.descricao, media.nome as 'image' FROM desejo INNER JOIN produto ON desejo.idProduto = produto.id INNER JOIN media ON desejo.idProduto = media.idProduto WHERE desejo.idCliente = :idCliente", "idCliente={$idCliente}");
$array = $read->getResult();
$html = '';
foreach ($array as $value) {
    $html .= View::show($value);
}

View::load("../templates/footer.tpl.html");
$footer = View::show($arrayVazio);

View::load("./view/favorito.html");
if ($html != null) {
    $arr['lista'] = $html;
    $arr['texto'] = "Meus Pedidos<i class='material-icons medium left'>shopping_basket</i>";
} else {
    $arr['lista'] = null;
    $arr['texto'] = "Nao ha produtos aqui<i class='material-icons medium left'>shopping_cart</i>";
}
$arr['footer'] = $footer;
$arr['navbar'] = $navbar;
echo View::show($arr);


