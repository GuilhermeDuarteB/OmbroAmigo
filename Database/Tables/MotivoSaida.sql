CREATE TABLE [dbo].[MotivoSaida]
(
	[IdMotivo] INT IDENTITY(1,1) PRIMARY KEY,
    [Motivo] NCHAR(250) NULL, 
    [IdUtilizador] INT NOT NULL, 
    CONSTRAINT [FK_Motivo_ToUtilizadores] FOREIGN KEY ([IdUtilizador]) REFERENCES [Utilizadores]([Id]),
);
