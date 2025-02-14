CREATE TABLE [dbo].[Subscricoes]
(
	[IdSubscricao] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
	[NomeSubscricao] nvarchar(255) NOT NULL,
    [Preco] DECIMAL(10, 2) NOT NULL,
    [CicloSubscricao] NVARCHAR(50) NOT NULL, 
    [DataInicio] DATETIME DEFAULT GETDATE(),
    [UpdateSubscricao] DATETIME DEFAULT GETDATE()
)
