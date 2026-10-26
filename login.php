<?php
require_once 'conexao.php';
session_start();

// Se já estiver logado, manda pra home
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (!empty($email) && !empty($senha)) {
        $stmt = $conn->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            
            if (password_verify($senha, $usuario['senha'])) {
                // --- A MÁGICA ACONTECE AQUI ---
                // 1. Regenera o ID da sessão (mata o crachá antigo)
                session_regenerate_id(true); 

                // 2. Salva os dados novos
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                
                header("Location: index.php");
                exit();
            } else {
                $mensagem = "Senha incorreta! 🦇";
            }
        } else {
            $mensagem = "E-mail não encontrado.";
        }
        $stmt->close();
    } else {
        $mensagem = "Preencha todos os campos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gothic Store</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="cadlogin.css">
</head>
<body>
    <header>
        <h1>Gothic <span>Store</span></h1>
    </header>
    <main>
        <div class="form-container">
            <h2>Entrar na Conta</h2>
            <?php if (!empty($mensagem)): ?>
                <p class="msg erro"><?php echo $mensagem; ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Entrar</button>
            </form>
            <a href="cadastro.php" class="link-login">Não tem conta? Cadastre-se 🖤</a>
        </div>
    </main>
</body>
</html>