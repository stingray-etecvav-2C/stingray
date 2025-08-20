-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/08/2025 às 00:31
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
(2, 'AMD Ryzen 9 7950X', 549.99, 'disponivel', 1, 'processadores', 'AMD'),
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
(20, 'Monitor Gamer AOC 24\" 144Hz', 899.90, 'disponivel', 10, 'perifericos', 'Outros'),
(21, 'Mousepad Gamer Grande', 49.90, 'disponivel', 30, 'perifericos', 'Outros'),
(22, 'Webcam Logitech C920', 349.90, 'disponivel', 15, 'perifericos', 'Outros'),
(23, 'Controle Xbox Wireless', 399.90, 'disponivel', 12, 'perifericos', 'Outros');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
