<?php

require_once 'includes/Controller.php';
require_once 'system/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// GET route
$app->get('/', function () {
    require_once "view/home.php";
});

// GET route
$app->get('/home', function () {
    require_once "view/home.php";
});

// GET route
$app->get('/videos', function () {
    require_once "view/videos.php";
});

// GET route
$app->get('/shop', function () {
    require_once "view/shop.php";
});

//Nesse serviço será selecionado os 3 maiores produtos
//que estejam na promoção em ordem decrescente
$app->get('/produtos', function (){  
    
    $sql = new Controller();
    $dados = $sql->select("SELECT * FROM tb_produtos WHERE preco_promorcional > 0 ORDER BY preco_promorcional DESC LIMIT 3");
    
    foreach ($dados as &$produto) {
        $preco = $produto['preco'];
        $centavos = explode(".", $preco);
        $produto['preco'] = number_format($preco, 0, ",", ".");
        $produto['centavos'] = end($centavos);
        $produto['parcelas'] = 10;
        $produto['parcela'] = number_format($preco/$produto['parcelas'], 2, ",", ".");
        $produto['total'] = number_format($preco, 2, ",", ".");
    }
    
    echo json_encode($dados);
    
});

$app->get('/produtos-mais-buscados', function () {
    
    $sql = new Controller();
    $dados = $sql->select("SELECT tb_produtos.id_prod, tb_produtos.nome_prod_curto, tb_produtos.nome_prod_longo,"
                              . " tb_produtos.codigo_interno, tb_produtos.id_cat, tb_produtos.preco, tb_produtos.peso,"
                              . " tb_produtos.largura_centimetro, tb_produtos.altura_centimetro, tb_produtos.quantidade_estoque,"
                              . " tb_produtos.preco_promorcional, tb_produtos.foto_principal, tb_produtos.visivel,"
                              . " CAST(AVG(review) AS DEC(10,2)) AS media, COUNT(id_prod) AS total_reviews"
                       . " FROM tb_produtos INNER JOIN tb_reviews USING(id_prod) GROUP BY" 
                            . " tb_produtos.id_prod, tb_produtos.nome_prod_curto, tb_produtos.nome_prod_longo,
                                tb_produtos.codigo_interno, tb_produtos.id_cat, tb_produtos.preco, tb_produtos.peso,
                                tb_produtos.largura_centimetro, tb_produtos.altura_centimetro, tb_produtos.quantidade_estoque,
                                tb_produtos.preco_promorcional, tb_produtos.foto_principal, tb_produtos.visivel LIMIT 4 ");
    
    foreach ($dados as &$produto) {
        $preco = $produto['preco'];
        $centavos = explode(".", $preco);
        $produto['preco'] = number_format($preco, 0, ",", ".");
        $produto['centavos'] = end($centavos);
        $produto['parcelas'] = 10;
        $produto['parcela'] = number_format($preco/$produto['parcelas'], 2, ",", ".");
        $produto['total'] = number_format($preco, 2, ",", ".");
    }
    
    echo json_encode($dados);
    
});

$app->get('/produto-:id_prod', function ($id_prod){
    
    $sql = new Controller();
    $produtos = $sql->select("SELECT * FROM tb_produtos WHERE id_prod = $id_prod");
    $produto = $produtos[0];
    
    $preco = $produto['preco'];
    $centavos = explode(".", $preco);
    $produto['preco'] = number_format($preco, 0, ",", ".");
    $produto['centavos'] = end($centavos);
    $produto['parcelas'] = 10;
    $produto['parcela'] = number_format($preco/$produto['parcelas'], 2, ",", ".");
    $produto['total'] = number_format($preco, 2, ",", ".");
    
    require_once "view/shop-produto.php";
    
});

$app->get('/cart', function (){
    require_once 'view/cart.php';
});

$app->get('/carrinho-dados', function(){
    
    $sql = new Controller();
    $result = $sql->select("CALL sp_carrinhos_get('".  session_id() ."')");
    
    $carrinho = $result[0];
    
    $sql = new Controller();
    $carrinho['produtos'] = $sql->select("CALL sp_carrinhosprodutos_list(".  $carrinho['id_car'] .")");
    
    $carrinho['total_car'] = number_format((float)$carrinho['total_car'], 2, ',', '.');
    $carrinho['subtotal_car'] = number_format((float)$carrinho['subtotal_car'], 2, ',', '.');
    $carrinho['frete_car'] = number_format((float)$carrinho['frete_car'], 2, ',', '.');
    
    echo json_encode($carrinho);
    
});

$app->get('/carrinhoAdd-:id_prod', function($id_prod){
    
    $sql = new Controller();
    $result = $sql->select("CALL sp_carrinhos_get('".  session_id() ."')");
    $carrinho = $result[0];
    
    $sql = new Controller();
    $sql->query("CALL sp_carrinhosprodutos_add(". $carrinho['id_car'] .",".  $id_prod .")");
    
    header("Location: cart");//Redireciona para a rota cart
    exit;
    
});

$app->delete('/carrinhoRemoveAll-:id_prod', function ($id_prod){
    
    $sql = new Controller();
    $result = $sql->select("CALL sp_carrinhos_get('".  session_id() ."')");
    $carrinho = $result[0];
    
    $sql = new Controller();
    $sql->query("CALL sp_carrinhosprodutostodos_rem(". $carrinho['id_car'] .",".  $id_prod .")");
    
    echo json_encode(array(
        "success"=>true
    ));
    
});

$app->post('/carrinho-produto', function (){
    
    $dados = json_decode(file_get_contents("php://input"), true);
    
    $sql = new Controller();
    $result = $sql->select("CALL sp_carrinhos_get('".  session_id() ."')");
    $carrinho = $result[0];
    
    $sql = new Controller();
    $sql->query("CALL sp_carrinhosprodutos_add(". $carrinho['id_car'] .",".  $dados['id_prod'] .")");
    
    echo json_encode(array(
        "success"=>true
    ));
    
});

$app->delete('/carrinho-produto', function (){
    
    $dados = json_decode(file_get_contents("php://input"), true);
    
    $sql = new Controller();
    $result = $sql->select("CALL sp_carrinhos_get('".  session_id() ."')");
    $carrinho = $result[0];
    
    $sql = new Controller();
    $sql->query("CALL sp_carrinhosprodutos_rem(". $carrinho['id_car'] .",".  $dados['id_prod'] .")");
    
    echo json_encode(array(
        "success"=>true
    ));
    
});

$app->get('/calcular-frete-:cep', function ($cep){

    require_once 'includes/Frete.php';
    
    $sql = new Controller();
    $result = $sql->select("CALL sp_carrinhos_get('".  session_id() ."')");
    $carrinho = $result[0];
    
    $sql = new Controller();
    $produtos = $sql->select("CALL sp_carrinhosprodutosfrete_list(". $carrinho['id_car'] .")");
    
    $peso = 0; 
    $comprimento = 0; 
    $altura = 0;
    $largura = 0; 
    $valor = 0;
    
    foreach ($produtos as $produto){
        $peso =+ $produto['peso'];
        $comprimento =+ $produto['comprimento'];
        $altura =+ $produto['altura'];
        $largura =+ $produto['largura'];
        $valor =+ $produto['preco'];
    }
    
    $cep = trim(str_replace('-', '', $cep));
    
    $frete = new Frete(
        $cepDeOrigem = '01418100', 
        $cepDeDestino = $cep, 
        $peso, 
        $comprimento, 
        $altura, 
        $largura, 
        $valor
    );
    
    $sql = new Controller();
    $sql->query("UPDATE tb_carrinhos SET cep_car = '".$cep."', frete_car = ".$frete->getValor().", prazo_car = ".$frete->getPrazoEntrega()." WHERE id_car = ". $carrinho['id_car']);
    
    echo json_encode(array(
        'peso' => $peso, 
        'comprimento' => $comprimento, 
        'altura' => $altura, 
        'largura' => $largura, 
        'valor' => $valor,
        'dimencao' => $peso * $comprimento * $largura
    ));
});

$app->run();
