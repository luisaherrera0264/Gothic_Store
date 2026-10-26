<?php
require_once 'conexao.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$pagamento = isset($_SESSION['checkout_pagamento']) ? $_SESSION['checkout_pagamento'] : "Não informado";
$frete = isset($_SESSION['checkout_frete']) ? $_SESSION['checkout_frete'] : "Não informado";
$total_final = isset($_SESSION['checkout_total']) ? $_SESSION['checkout_total'] : 0;
$valor_frete = isset($_SESSION['checkout_valor_frete']) ? $_SESSION['checkout_valor_frete'] : 0;

unset($_SESSION['checkout_pagamento']);
unset($_SESSION['checkout_frete']);
unset($_SESSION['checkout_total']);
unset($_SESSION['checkout_valor_frete']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Finalizada - Gothic Store</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="menu.css">
    <style>
        .sucesso-container {
            max-width: 600px;
            margin: 80px auto;
            text-align: center;
            background: #1a1a1a;
            padding: 50px;
            border-radius: 10px;
            border: 1px solid #6a1b9a;
            box-shadow: 0 0 20px rgba(156, 39, 176, 0.2);
        }
        .sucesso-container h1 {
            font-family: 'Montserrat', sans-serif;
            color: #9c27b0;
            font-size: 32px;
            margin-bottom: 20px;
        }
        .sucesso-container p {
            color: #a0a0a0;
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .resumo-box {
            background: #0a0a0a;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #333;
            margin-bottom: 30px;
            text-align: left;
        }
        .resumo-box p {
            margin-bottom: 10px;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
        }
        .resumo-box strong {
            color: #9c27b0;
        }
        .total-final {
            border-top: 1px solid #333;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 20px;
            font-weight: bold;
        }
        .sucesso-container .btn {
            width: auto;
            padding: 12px 30px;
        }
    </style>
</head>
<body>

    <header>
        <h1>Gothic <span>Store</span></h1>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="catalogo.php">Catálogo</a></li>
                <li><a href="carrinho.php">Carrinho 🛒</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="sucesso-container">
            <h1>Compra Finalizada! 🦇</h1>
            <p>Obrigado por comprar na Gothic Store. Seu pedido foi processado com sucesso e o estoque já foi atualizado no sistema.</p>
            
            <div class="resumo-box">
                <p><span>Forma de Pagamento:</span> <strong><?php echo $pagamento; ?></strong></p>
                <p><span>Opção de Frete:</span> <strong><?php echo $frete; ?></strong></p>
                <p><span>Valor do Frete:</span> <strong>R$ <?php echo number_format($valor_frete, 2, ',', '.'); ?></strong></p>
                
                <p class="total-final"><span>Total Pago:</span> <span style="color: #9c27b0;">R$ <?php echo number_format($total_final, 2, ',', '.'); ?></span></p>
                
                <p style="font-size: 12px; color: #666; margin-top: 15px; display: block;">(Este é um ambiente de simulação acadêmica)</p>
            </div>
            
            <a href="catalogo.php" class="btn">Voltar ao Catálogo</a>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Gothic Store - Todos os direitos reservados. 🦇</p>
    </footer>

</body>
</html>