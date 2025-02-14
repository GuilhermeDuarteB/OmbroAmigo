CREATE TABLE [dbo].[Sentimentos]
(
    [IDSentimentos] INT IDENTITY(1,1) NOT NULL PRIMARY KEY, 
    [IDUtilizador] INT NOT NULL,
    [Descricao] NVARCHAR(255) NOT NULL, 
    [data] DATE NOT NULL, 
    CONSTRAINT [FK_Sentimentos_ToUtilizadores] FOREIGN KEY ([IDUtilizador]) REFERENCES [Utilizadores]([Id]),

)

