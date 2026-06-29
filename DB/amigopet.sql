/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.13-MariaDB, for Linux (x86_64)
--
-- Host: 216.172.172.214    Database: eswdev14_dsw_2026
-- ------------------------------------------------------
-- Server version	5.7.44-48

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `administrador`
--

DROP TABLE IF EXISTS `administrador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `administrador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logradouro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` text COLLATE utf8mb4_unicode_ci,
  `data_nascimento` date DEFAULT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_1` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `numero` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adotante`
--

DROP TABLE IF EXISTS `adotante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `adotante` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pessimo','regular','bom','muito bom','excelente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bom',
  `data_nascimento` date NOT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `bairro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` text COLLATE utf8mb4_unicode_ci,
  `telefone_1` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefone_2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logradouro` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `fk_login_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `uq_adotante_cpf` (`cpf`),
  KEY `fk_adotante_login` (`fk_login_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `animal`
--

DROP TABLE IF EXISTS `animal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `animal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `sexo` enum('m','f','n/a') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n/a',
  `fk_especie_id` int(11) DEFAULT NULL,
  `cor` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `castrado` tinyint(1) NOT NULL DEFAULT '0',
  `data_castracao` date DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `historico_resgate` text COLLATE utf8mb4_unicode_ci,
  `alergias` text COLLATE utf8mb4_unicode_ci,
  `porte` enum('pequeno','medio','grande','gigante') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `localizacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('disponivel','adotado','em_tratamento','reservado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponivel',
  `local_logradouro` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_numero` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_bairro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_estado` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_animal_especie` (`fk_especie_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `animal_imagens`
--

DROP TABLE IF EXISTS `animal_imagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `animal_imagens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_animal_id` int(11) NOT NULL,
  `caminho_imagem` varchar(255) NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_animal_ordem` (`fk_animal_id`,`ordem`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `animal_raca`
--

DROP TABLE IF EXISTS `animal_raca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `animal_raca` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_raca_id` int(11) DEFAULT NULL,
  `fk_animal_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_animal_raca_2` (`fk_raca_id`),
  KEY `fk_animal_raca_3` (`fk_animal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabela` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `acao` enum('INSERT','UPDATE','DELETE') COLLATE utf8_unicode_ci DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `data_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  `valores_antigos` text COLLATE utf8_unicode_ci,
  `valores_novos` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `avaliacao_adotante`
--

DROP TABLE IF EXISTS `avaliacao_adotante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `avaliacao_adotante` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_solicitacao_id` int(11) NOT NULL,
  `tipo_moradia` enum('casa_com_quintal','casa_sem_quintal','apartamento','outro') NOT NULL,
  `experiencia_previa` tinyint(1) NOT NULL DEFAULT '0',
  `tem_criancas` tinyint(1) NOT NULL DEFAULT '0',
  `tem_outros_animais` tinyint(1) NOT NULL DEFAULT '0',
  `parecer` text NOT NULL,
  `resultado` enum('aprovado','reprovado','pendente_informacoes') NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `criado_por` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_aval_sol` (`fk_solicitacao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `caso_veterinario`
--

DROP TABLE IF EXISTS `caso_veterinario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `caso_veterinario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_animal_id` int(11) NOT NULL,
  `fk_ong_id` int(11) NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `prioridade` enum('baixa','media','alta','urgente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'media',
  `status` enum('pendente','em_avaliacao','concluido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `diagnostico` text COLLATE utf8mb4_unicode_ci,
  `tratamento` text COLLATE utf8mb4_unicode_ci,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `data_envio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_avaliacao` datetime DEFAULT NULL,
  `data_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fk_animal_id` (`fk_animal_id`),
  KEY `idx_fk_ong_id` (`fk_ong_id`),
  KEY `idx_status` (`status`),
  KEY `idx_prioridade` (`prioridade`),
  KEY `idx_data_envio` (`data_envio`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chamado`
--

DROP TABLE IF EXISTS `chamado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `chamado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ong_id` int(11) DEFAULT NULL,
  `fk_denuncia_id` int(11) DEFAULT NULL,
  `tipo` enum('resgate','abandono','maus_tratos','perdido','encontrado','outro') COLLATE utf8_unicode_ci NOT NULL,
  `urgencia` enum('baixa','media','alta','critica') COLLATE utf8_unicode_ci DEFAULT 'media',
  `assunto` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `localizacao` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('pendente','em_andamento','concluido','cancelado') COLLATE utf8_unicode_ci DEFAULT 'pendente',
  `origem` enum('manual','denuncia') COLLATE utf8_unicode_ci DEFAULT 'manual',
  `contato_nome` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contato_telefone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_chamado_ong` (`fk_ong_id`),
  KEY `idx_chamado_denuncia` (`fk_denuncia_id`),
  KEY `idx_chamado_status` (`status`),
  KEY `idx_chamado_urgencia` (`urgencia`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chamado_observacao`
--

DROP TABLE IF EXISTS `chamado_observacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `chamado_observacao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_chamado_id` int(11) NOT NULL,
  `fk_usuario_id` int(11) NOT NULL,
  `observacao` text COLLATE utf8_unicode_ci NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_observacao_chamado` (`fk_chamado_id`),
  KEY `idx_observacao_usuario` (`fk_usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clinica`
--

DROP TABLE IF EXISTS `clinica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clinica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_1` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logradouro` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `bairro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` text COLLATE utf8mb4_unicode_ci,
  `telefone_2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `despesa`
--

DROP TABLE IF EXISTS `despesa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `despesa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ong_id` int(11) NOT NULL,
  `categoria` enum('veterinario','medicamentos','alimentacao','higiene','transporte','instalacoes','outros') COLLATE utf8_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `descricao` text COLLATE utf8_unicode_ci NOT NULL,
  `fk_animal_id` int(11) DEFAULT NULL,
  `data` date NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_despesa_ong` (`fk_ong_id`),
  KEY `idx_despesa_animal` (`fk_animal_id`),
  KEY `idx_despesa_data` (`data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `doacao`
--

DROP TABLE IF EXISTS `doacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `doacao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ong_id` int(11) NOT NULL,
  `tipo` enum('dinheiro','alimentos','medicamentos','materiais','outros') COLLATE utf8_unicode_ci NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `descricao` text COLLATE utf8_unicode_ci NOT NULL,
  `doador` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` date NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_doacao_ong` (`fk_ong_id`),
  KEY `idx_doacao_data` (`data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equipe`
--

DROP TABLE IF EXISTS `equipe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ong_id` int(11) NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8_unicode_ci,
  `fk_responsavel_id` int(11) DEFAULT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_equipe_ong` (`fk_ong_id`),
  KEY `idx_equipe_responsavel` (`fk_responsavel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `especie`
--

DROP TABLE IF EXISTS `especie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `especie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `historico_animal`
--

DROP TABLE IF EXISTS `historico_animal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `historico_animal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `data` datetime DEFAULT NULL,
  `tipo` enum('vacinacao','procedimento','ocorrencia','registro_inicial','atualizacao_pos_tratamento','adocao','resgate','outros') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_animal_id` int(11) DEFAULT NULL,
  `fk_ong_id` int(11) DEFAULT NULL,
  `fk_veterinario_id` int(11) DEFAULT NULL,
  `autor_nome` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_historico_animal_2` (`fk_animal_id`),
  KEY `fk_historico_animal_3` (`fk_ong_id`),
  KEY `fk_historico_animal_4` (`fk_veterinario_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_cadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_usuario` enum('administrador','ong','adotante','veterinario','moderador') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'adotante',
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('a','i') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'a',
  `data_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_login_email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notificacao`
--

DROP TABLE IF EXISTS `notificacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificacao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_login_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `mensagem` text NOT NULL,
  `data` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `tipo` enum('solicitacao','sistema','adocao_concluida','status_adocao','avistamento_atualizado','alerta_vacina','promocao_papel') NOT NULL DEFAULT 'sistema',
  `destino_tab` varchar(50) DEFAULT NULL,
  `destino_tela` varchar(100) DEFAULT NULL,
  `destino_params` text COMMENT 'JSON params de navegação',
  PRIMARY KEY (`id`),
  KEY `idx_notif_login` (`fk_login_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ong`
--

DROP TABLE IF EXISTS `ong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ong` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('a','i') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantidade_animais` int(11) DEFAULT NULL,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_1` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` text COLLATE utf8mb4_unicode_ci,
  `logradouro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ong_animal`
--

DROP TABLE IF EXISTS `ong_animal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ong_animal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ong_id` int(11) DEFAULT NULL,
  `fk_animal_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ong_animal_2` (`fk_ong_id`),
  KEY `fk_ong_animal_3` (`fk_animal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `procedimento`
--

DROP TABLE IF EXISTS `procedimento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `procedimento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('consulta','cirurgia','exame','castracao','outro') NOT NULL,
  `data` date NOT NULL,
  `veterinario_nome` varchar(100) DEFAULT NULL,
  `observacoes` text,
  `anexo_url` varchar(255) DEFAULT NULL,
  `fk_animal_id` int(11) NOT NULL,
  `fk_login_id` int(11) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `criado_por` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_proc_animal` (`fk_animal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='append-only por política (RNF#08)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publicacao_encontrado`
--

DROP TABLE IF EXISTS `publicacao_encontrado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `publicacao_encontrado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_animal_id` int(11) NOT NULL,
  `fk_login_id` int(11) NOT NULL,
  `data_encontro` date NOT NULL,
  `localizacao` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `condicao_fisica` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `acoes_realizadas` text COLLATE utf8_unicode_ci,
  `status` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'aguardando acolhimento',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(11,7) DEFAULT NULL,
  `especie` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fotos` text COLLATE utf8_unicode_ci COMMENT 'JSON array de URLs',
  `foto` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pontos` int(11) DEFAULT NULL,
  `local_cep` varchar(9) COLLATE utf8_unicode_ci DEFAULT NULL,
  `local_logradouro` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `local_numero` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `local_bairro` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `local_cidade` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `local_estado` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `raca`
--

DROP TABLE IF EXISTS `raca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `raca` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_especie_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_raca_2` (`fk_especie_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rastreador`
--

DROP TABLE IF EXISTS `rastreador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rastreador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logradouro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_1` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `numero` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resgate`
--

DROP TABLE IF EXISTS `resgate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resgate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_chamado_id` int(11) DEFAULT NULL,
  `estado_animal` enum('saudavel','ferido','precisa_atencao','critico') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'saudavel',
  `destino` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_resgate` enum('pendente','em_andamento','concluido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `data_resgate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fk_chamado_id` (`fk_chamado_id`),
  KEY `idx_status_resgate` (`status_resgate`),
  KEY `idx_data_resgate` (`data_resgate`),
  CONSTRAINT `fk_resgate_chamado` FOREIGN KEY (`fk_chamado_id`) REFERENCES `chamado` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_rota` varchar(100) NOT NULL COMMENT 'O Nome da Rota DEVE ser Unico no Sistema!!! Não pode conter espaços no nome!!',
  `slug` varchar(255) NOT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_dynamic` tinyint(1) DEFAULT '0',
  `pattern` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`(191))
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `saude_animal`
--

DROP TABLE IF EXISTS `saude_animal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `saude_animal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_animal_id` int(11) NOT NULL,
  `apto_para_adocao` tinyint(1) NOT NULL DEFAULT '1',
  `temperamento` text,
  `necessidades_especiais` text,
  `condicao_geral` text,
  `atualizado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_saude_animal` (`fk_animal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `solicitacao_adocao`
--

DROP TABLE IF EXISTS `solicitacao_adocao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitacao_adocao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` datetime DEFAULT NULL,
  `status` enum('Pendente','Em Análise','Aprovado','Concluído','Recusado') CHARACTER SET utf8mb4 DEFAULT 'Pendente',
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `motivo_recusa` text COLLATE utf8mb4_unicode_ci,
  `fk_adotante_id` int(11) DEFAULT NULL,
  `fk_animal_id` int(11) DEFAULT NULL,
  `termo_assinado` tinyint(1) DEFAULT NULL,
  `pdf_termo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aceite_termo` tinyint(1) DEFAULT NULL,
  `timestamp_aceite` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_solicitacao_adocao_2` (`fk_adotante_id`),
  KEY `fk_solicitacao_adocao_3` (`fk_animal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `termo_adocao`
--

DROP TABLE IF EXISTS `termo_adocao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `termo_adocao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_solicitacao_id` int(11) NOT NULL,
  `pdf_url` varchar(255) DEFAULT NULL,
  `pdf_assinado_url` varchar(255) DEFAULT NULL,
  `conteudo_texto` text,
  `assinado` tinyint(1) NOT NULL DEFAULT '0',
  `data_assinatura` datetime DEFAULT NULL,
  `ip_assinatura` varchar(45) DEFAULT NULL COMMENT 'capturado server-side',
  `user_agent` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_termo_sol` (`fk_solicitacao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transferencia`
--

DROP TABLE IF EXISTS `transferencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transferencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_animal_id` int(11) NOT NULL,
  `de_usuario_id` int(11) NOT NULL,
  `de_usuario_nome` varchar(150) NOT NULL,
  `para_usuario_id` int(11) NOT NULL,
  `para_usuario_nome` varchar(150) NOT NULL,
  `motivo` text,
  `data` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_transf_animal` (`fk_animal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='append-only por política (RNF#08)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vacina`
--

DROP TABLE IF EXISTS `vacina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vacina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `data_aplicacao` date NOT NULL,
  `data_reforco` date DEFAULT NULL,
  `veterinario_nome` varchar(100) DEFAULT NULL,
  `clinica_nome` varchar(100) DEFAULT NULL,
  `fk_animal_id` int(11) NOT NULL,
  `fk_login_id` int(11) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `criado_por` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vacina_animal` (`fk_animal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='append-only por política (RNF#08)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vet_clinica`
--

DROP TABLE IF EXISTS `vet_clinica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vet_clinica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_clinica_id` int(11) DEFAULT NULL,
  `fk_veterinario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vet_clinica_2` (`fk_clinica_id`),
  KEY `fk_vet_clinica_3` (`fk_veterinario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `veterinario`
--

DROP TABLE IF EXISTS `veterinario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `veterinario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_login_id` int(11) DEFAULT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crmv` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logradouro` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `bairro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `estado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vet_login` (`fk_login_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visita_adocao`
--

DROP TABLE IF EXISTS `visita_adocao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `visita_adocao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_solicitacao_adocao_id` int(11) NOT NULL,
  `fk_usuario_id` int(11) NOT NULL,
  `data_visita` date NOT NULL,
  `estado_animal` text COLLATE utf8_unicode_ci,
  `endereco_visita` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `observacoes` text COLLATE utf8_unicode_ci,
  `nota` text COLLATE utf8_unicode_ci,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_visita_solicitacao` (`fk_solicitacao_adocao_id`),
  KEY `idx_visita_usuario` (`fk_usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `voluntario`
--

DROP TABLE IF EXISTS `voluntario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `voluntario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ong_id` int(11) NOT NULL,
  `fk_equipe_id` int(11) DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `disponibilidade` enum('manha','tarde','noite','fim_semana','flexivel') COLLATE utf8_unicode_ci DEFAULT NULL,
  `habilidades` text COLLATE utf8_unicode_ci,
  `status` enum('ativo','pendente','inativo') COLLATE utf8_unicode_ci DEFAULT 'pendente',
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_voluntario_ong` (`fk_ong_id`),
  KEY `idx_voluntario_equipe` (`fk_equipe_id`),
  KEY `idx_voluntario_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-29 14:58:53
