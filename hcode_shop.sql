-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 21-Ago-2017 às 19:46
-- Versão do servidor: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `hcode_shop`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_carrinhosprodutostodos_rem`(
pid_car INT,
pid_prod INT
)
BEGIN
    
    DELETE FROM tb_carrinhosprodutos
    WHERE id_car = pid_car AND id_prod = pid_prod;
    
    CALL sp_carrinhosprodutos_list(pid_car);
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_carrinhosprodutos_add`(
pid_car INT,
pid_prod INT
)
BEGIN
	
	INSERT INTO tb_carrinhosprodutos (id_car, id_prod)
    VALUES(pid_car, pid_prod);
    
    CALL sp_carrinhosprodutos_list(pid_car);
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_carrinhosprodutos_list`(
pid_car INT
)
BEGIN

	SELECT 
	a.id_prod,
	a.id_car,
	b.nome_prod_longo,
	b.nome_prod_curto,
	b.preco,
	b.peso,
	b.largura_centimetro,
	b.altura_centimetro,
	b.comprimento_centimetro,
	b.foto_principal,
	COUNT(*) AS qtd_car,
    SUM(preco) AS total
	FROM tb_carrinhosprodutos a
	INNER JOIN tb_produtos b USING(id_prod)
	WHERE id_car = pid_car
	GROUP BY
	a.id_prod,
	a.id_car,
	b.nome_prod_longo,
	b.nome_prod_curto,
	b.preco,
	b.peso,
	b.largura_centimetro,
	b.altura_centimetro,
	b.comprimento_centimetro,
	b.foto_principal;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_carrinhosprodutos_rem`(
pid_car INT,
pid_prod INT
)
BEGIN
	
	DELETE FROM tb_carrinhosprodutos
    WHERE id_car = pid_car AND id_prod = pid_prod
    LIMIT 1;
    
    CALL sp_carrinhosprodutos_list(pid_car);
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_carrinhos_get`(
psession_car VARCHAR(256)
)
BEGIN
	
    DECLARE pqtd INT;
	DECLARE pid_car INT;
    DECLARE ptotal_car DECIMAL(10,2);
    
	SELECT id_car INTO pid_car
    FROM tb_carrinhos
    WHERE session_car = psession_car;
    
    IF NOT pid_car > 0 OR pid_car IS NULL THEN
    
		INSERT INTO tb_carrinhos (session_car, data_car)
        VALUES(psession_car, NOW());
        
        SET pid_car = LAST_INSERT_ID();
    
    END IF;
    
    SELECT 
    COUNT(*), SUM(b.preco) INTO pqtd, ptotal_car
    FROM tb_carrinhosprodutos a
    INNER JOIN tb_produtos b USING(id_prod)
    WHERE id_car = pid_car;
    
	SELECT 
    a.id_car,
    a.frete_car,
    a.cep_car,
    a.data_car,
    a.session_car,
    pqtd AS qtd_prod,
    ptotal_car AS subtotal_car,
    IFNULL(ptotal_car,0)+IFNULL(frete_car, 0) AS total_car
    FROM tb_carrinhos a
    WHERE a.id_car = pid_car
    GROUP BY
    a.id_car,
    a.frete_car,
    a.cep_car,
    a.data_car,
    a.session_car;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_carrinhos`
--

CREATE TABLE IF NOT EXISTS `tb_carrinhos` (
`id_car` int(11) NOT NULL,
  `frete_car` decimal(10,2) DEFAULT NULL,
  `cep_car` char(8) DEFAULT NULL,
  `data_car` datetime DEFAULT NULL,
  `session_car` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_carrinhosprodutos`
--

CREATE TABLE IF NOT EXISTS `tb_carrinhosprodutos` (
`id` int(11) NOT NULL,
  `id_car` int(11) NOT NULL,
  `id_prod` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_produtos`
--

CREATE TABLE IF NOT EXISTS `tb_produtos` (
`id_prod` int(11) NOT NULL,
  `nome_prod_curto` varchar(150) DEFAULT NULL,
  `nome_prod_longo` varchar(500) DEFAULT NULL,
  `codigo_interno` bigint(20) DEFAULT NULL,
  `id_cat` int(11) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `peso` decimal(10,2) DEFAULT NULL,
  `largura_centimetro` int(11) DEFAULT NULL,
  `altura_centimetro` int(11) DEFAULT NULL,
  `quantidade_estoque` int(11) DEFAULT NULL,
  `preco_promorcional` decimal(10,2) DEFAULT NULL,
  `foto_principal` varchar(500) DEFAULT NULL,
  `visivel` bit(8) DEFAULT NULL,
  `comprimento_centimetro` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `tb_produtos`
--

INSERT INTO `tb_produtos` (`id_prod`, `nome_prod_curto`, `nome_prod_longo`, `codigo_interno`, `id_cat`, `preco`, `peso`, `largura_centimetro`, `altura_centimetro`, `quantidade_estoque`, `preco_promorcional`, `foto_principal`, `visivel`, `comprimento_centimetro`) VALUES
(3, 'iPhone 6s 16GB', 'iPhone 6s 16GB Prata Desbloqueado iOS 9 4G 12MP - Apple', 124678139, 3, '2000.00', '0.14', 7, 13, 50, '1499.00', 'iphone.jpg', b'00000001', NULL),
(4, 'Smart TV Samsung', 'Smart TV Nano Cristal 60" Samsung 60JS7200 SUHD 4K com Conversor Digital 4 HDMI 3 USB Wi-Fi Função Games Quad Core ', 124407806, 1, '3500.00', '30.70', 168, 92, 10, '1999.00', 'SmartTVSamsung.jpg', b'00000001', NULL),
(5, 'Cafeteira Expresso Nescafé Dolce Gusto', 'Cafeteira Expresso Nescafé Dolce Gusto Genio Arno Preta 15Bar', 120866280, 5, '700.00', '2.70', 26, 60, 100, '508.00', 'CafeteiraDolceGusto.jpg', b'00000001', NULL),
(6, 'Ar Condicionado Split Hi Wall LG 12.000 Btus Quente/Frio - 220V', 'Ar Condicionado Split Hi Wall LG 12.000 Btus Quente/Frio - 220V', 123893531, 5, '1599.99', '9.00', 29, 21, 10, '1499.99', 'arCondicionadoSplitLG.jpg', b'00000001', NULL),
(7, 'Smartphone Motorola Moto Maxx', 'Smartphone Motorola Moto Maxx Desbloqueado Android 4.4 Tela 5.2" Memória 64GB Wi-Fi Câmera 21MP Preto', 121034371, 3, '2000.00', '0.14', 7, 13, 100, '1943.33', 'motoMaxx.jpg', b'00000001', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_reviews`
--

CREATE TABLE IF NOT EXISTS `tb_reviews` (
`id_revew` int(11) NOT NULL,
  `id_prod` int(11) DEFAULT NULL,
  `review` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `tb_reviews`
--

INSERT INTO `tb_reviews` (`id_revew`, `id_prod`, `review`) VALUES
(1, 3, 4),
(2, 3, 5),
(3, 3, 4),
(4, 3, 5),
(5, 4, 4),
(6, 4, 5),
(7, 4, 5),
(8, 5, 5),
(9, 5, 4),
(10, 5, 3),
(11, 6, 2),
(12, 6, 4),
(13, 6, 5),
(14, 7, 3),
(15, 7, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_carrinhos`
--
ALTER TABLE `tb_carrinhos`
 ADD PRIMARY KEY (`id_car`);

--
-- Indexes for table `tb_carrinhosprodutos`
--
ALTER TABLE `tb_carrinhosprodutos`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_produtos`
--
ALTER TABLE `tb_produtos`
 ADD PRIMARY KEY (`id_prod`);

--
-- Indexes for table `tb_reviews`
--
ALTER TABLE `tb_reviews`
 ADD PRIMARY KEY (`id_revew`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_carrinhos`
--
ALTER TABLE `tb_carrinhos`
MODIFY `id_car` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tb_carrinhosprodutos`
--
ALTER TABLE `tb_carrinhosprodutos`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tb_produtos`
--
ALTER TABLE `tb_produtos`
MODIFY `id_prod` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `tb_reviews`
--
ALTER TABLE `tb_reviews`
MODIFY `id_revew` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
