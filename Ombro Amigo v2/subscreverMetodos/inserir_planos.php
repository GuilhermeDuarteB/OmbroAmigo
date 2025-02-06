<?php
include '../connection.php';

try {
    // Primeiro, vamos verificar se já existem planos
    $checkQuery = "SELECT COUNT(*) as count FROM Subscricoes";
    $stmt = $conn->query($checkQuery);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        echo "Os planos já existem na base de dados!<br>";
        echo "<a href='index.html'>Voltar para a página de subscrição</a>";
        exit();
    }

    // Habilitar inserção de identidade
    $conn->exec("SET IDENTITY_INSERT Subscricoes ON");

    // Array com os planos
    $planos = [
        [
            'id' => 1,
            'nome' => 'Plano Individual',
            'preco' => 54.99,
            'ciclo' => 'individual',
            'vantagens' => 'Apenas Uma Consulta, Psicologia, Psiquiatria, Nutricionismo, Medicina Geral'
        ],
        [
            'id' => 2,
            'nome' => 'Plano Mensal',
            'preco' => 224.99,
            'ciclo' => 'mensal',
            'vantagens' => 'Número Ilímitado de Consultas Durante o Mês Ativo, Psicologia, 1 Serviço à Escolha'
        ],
        [
            'id' => 3,
            'nome' => 'Plano Anual',
            'preco' => 700.00,
            'ciclo' => 'anual',
            'vantagens' => 'Número Ilímitado de Consultas Durante o Ano Ativo, Psicologia, 3 Serviços à Escolha'
        ],
        [
            'id' => 4,
            'nome' => 'Plano Herois Sem Capa',
            'preco' => 15.00,
            'ciclo' => 'mensal',
            'vantagens' => 'Consultas ilímitadas de Psicologia e Psiquiatria'
        ]
    ];

    // Preparar e executar a inserção
    $query = "INSERT INTO Subscricoes (IdSubscricao, NomeSubscricao, Preco, CicloSubscricao, Vantagens) 
              VALUES (:id, :nome, :preco, :ciclo, :vantagens)";
    
    $stmt = $conn->prepare($query);

    foreach ($planos as $plano) {
        $stmt->execute([
            ':id' => $plano['id'],
            ':nome' => $plano['nome'],
            ':preco' => $plano['preco'],
            ':ciclo' => $plano['ciclo'],
            ':vantagens' => $plano['vantagens']
        ]);
    }

    // Desabilitar inserção de identidade
    $conn->exec("SET IDENTITY_INSERT Subscricoes OFF");

    echo "Planos inseridos com sucesso!<br>";
    echo "Planos cadastrados:<br><br>";

    // Mostrar os planos inseridos
    $planos = $conn->query("SELECT * FROM Subscricoes")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($planos as $plano) {
        echo "ID: {$plano['IdSubscricao']}<br>";
        echo "Nome: {$plano['NomeSubscricao']}<br>";
        echo "Preço: €{$plano['Preco']}<br>";
        echo "Ciclo: {$plano['CicloSubscricao']}<br>";
        echo "Vantagens: {$plano['Vantagens']}<br><br>";
    }

    echo "<a href='index.html'>Voltar para a página de subscrição</a>";

} catch (PDOException $e) {
    echo "Erro ao inserir planos: " . $e->getMessage() . "<br>";
    echo "<a href='index.html'>Voltar para a página de subscrição</a>";
}
?> 