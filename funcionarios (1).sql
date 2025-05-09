-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/05/2025 às 21:11
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
-- Banco de dados: `estoque`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `Telefone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`id`, `Nome`, `senha`, `email`, `Telefone`) VALUES
(1, 'joao', 'senha', 'joaoaugebit@gmail.com', 2147483647),
(2, 'ana', 'admin2024', 'anaaugebit@gmail.com', 1198765461),
(3, 'maria', 'estoque321', 'mariaaugebit@gmail.com', 1145678988);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
