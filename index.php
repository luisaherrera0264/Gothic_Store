<?php
require_once 'conexao.php';
session_start();

$sql = "SELECT * FROM produtos LIMIT 5";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gothic Store 🦇</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="menu.css">
</head>
<body>

    <header>
        <h1>Gothic <span>Store</span></h1>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <!-- LINK DO MENU CORRIGIDO AQUI -->
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
                <?php else: ?>
                    <li><a href="login.php">Entrar</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h2>Bem-vindo ao Lado Sombrio da Moda</h2>
            <p>Descubra peças únicas para quem não tem medo da escuridão.</p>
        </section>

        <section class="vitrine">
            <h2>Destaques</h2>
            
            <div class="catalogo">
                <?php
                if ($resultado->num_rows > 0) {
                    while($produto = $resultado->fetch_assoc()) {
                        $preco_formatado = number_format($produto['preco'], 2, ',', '.');
                        
                        // O botão do produto manda pro carrinho
                        $link_carrinho = isset($_SESSION['usuario_id']) ? "carrinho.php?add=" . $produto['id'] : "login.php";
                        ?>
                        
                        <div class="produto-card">
                            <img src="img/<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>">
                            <h3><?php echo $produto['nome']; ?></h3>
                            <p class="preco">R$ <?php echo $preco_formatado; ?></p>
                            <a href="<?php echo $link_carrinho; ?>" class="btn">Adicionar ao Carrinho</a>
                        </div>

                        <?php
                    }
                } else {
                    echo "<p style='color: #666; font-style: italic;'>Nenhum produto cadastrado. 🦇</p>";
                }
                ?>
            </div>

            <!-- BOTÃO "VER CATÁLOGO COMPLETO" CORRIGIDO AQUI -->
            <div class="btn-container">
                <a href="catalogo.php" class="btn btn-outline">Ver Catálogo Completo</a>
            </div>
        </section>
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