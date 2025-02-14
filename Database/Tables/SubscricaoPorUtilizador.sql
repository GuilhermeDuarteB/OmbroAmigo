CREATE TABLE [dbo].[SubscricaoPorUtilizador] (
    [IdSubscricaoUsuario] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [IdUtilizador] INT NOT NULL,
    [IdSubscricao] INT NOT NULL,
    [Status] NVARCHAR(50) CHECK (Status IN ('Ativa', 'Expirada', 'Cancelada')) NOT NULL,
    [DataInicio] DATETIME NOT NULL DEFAULT GETDATE(),
    [DataFim] DATETIME,
    [DataCriacao] DATETIME DEFAULT GETDATE(),
    [DataAtualizacao] DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (IdUtilizador) REFERENCES Utilizadores(Id),
    FOREIGN KEY (IdSubscricao) REFERENCES Subscricoes(IdSubscricao)
);