<?php
require_once 'conexao.php';
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$mensagem = "";
$tipo_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (!empty($nome) && !empty($email) && !empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Verifica se o e-mail já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensagem = "Este e-mail já está cadastrado! 🦇";
            $tipo_msg = "erro";
        } else {
            // Insere no banco
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $email, $senha_hash);
            
            if ($stmt->execute()) {
                $mensagem = "Conta criada com sucesso! Faça login.";
                $tipo_msg = "sucesso";
            } else {
                $mensagem = "Erro ao criar conta: " . $conn->error;
                $tipo_msg = "erro";
            }
        }
        $stmt->close();
    } else {
        $mensagem = "Preencha todos os campos!";
        $tipo_msg = "erro";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Gothic Store</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="cadlogin.css">
</head>
<body>

    <header>
        <h1>Gothic <span>Store</span></h1>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="#">Catálogo</a></li>
                <li><a href="#">Carrinho 🛒</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Criar Conta</h2>
            
            <?php if (!empty($mensagem)): ?>
                <p class="msg <?php echo $tipo_msg; ?>"><?php echo $mensagem; ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Cadastrar</button>
            </form>
            
            <a href="login.php" class="link-login">Já tem uma conta? Faça login 🖤</a>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Gothic Store - Todos os direitos reservados. 🦇</p>
    </footer>
</body>
</html>