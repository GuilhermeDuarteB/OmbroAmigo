CREATE TABLE [dbo].[Utilizadores] (
    [Id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [Nome] NCHAR(50) NOT NULL,
    [NomeUtilizador] NCHAR(30) NOT NULL,
    [DataNascimento] DATE NOT NULL,
    [Email] NVARCHAR(255) NOT NULL,
    [Telefone] VARCHAR(15) NOT NULL,
    [Morada] NVARCHAR(255) NULL,
    [PalavraPasse] NVARCHAR(255) NOT NULL,
    [Tipo] NVARCHAR (255) NULL,
    [Foto] varbinary (MAX) NULL,
    [Descricao] nvarchar (150) NULL, 
    [IdSubscricao] INT NULL, 
    CONSTRAINT [FK_Utilizadores_ToTable] FOREIGN KEY ([IdSubscricao]) REFERENCES [Subscricoes]([IdSubscricao])
);