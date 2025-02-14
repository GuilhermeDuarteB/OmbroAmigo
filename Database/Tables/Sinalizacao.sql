CREATE TABLE [dbo].[Sinalizacao] (
    Id INT PRIMARY KEY IDENTITY(1,1),
    ConsultaId INT NOT NULL,
    IdRemetente INT NOT NULL,
    IdDestinatario INT NOT NULL,
    TipoRemetente VARCHAR(20) NOT NULL,
    TipoDestinatario VARCHAR(20) NOT NULL,
    Dados NVARCHAR(MAX) NOT NULL,
    Processado BIT DEFAULT 0,
    DataCriacao DATETIME DEFAULT GETDATE(),
    CONSTRAINT FK_Sinalizacao_Consulta FOREIGN KEY (ConsultaId)
        REFERENCES Consultas(IDConsulta),
    CONSTRAINT CHK_TipoRemetente CHECK (TipoRemetente IN ('user', 'profissional')),
    CONSTRAINT CHK_TipoDestinatario CHECK (TipoDestinatario IN ('user', 'profissional'))
);