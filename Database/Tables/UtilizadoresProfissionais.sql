﻿CREATE TABLE [dbo].[UtilizadoresProfissionais] (
    [Id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [Nome] NCHAR(50) NOT NULL,
    [NomeUtilizador] NCHAR(30) NOT NULL,
    [Tipo] NVARCHAR (255) NOT NULL,
    [DataNascimento] DATE NOT NULL,
    [Email] NVARCHAR(255) NOT NULL,
    [Telefone] VARCHAR(15) NOT NULL,
    [Morada] NVARCHAR(255) NOT NULL,
    [PalavraPasse] NVARCHAR(255) NOT NULL,
    [Genero] nvarchar(50) NOT NULL,
    [EstabelecimentoEnsino] NVARCHAR (255) NOT NULL,
    [AreaEspecializada] NVARCHAR (255) NOT NULL,
    [SituacaoAtual] NVARCHAR (255) NOT NULL,
    [MotivoCandidatura] NVARCHAR (255) NOT NULL,
    [Diploma] varbinary (MAX) NOT NULL,
    [CV] VARBINARY (MAX) NOT NULL,
    [Foto] varbinary (MAX) NULL, 
);