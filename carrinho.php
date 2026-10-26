<?php
require_once 'conexao.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = "";

// --- AÇÃO: FINALIZAR COMPRA (BAIXAR ESTOQUE) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_compra'])) {
    $sql_itens = "SELECT c.produto_id, c.quantidade, p.nome, p.estoque 
                  FROM carrinho c 
                  JOIN produtos p ON c.produto_id = p.id 
                  WHERE c.usuario_id = ?";
    $stmt_itens = $conn->prepare($sql_itens);
    $stmt_itens->bind_param("i", $usuario_id);
    $stmt_itens->execute();
    $resultado_itens = $stmt_itens->get_result();
    
    $tem_estoque_suficiente = true;
    $itens_para_atualizar = [];

    while ($item = $resultado_itens->fetch_assoc()) {
        if ($item['quantidade'] > $item['estoque']) {
            $tem_estoque_suficiente = false;
            $mensagem = "Estoque insuficiente para: " . $item['nome'];
            break;
        }
        $itens_para_atualizar[] = $item;
    }

    if ($tem_estoque_suficiente) {
        foreach ($itens_para_atualizar as $item) {
            $novo_estoque = $item['estoque'] - $item['quantidade'];
            $stmt_up = $conn->prepare("UPDATE produtos SET estoque = ? WHERE id = ?");
            $stmt_up->bind_param("ii", $novo_estoque, $item['produto_id']);
            $stmt_up->execute();
            $stmt_up->close();
        }

        $stmt_del = $conn->prepare("DELETE FROM carrinho WHERE usuario_id = ?");
        $stmt_del->bind_param("i", $usuario_id);
        $stmt_del->execute();
        $stmt_del->close();

        // Salva as escolhas e valores na sessão
        $_SESSION['checkout_pagamento'] = $_POST['pagamento'];
        $_SESSION['checkout_frete'] = $_POST['frete'];
        
        $valor_frete = 0;
        if ($_POST['frete'] == 'Sedex') $valor_frete = 15.00;
        elseif ($_POST['frete'] == 'PAC') $valor_frete = 8.00;
        
        $total_produtos = floatval($_POST['total_produtos']);
        
        $_SESSION['checkout_total'] = $total_produtos + $valor_frete;
        $_SESSION['checkout_valor_frete'] = $valor_frete;

        header("Location: finalizar.php");
        exit();
    }
}

// --- AÇÃO: ADICIONAR PRODUTO ---
if (isset($_GET['add'])) {
    $produto_id = intval($_GET['add']);
    $stmt = $conn->prepare("SELECT id, quantidade FROM carrinho WHERE usuario_id = ? AND produto_id = ?");
    $stmt->bind_param("ii", $usuario_id, $produto_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $item = $resultado->fetch_assoc();
        $nova_qtd = $item['quantidade'] + 1;
        $stmt_update = $conn->prepare("UPDATE carrinho SET quantidade = ? WHERE id = ?");
        $stmt_update->bind_param("ii", $nova_qtd, $item['id']);
        $stmt_update->execute();
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO carrinho (usuario_id, produto_id, quantidade) VALUES (?, ?, 1)");
        $stmt_insert->bind_param("ii", $usuario_id, $produto_id);
        $stmt_insert->execute();
    }
    $stmt->close();
    header("Location: carrinho.php");
    exit();
}

// --- AÇÃO: REMOVER PRODUTO ---
if (isset($_GET['remover'])) {
    $carrinho_id = intval($_GET['remover']);
    $stmt_del = $conn->prepare("DELETE FROM carrinho WHERE id = ? AND usuario_id = ?");
    $stmt_del->bind_param("ii", $carrinho_id, $usuario_id);
    $stmt_del->execute();
    $stmt_del->close();
    header("Location: carrinho.php");
    exit();
}

// --- AÇÃO: ALTERAR QUANTIDADE (+ / -) ---
if (isset($_GET['acao']) && isset($_GET['id'])) {
    $carrinho_id = intval($_GET['id']);
    $acao = $_GET['acao'];
    $stmt_qtd = $conn->prepare("SELECT quantidade FROM carrinho WHERE id = ? AND usuario_id = ?");
    $stmt_qtd->bind_param("ii", $carrinho_id, $usuario_id);
    $stmt_qtd->execute();
    $res_qtd = $stmt_qtd->get_result();
    if ($res_qtd->num_rows > 0) {
        $item = $res_qtd->fetch_assoc();
        $nova_qtd = $item['quantidade'];
        if ($acao == 'mais') { $nova_qtd++; }
        elseif ($acao == 'menos') { $nova_qtd--; }
        if ($nova_qtd <= 0) {
            $stmt_del = $conn->prepare("DELETE FROM carrinho WHERE id = ?");
            $stmt_del->bind_param("i", $carrinho_id);
            $stmt_del->execute();
        } else {
            $stmt_up = $conn->prepare("UPDATE carrinho SET quantidade = ? WHERE id = ?");
            $stmt_up->bind_param("ii", $nova_qtd, $carrinho_id);
            $stmt_up->execute();
        }
    }
    header("Location: carrinho.php");
    exit();
}

// --- LISTAR OS ITENS DO CARRINHO ---
$sql = "SELECT c.id AS carrinho_id, c.quantidade, p.nome, p.preco, p.imagem 
        FROM carrinho c 
        JOIN produtos p ON c.produto_id = p.id 
        WHERE c.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$itens = $stmt->get_result();

$total = 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Carrinho - Gothic Store</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="menu.css">
    <link rel="stylesheet" href="carrinho.css">
</head>
<body>

    <header>
        <h1>Gothic <span>Store</span></h1>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="catalogo.php">Catálogo</a></li>
                <li><a href="carrinho.php">Carrinho 🛒</a></li>
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <li class="dropdown" onclick="toggleMenu(event)">
                        <a href="#" style="color: #9c27b0;">Minha Conta ▾</a>
                        <div class="dropdown-content" id="menuConta">
                            <p class="ola">Olá, <?php echo explode(' ', $_SESSION['usuario_nome'])[0]; ?>!</p>
                            <!-- <a href="logout.php" style="border-top: 1px solid #333;">Sair</a> -->
                            <a href="logout.php" style="border-top: 1px solid #333;" onclick="window.location.href='logout.php'; return false;">Sair</a>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <div class="carrinho-container">
            <h2>Meu Carrinho</h2>

            <?php if (!empty($mensagem)): ?>
                <p style="color: #d32f2f; background: rgba(211, 47, 47, 0.1); padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #d32f2f; text-align: center;">
                    <?php echo $mensagem; ?>
                </p>
            <?php endif; ?>

            <?php if ($itens->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th>Qtd</th>
                            <th>Subtotal</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = $itens->fetch_assoc()): 
                            $subtotal = $item['preco'] * $item['quantidade'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td class="produto-info">
                                    <img src="img/<?php echo $item['imagem']; ?>" alt="<?php echo $item['nome']; ?>">
                                    <span><?php echo $item['nome']; ?></span>
                                </td>
                                <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                <td>
                                    <div class="qtd-container">
                                        <a href="carrinho.php?acao=menos&id=<?php echo $item['carrinho_id']; ?>" class="btn-qtd">-</a>
                                        <span class="qtd-texto"><?php echo $item['quantidade']; ?></span>
                                        <a href="carrinho.php?acao=mais&id=<?php echo $item['carrinho_id']; ?>" class="btn-qtd">+</a>
                                    </div>
                                </td>
                                <td>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                                <td>
                                    <a href="carrinho.php?remover=<?php echo $item['carrinho_id']; ?>" class="btn-remover">🗑️</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- ÁREA DE CHECKOUT -->
                <form method="POST" action="" class="checkout-form">
                    
                    <input type="hidden" name="total_produtos" value="<?php echo $total; ?>">

                    <div class="checkout-opcoes">
                        <!-- PAGAMENTO -->
                        <div class="opcao-grupo">
                            <h3>Forma de Pagamento</h3>
                            <label class="radio-label">
                                <input type="radio" name="pagamento" value="Pix" required>
                                <span>Pix (Aprovação imediata)</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="pagamento" value="Cartão de Crédito">
                                <span>Cartão de Crédito (até 3x sem juros)</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="pagamento" value="Boleto">
                                <span>Boleto Bancário</span>
                            </label>
                        </div>

                        <!-- FRETE COM VALORES -->
                        <div class="opcao-grupo">
                            <h3>Opção de Frete</h3>
                            <label class="radio-label">
                                <input type="radio" name="frete" value="Sedex" required onclick="atualizarTotal(15.00)">
                                <span>Sedex (3 a 5 dias úteis) - <strong>R$ 15,00</strong></span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="frete" value="PAC" onclick="atualizarTotal(8.00)">
                                <span>PAC (7 a 10 dias úteis) - <strong>R$ 8,00</strong></span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="frete" value="Retirada" onclick="atualizarTotal(0.00)">
                                <span>Retirar na Loja - <strong>Grátis</strong></span>
                            </label>
                        </div>
                    </div>

                    <!-- SUBTOTAL E TOTAL GERAL -->
                    <div class="total-box">
                        <p style="font-size: 16px; margin-bottom: 5px; color: #a0a0a0;">
                            Subtotal (Produtos): <span style="font-size: 16px; color: #f5f5f5;">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                        </p>
                        <p style="font-size: 16px; margin-bottom: 15px; color: #a0a0a0;">
                            Frete: <span id="valor-frete" style="font-size: 16px; color: #f5f5f5;">R$ 0,00</span>
                        </p>
                        <hr style="border-color: #333; margin-bottom: 15px;">
                        <p>
                            Total Geral: <span id="total-geral">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                        </p>
                    </div>

                    <div class="acoes-carrinho">
                        <a href="index.php" class="btn-continuar">Adicionar mais produtos</a>
                        <button type="submit" name="finalizar_compra" class="btn-finalizar" onclick="return confirm('Deseja finalizar a compra?');">Finalizar Compra</button>
                    </div>
                </form>

                <!-- SCRIPT PRA ATUALIZAR O TOTAL NA TELA -->
                <script>
                    // Pega o valor dos produtos direto do PHP
                    const totalProdutos = <?php echo $total; ?>;

                    function atualizarTotal(valorFrete) {
                        // Atualiza o valor do frete
                        document.getElementById('valor-frete').innerText = 'R$ ' + valorFrete.toFixed(2).replace('.', ',');
                        
                        // Calcula o total geral
                        let totalGeral = totalProdutos + valorFrete;
                        document.getElementById('total-geral').innerText = 'R$ ' + totalGeral.toFixed(2).replace('.', ',');
                    }
                </script>

            <?php else: ?>
                <p class="carrinho-vazio">Seu carrinho está vazio. 🦇</p>
                <div style="text-align: center;">
                    <a href="index.php" class="btn btn-outline">Voltar ao Catálogo</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Gothic Store - Todos os direitos reservados. 🦇</p>
    </footer>

    <script>
        function toggleMenu(event) {
            event.preventDefault(); 
            document.getElementById("menuConta").classList.toggle("show");
        }
        window.onclick = function(event) {
            if (!event.target.matches('.dropdown a') && !event.target.closest('.dropdown')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    if (dropdowns[i].classList.contains('show')) {
                        dropdowns[i].classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>