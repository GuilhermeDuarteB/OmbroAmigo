CREATE TABLE [dbo].[Diarios]
(
    [IDDiario] INT IDENTITY(1,1) PRIMARY KEY,
    [IDUtilizador] INT NOT NULL,
    [Mensagem] NVARCHAR(500) NOT NULL, 
    [Imagens] varbinary (max) null,
    [DataEntrada] DATE NOT NULL, 
    [Titulo] NVARCHAR(25) NOT NULL, 
    CONSTRAINT [FK_Diarios_ToUtilizadores] FOREIGN KEY ([IDUtilizador]) REFERENCES [Utilizadores]([Id]),
);
