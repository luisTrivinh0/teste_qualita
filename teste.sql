-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 192.168.19.5
-- Tempo de geração: 28-Jan-2022 às 21:35
-- Versão do servidor: 10.4.22-MariaDB
-- versão do PHP: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `teste`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `correspondencias`
--

CREATE TABLE `correspondencias` (
  `id_correspondencia` int(11) UNSIGNED NOT NULL,
  `empresa` varchar(35) NOT NULL,
  `destinatario` varchar(35) NOT NULL,
  `cep` varchar(8) NOT NULL,
  `logradouro` varchar(40) NOT NULL,
  `numero` varchar(6) NOT NULL,
  `complemento` varchar(20) DEFAULT NULL,
  `bairro` varchar(35) NOT NULL,
  `municipio` varchar(50) NOT NULL,
  `uf` varchar(2) NOT NULL,
  `responsavel_envio` varchar(35) NOT NULL,
  `tipo` int(11) NOT NULL,
  `ar` text NOT NULL,
  `data_envio` date NOT NULL,
  `codigo_rastreio` varchar(25) NOT NULL,
  `email_usuario` varchar(40) NOT NULL,
  `data_criacao` date NOT NULL,
  `data_alteracao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `correspondencias`
--

INSERT INTO `correspondencias` (`id_correspondencia`, `empresa`, `destinatario`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `municipio`, `uf`, `responsavel_envio`, `tipo`, `ar`, `data_envio`, `codigo_rastreio`, `email_usuario`, `data_criacao`, `data_alteracao`) VALUES
(3, 'TIKS - TI', 'Luís', '18555200', 'Avenida João Francisco de Oliveira', '191', 'TESTE', 'Parque das Arvores', 'BOITUVA', 'PB', 'SISTEMA', 1, 'ABC123DEF456', '2022-01-19', '18555200ABC123DEF456', 'luis.trivinho@icloud.com', '2022-01-28', '2022-01-28'),
(5, 'Ford', 'Creepy', '19856003', 'Avenida Bonnie Clyde', '300', '', 'Bahia', 'Jurerê', 'CE', 'SISTEMA', 3, 'Bom dia.', '2022-01-28', 'KAPPAKAPPAKAPPA', 'luis@icloud.coM', '2022-01-28', '2022-01-28'),
(7, 'Netwish', 'Luís', '08007271', 'Av. Oswaldo Rossi', '150', 'Prédio', 'Jordanópolis', 'São Bernardo do Campo', 'SP', 'SISTEMA', 4, 'O Senhor é o meu pastor, e nada me faltará.', '2022-01-12', '54172369112', 'retrivinho@bol.com', '2022-01-28', '2022-01-28'),
(8, 'Beatriz Company', 'Luís', '10123487', 'Rua Cesario Motta', '150', 'Casa 1 ', 'Centro', 'Cerquilho', 'SP', 'SISTEMA', 2, 'Olá, meu bem.', '2022-01-01', 'ABCEASYAS123', 'beatriz@gmai.com', '2022-01-28', '2022-01-28');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `correspondencias`
--
ALTER TABLE `correspondencias`
  ADD PRIMARY KEY (`id_correspondencia`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `correspondencias`
--
ALTER TABLE `correspondencias`
  MODIFY `id_correspondencia` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
