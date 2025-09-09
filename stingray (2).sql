-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/09/2025 às 03:13
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `stingray`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinhos`
--

CREATE TABLE `carrinhos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `status` enum('aberto','finalizado') DEFAULT 'aberto',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `carrinhos`
--

INSERT INTO `carrinhos` (`id`, `usuario_id`, `status`, `created_at`) VALUES
(1, 2, 'finalizado', '2025-08-26 23:13:05'),
(2, 3, 'finalizado', '2025-08-26 23:22:33'),
(4, 2, 'aberto', '2025-09-08 00:46:27'),
(5, 3, 'aberto', '2025-09-08 00:48:43');

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho_itens`
--

CREATE TABLE `carrinho_itens` (
  `id` int(11) NOT NULL,
  `carrinho_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `carrinho_itens`
--

INSERT INTO `carrinho_itens` (`id`, `carrinho_id`, `produto_id`, `quantidade`) VALUES
(5, 1, 22, 1),
(6, 1, 20, 1),
(7, 2, 2, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('aberto','finalizado') NOT NULL DEFAULT 'aberto',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `total`, `status`, `created_at`) VALUES
(1, 2, 1249.80, 'finalizado', '2025-09-08 00:46:25'),
(2, 3, 549.99, 'finalizado', '2025-09-08 00:48:42');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_itens`
--

INSERT INTO `pedido_itens` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco`) VALUES
(1, 1, 20, 1, 899.90),
(2, 1, 22, 1, 349.90),
(3, 2, 2, 1, 549.99);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id_produto` int(11) NOT NULL,
  `nome_produto` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `estado` enum('disponivel','vendido') NOT NULL DEFAULT 'disponivel',
  `quantidade_estoque` int(11) NOT NULL DEFAULT 0,
  `categoria` enum('gabinetes','processadores','perifericos','acessorios') NOT NULL DEFAULT 'acessorios',
  `marca` varchar(50) NOT NULL DEFAULT 'Outros'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id_produto`, `nome_produto`, `preco`, `estado`, `quantidade_estoque`, `categoria`, `marca`) VALUES
(1, 'Intel Core i9-13900K', 589.99, 'disponivel', 15, 'processadores', 'Intel'),
(2, 'AMD Ryzen 9 7950X', 549.99, 'disponivel', 0, 'processadores', 'AMD'),
(3, 'NVIDIA GeForce RTX 4090', 1599.99, 'disponivel', 8, 'acessorios', 'Outros'),
(4, 'AMD Radeon RX 7900 XTX', 999.99, 'disponivel', 12, 'acessorios', 'AMD'),
(5, 'Corsair Vengeance RGB 32GB DDR5', 129.99, 'disponivel', 25, 'acessorios', 'Corsair'),
(6, 'Kingston Fury Beast 16GB DDR4', 59.99, 'disponivel', 1, 'acessorios', 'Outros'),
(7, 'Samsung 980 Pro SSD 1TB NVMe', 89.99, 'disponivel', 30, 'acessorios', 'Outros'),
(8, 'WD Black SN850X 2TB SSD', 149.99, 'disponivel', 18, 'acessorios', 'Outros'),
(9, 'ASUS ROG Strix Z790-E', 449.99, 'disponivel', 10, 'acessorios', 'ASUS'),
(10, 'Gigabyte B650 AORUS Elite', 219.99, 'disponivel', 1, 'acessorios', 'Gigabyte'),
(11, 'Corsair RM850x 80 Plus Gold', 134.99, 'disponivel', 20, 'acessorios', 'Corsair'),
(12, 'Gabinete Gamer Rise Mode P07', 249.99, 'disponivel', 15, 'gabinetes', 'Outros'),
(13, 'Gabinete NZXT H510', 299.90, 'disponivel', 10, 'gabinetes', 'Outros'),
(14, 'Gabinete Cooler Master MasterBox', 319.90, 'disponivel', 8, 'gabinetes', 'Outros'),
(15, 'Gabinete Corsair 4000D', 449.90, 'disponivel', 12, 'gabinetes', 'Corsair'),
(16, 'Gabinete Redragon Side Window', 199.90, 'disponivel', 20, 'gabinetes', 'Outros'),
(17, 'Teclado Mecânico Redragon Kumara', 219.90, 'disponivel', 25, 'perifericos', 'Outros'),
(18, 'Mouse Gamer Logitech G502', 249.90, 'disponivel', 18, 'perifericos', 'Outros'),
(19, 'Headset HyperX Cloud Stinger', 199.90, 'disponivel', 22, 'perifericos', 'Outros'),
(20, 'Monitor Gamer AOC 24\" 144Hz', 899.90, 'disponivel', 9, 'perifericos', 'Outros'),
(21, 'Mousepad Gamer Grande', 49.90, 'disponivel', 30, 'perifericos', 'Outros'),
(22, 'Webcam Logitech C920', 349.90, 'disponivel', 14, 'perifericos', 'Outros'),
(23, 'Controle Xbox Wireless', 399.90, 'disponivel', 12, 'perifericos', 'Outros');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `token_recuperacao` varchar(255) DEFAULT NULL,
  `expiracao_token` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `created_at`, `updated_at`, `token_recuperacao`, `expiracao_token`) VALUES
(2, 'Guilherme Rodrigues', 'Email1@gmail.com', '$2y$10$oX2gudQzaQxF5c77SG.jMeg7CNDychfrQKP65OUkXTM8HLWSLZSCC', '2025-08-26 22:59:00', '2025-08-26 22:59:00', NULL, NULL),
(3, 'Bora Bill', 'bill@gmail.com', '$2y$10$dGVFHwRdcyQ0I48Mn6FUbemxXvhvLeMqKIUW8iQOkXKyoaaej22tC', '2025-08-26 23:00:32', '2025-09-08 01:16:14', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuariosgestao`
--

CREATE TABLE `usuariosgestao` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cargo` enum('admin','repositor','funcionario') NOT NULL DEFAULT 'funcionario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuariosgestao`
--

INSERT INTO `usuariosgestao` (`id`, `nome`, `email`, `senha`, `cargo`) VALUES
(1, 'Admin', 'admin@stingray.com', '$2y$10$gyXAeYxcGXDVJxhEzl5bZO..rwjikMqfll5I/6soMU1ZadvK8Fbhm', 'admin'),
(2, 'Aladar', 'aladar@stingray.com', '$2y$10$gyXAeYxcGXDVJxhEzl5bZO..rwjikMqfll5I/6soMU1ZadvK8Fbhm', 'repositor'),
(3, 'Bill', 'bill@stingray.com', '$2y$10$vFcplRuPWuQcY684sZQJIuA6Ywc4YJFO.CaOJY4QdJ/PCD.On61Om', 'funcionario');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `carrinhos`
--
ALTER TABLE `carrinhos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_carrinho_produto` (`carrinho_id`,`produto_id`),
  ADD KEY `fk_ci_produto` (`produto_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `usuariosgestao`
--
ALTER TABLE `usuariosgestao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carrinhos`
--
ALTER TABLE `carrinhos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `usuariosgestao`
--
ALTER TABLE `usuariosgestao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `carrinhos`
--
ALTER TABLE `carrinhos`
  ADD CONSTRAINT `carrinhos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  ADD CONSTRAINT `fk_ci_carrinho` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinhos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ci_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id_produto`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id_produto`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
