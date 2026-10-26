-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 26-Out-2026 às 19:22
-- Versão do servidor: 5.7.36
-- versão do PHP: 8.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gothic_store`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT '1',
  `data_adicao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Em processamento',
  `data_pedido` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `estoque` int(11) NOT NULL DEFAULT '0',
  `imagem` varchar(255) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `preco`, `estoque`, `imagem`, `categoria`) VALUES
(1, 'Body Nightshade', '149.90', 12, 'BodyNightshade.jpg', 'Roupas'),
(2, 'Boina Iron Noir', '89.90', 19, 'BoinaIronNoir.jpg', 'Acessórios'),
(3, 'Bota Buckle Storm', '349.90', 4, 'BotaBuckleStorm.jpg', 'Calçados'),
(4, 'Brinco Gothic Cross', '59.90', 28, 'BrincoGothicCross.jpg', 'Acessórios'),
(5, 'Choker Spike Noir', '69.90', 25, 'ChokerSpikeNoir.jpg', 'Acessórios'),
(6, 'Chunky Star Boots', '299.90', 8, 'ChunkyStarBoots.jpg', 'Calçados'),
(7, 'Cinto Belt Black', '79.90', 15, 'CintoBeltBlack.jpg', 'Acessórios'),
(8, 'Colar Black Rose', '74.90', 18, 'ColarBlackRose.jpg', 'Acessórios'),
(9, 'Colar Bat Cross', '89.90', 15, 'ColarBatCross.jpg', 'Acessórios'),
(10, 'Colar Spider Cross', '84.90', 12, 'ColarSpiderCross.jpg', 'Acessórios'),
(11, 'Corset Shadow', '179.90', 10, 'CorsetShadow.jpg', 'Roupas'),
(12, 'Dark Lace Corset', '169.90', 8, 'DarkLaceCorset.jpg', 'Roupas'),
(13, 'Midnight Cross Top', '139.90', 14, 'MidnightCrossTop.jpg', 'Roupas'),
(14, 'Pulseira Chain & Spike', '59.90', 21, 'PulseiraChainSpike.jpg', 'Acessórios'),
(15, 'Saia Chain & Lace', '159.90', 8, 'SaiaChainLace.jpg', 'Roupas'),
(16, 'Saia Grommet Noir', '149.90', 11, 'SaiaGrommetNoir.jpg', 'Roupas'),
(17, 'Sapato Midnight Mary', '259.90', 6, 'SapatoMidnightMary.jpg', 'Calçados'),
(18, 'Short Cut-Off Noir', '119.90', 13, 'ShortCutOffNoir.jpg', 'Roupas'),
(19, 'Top Spike Metal', '129.90', 10, 'TopSpikeMetal.jpg', 'Roupas'),
(20, 'Victorian Corset', '199.90', 7, 'VictorianCorset.jpg', 'Roupas');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `data_cadastro`) VALUES
(1, 'Marina Veloso', 'marinaveloso@gmail.com', '$2y$10$TVwDkfBF4HcKRSA0h9YYrejLfwGqWEp4AGXp0qLSEdjngl.HSYiXK', '2026-10-24 00:26:21'),
(2, 'Elena Santos', 'elenasantos@gmail.com', '$2y$10$QcLPgQ7F/GwFXDV7UR2nLe4x2RvsIIiDygHfEb0sRvkZWwaN.WtH6', '2026-10-24 13:10:52'),
(3, 'jorge', 'j@orge', '$2y$10$Q2qq69GYeTUg2A8DHjYQq.D1ucvzAZe.sH8TcbmO1dE7kMVL910V2', '2026-10-24 14:03:35'),
(4, 'Ana Souza', 'ana@gmail.com', '$2y$10$HPlXBIlNNoQFDEOi9AjkXeahalXqiJCx95zZmcVIyAjwyT.X8UbGO', '2026-10-24 14:31:50');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices para tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrinho_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
