CREATE TABLE [dbo].[Consultas] (
    [IDConsulta] INT IDENTITY(1,1) NOT NULL PRIMARY KEY, 
    [IdUtilizador] INT NOT NULL, -- ID do usuário que agendou a consulta
    [IdProfissional] INT NOT NULL, -- ID do profissional que realizará a consulta
    [DataConsulta] DATE NOT NULL, -- Data da consulta
    [HoraConsulta] TIME NOT NULL, -- Hora da consulta
    [Status] VARCHAR(20) DEFAULT 'Agendada',
    [LinkConsulta] NCHAR(255) NULL, 
    [DataAtualizacao] DATETIME DEFAULT getdate(),
    CONSTRAINT [FK_Consultas_ToProfissionais] FOREIGN KEY ([IdProfissional]) REFERENCES [UtilizadoresProfissionais]([Id]), 
    CONSTRAINT [FK_Consultas_ToUtilizadores] FOREIGN KEY ([IdUtilizador]) REFERENCES [Utilizadores]([Id])
);
