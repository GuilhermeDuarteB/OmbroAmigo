CREATE TABLE [dbo].[Pagamentos] (
    [IdPagamento] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [IdSubscricaoUsuario] INT NOT NULL,
    [IdUtilizador] INT NOT NULL,
    [Valor] DECIMAL(10, 2) NOT NULL,
    [DataPagamento] DATETIME DEFAULT GETDATE(),
    [MetodoPagamento] NVARCHAR(50) CHECK (MetodoPagamento IN ('cartao_credito', 'mbway', 'paypal')) NOT NULL,
    [Estado] NVARCHAR(50) CHECK (Estado IN ('sucesso', 'falha', 'pendente')) NOT NULL,
    [DataCriacao] DATETIME DEFAULT GETDATE(),
    [DataAtualizacao] DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (IdSubscricaoUsuario) REFERENCES SubscricaoPorUtilizador(IdSubscricaoUsuario),
    FOREIGN KEY (IdUtilizador) REFERENCES Utilizadores(Id)
);