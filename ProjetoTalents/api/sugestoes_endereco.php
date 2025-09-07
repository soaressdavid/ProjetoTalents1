<?php
// API para sugestões de endereços completos
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../app/utils/init.php';

$termo = $_GET['termo'] ?? '';

if (empty($termo) || strlen($termo) < 2) {
    echo json_encode([]);
    exit();
}

try {
    // Buscar sugestões de endereços baseadas no termo
    $sugestoes = buscarSugestoesEndereco($termo);
    
    echo json_encode($sugestoes);
} catch (Exception $e) {
    error_log("Erro ao buscar sugestões de endereço: " . $e->getMessage());
    echo json_encode([]);
}

function buscarSugestoesEndereco($termo) {
    // Base de dados simulada de locais (empresas, escolas, pontos de interesse, endereços)
    $locais = [
        // Empresas e Instituições
        'ENIAC' => [
            'ENIAC - Faculdade de Tecnologia, Rua dos Eniacs, 123, Vila Madalena, São Paulo - SP',
            'ENIAC Shopping, Avenida Eniac, 456, Centro, São Paulo - SP',
            'ENIAC Business Center, Praça dos Eniacs, 789, Pinheiros, São Paulo - SP'
        ],
        'SENAC' => [
            'SENAC São Paulo, Rua Dr. Plínio Barreto, 285, Bela Vista, São Paulo - SP',
            'SENAC Lapa Tito, Rua Tito, 54, Lapa, São Paulo - SP',
            'SENAC Santana, Avenida Engenheiro Caetano Álvares, 5102, Santana, São Paulo - SP'
        ],
        'FIESP' => [
            'FIESP - Federação das Indústrias, Avenida Paulista, 1313, Bela Vista, São Paulo - SP',
            'FIESP Centro Cultural, Rua Tagua, 150, Bela Vista, São Paulo - SP'
        ],
        'USP' => [
            'USP - Universidade de São Paulo, Cidade Universitária, Butantã, São Paulo - SP',
            'USP Leste, Rua Arlindo Béttio, 1000, Ermelino Matarazzo, São Paulo - SP',
            'USP Ribeirão Preto, Avenida Bandeirantes, 3900, Ribeirão Preto - SP'
        ],
        'UNICAMP' => [
            'UNICAMP - Universidade Estadual de Campinas, Cidade Universitária, Campinas - SP',
            'UNICAMP Limeira, Rua Pedro Zaccaria, 1300, Limeira - SP'
        ],
        'ITAU' => [
            'Itaú Unibanco, Avenida Paulista, 1111, Bela Vista, São Paulo - SP',
            'Itaú Cultural, Avenida Paulista, 149, Bela Vista, São Paulo - SP',
            'Itaú Shopping, Rua do Rócio, 200, Vila Olímpia, São Paulo - SP'
        ],
        'BANCO' => [
            'Banco do Brasil, Avenida Paulista, 1000, Bela Vista, São Paulo - SP',
            'Bradesco, Avenida Paulista, 2000, Bela Vista, São Paulo - SP',
            'Caixa Econômica Federal, Rua da Consolação, 3000, Centro, São Paulo - SP'
        ],
        'SHOPPING' => [
            'Shopping Iguatemi, Avenida Faria Lima, 2232, Itaim Bibi, São Paulo - SP',
            'Shopping Morumbi, Avenida Roque Petroni Júnior, 1089, Morumbi, São Paulo - SP',
            'Shopping Center Norte, Travessa Casalbuono, 120, Vila Guilherme, São Paulo - SP'
        ],
        'HOSPITAL' => [
            'Hospital das Clínicas, Avenida Dr. Enéas Carvalho de Aguiar, 255, Cerqueira César, São Paulo - SP',
            'Hospital Sírio-Libanês, Rua Dona Adma Jafet, 91, Bela Vista, São Paulo - SP',
            'Hospital Albert Einstein, Avenida Albert Einstein, 627, Morumbi, São Paulo - SP'
        ],
        'ESCOLA' => [
            'Colégio Bandeirantes, Rua Estela, 268, Vila Progredior, São Paulo - SP',
            'Escola Estadual, Rua Augusta, 1000, Consolação, São Paulo - SP',
            'Colégio Objetivo, Avenida Paulista, 2000, Bela Vista, São Paulo - SP'
        ],
        'PAULISTA' => [
            'Avenida Paulista, 1000, Bela Vista, São Paulo - SP',
            'Avenida Paulista, 2000, Consolação, São Paulo - SP',
            'Rua da Paulista, 300, Centro, São Paulo - SP'
        ],
        'MCDONALDS' => [
            'McDonald\'s Paulista, Avenida Paulista, 1000, Bela Vista, São Paulo - SP',
            'McDonald\'s Shopping, Rua Augusta, 2000, Consolação, São Paulo - SP',
            'McDonald\'s Centro, Rua da Consolação, 3000, Centro, São Paulo - SP'
        ],
        'BURGER' => [
            'Burger King, Avenida Paulista, 1500, Bela Vista, São Paulo - SP',
            'Bob\'s, Rua Augusta, 2500, Consolação, São Paulo - SP',
            'Subway, Rua da Consolação, 3500, Centro, São Paulo - SP'
        ],
        'STARBUCKS' => [
            'Starbucks Paulista, Avenida Paulista, 1200, Bela Vista, São Paulo - SP',
            'Starbucks Shopping, Rua Augusta, 2200, Consolação, São Paulo - SP',
            'Starbucks Centro, Rua da Consolação, 3200, Centro, São Paulo - SP'
        ],
        'WALMART' => [
            'Walmart Supercenter, Avenida Paulista, 1800, Bela Vista, São Paulo - SP',
            'Walmart Express, Rua Augusta, 2800, Consolação, São Paulo - SP',
            'Walmart Neighborhood, Rua da Consolação, 3800, Centro, São Paulo - SP'
        ],
        'CARREFOUR' => [
            'Carrefour, Avenida Paulista, 1900, Bela Vista, São Paulo - SP',
            'Carrefour Express, Rua Augusta, 2900, Consolação, São Paulo - SP',
            'Carrefour Bairro, Rua da Consolação, 3900, Centro, São Paulo - SP'
        ],
        'UNIVERSIDADE' => [
            'PUC-SP, Rua Monte Alegre, 984, Perdizes, São Paulo - SP',
            'UNIP, Rua Taguá, 150, Bela Vista, São Paulo - SP',
            'Mackenzie, Rua da Consolação, 930, Consolação, São Paulo - SP'
        ],
        'FACULDADE' => [
            'Faculdade Anhembi Morumbi, Rua Casa do Ator, 275, Vila Olímpia, São Paulo - SP',
            'Faculdade Cásper Líbero, Avenida Paulista, 900, Bela Vista, São Paulo - SP',
            'Faculdade FGV, Rua Itapeva, 474, Bela Vista, São Paulo - SP'
        ],
        'AEROPORTO' => [
            'Aeroporto de Congonhas, Praça do Aeroporto, s/n, Campo Belo, São Paulo - SP',
            'Aeroporto de Guarulhos, Rodovia Hélio Smidt, s/n, Guarulhos - SP',
            'Aeroporto de Viracopos, Rodovia Santos Dumont, s/n, Campinas - SP'
        ],
        'ESTACAO' => [
            'Estação da Luz, Praça da Luz, 1, Bom Retiro, São Paulo - SP',
            'Estação Brás, Rua Brás, 200, Brás, São Paulo - SP',
            'Estação Tatuapé, Rua Tuiuti, 515, Tatuapé, São Paulo - SP'
        ],
        'TERMINAL' => [
            'Terminal Tietê, Avenida Cruzeiro do Sul, 1800, Santana, São Paulo - SP',
            'Terminal Barra Funda, Avenida Marquês de São Vicente, 1000, Barra Funda, São Paulo - SP',
            'Terminal Jabaquara, Rua dos Jequitibás, 100, Jabaquara, São Paulo - SP'
        ],
        'PRAÇA' => [
            'Praça da Sé, Praça da Sé, s/n, Sé, São Paulo - SP',
            'Praça da República, Praça da República, s/n, República, São Paulo - SP',
            'Praça do Patriarca, Praça do Patriarca, s/n, Centro, São Paulo - SP'
        ],
        'PARQUE' => [
            'Parque Ibirapuera, Avenida Pedro Álvares Cabral, s/n, Vila Mariana, São Paulo - SP',
            'Parque Villa-Lobos, Avenida Professor Fonseca Rodrigues, 2001, Alto de Pinheiros, São Paulo - SP',
            'Parque da Aclimação, Rua Muniz de Sousa, 1119, Aclimação, São Paulo - SP'
        ],
        'MUSEU' => [
            'MASP - Museu de Arte, Avenida Paulista, 1578, Bela Vista, São Paulo - SP',
            'Museu do Ipiranga, Parque da Independência, s/n, Ipiranga, São Paulo - SP',
            'Pinacoteca, Praça da Luz, 2, Bom Retiro, São Paulo - SP'
        ],
        'TEATRO' => [
            'Teatro Municipal, Praça Ramos de Azevedo, s/n, Centro, São Paulo - SP',
            'Teatro Sérgio Cardoso, Rua Rui Barbosa, 153, Bela Vista, São Paulo - SP',
            'Teatro Alfa, Rua Bento Branco de Andrade Filho, 722, Santo Amaro, São Paulo - SP'
        ],
        'CINEMA' => [
            'Cinemark, Avenida Paulista, 1000, Bela Vista, São Paulo - SP',
            'Cinépolis, Rua Augusta, 2000, Consolação, São Paulo - SP',
            'UCI, Rua da Consolação, 3000, Centro, São Paulo - SP'
        ],
        'FARMACIA' => [
            'Farmácia Pague Menos, Avenida Paulista, 1100, Bela Vista, São Paulo - SP',
            'Droga Raia, Rua Augusta, 2100, Consolação, São Paulo - SP',
            'Farmácia Nissei, Rua da Consolação, 3100, Centro, São Paulo - SP'
        ],
        'POSTO' => [
            'Posto Shell, Avenida Paulista, 1200, Bela Vista, São Paulo - SP',
            'Posto Ipiranga, Rua Augusta, 2200, Consolação, São Paulo - SP',
            'Posto BR, Rua da Consolação, 3200, Centro, São Paulo - SP'
        ],
        'MERCADO' => [
            'Mercado Municipal, Rua da Cantareira, 306, Centro, São Paulo - SP',
            'Mercado Central, Rua Augusta, 2000, Consolação, São Paulo - SP',
            'Mercado da Vila, Rua Harmonia, 100, Vila Madalena, São Paulo - SP'
        ],
        'IBIRAPUERA' => [
            'Parque do Ibirapuera, s/n, Vila Mariana, São Paulo - SP',
            'Avenida Pedro Álvares Cabral, 1000, Vila Mariana, São Paulo - SP',
            'Rua do Ibirapuera, 200, Vila Mariana, São Paulo - SP'
        ],
        'COPACABANA' => [
            'Avenida Atlântica, 1000, Copacabana, Rio de Janeiro - RJ',
            'Rua Barata Ribeiro, 200, Copacabana, Rio de Janeiro - RJ',
            'Rua Nossa Senhora de Copacabana, 300, Copacabana, Rio de Janeiro - RJ'
        ],
        'IPANEMA' => [
            'Avenida Vieira Souto, 100, Ipanema, Rio de Janeiro - RJ',
            'Rua Visconde de Pirajá, 200, Ipanema, Rio de Janeiro - RJ',
            'Rua Garcia d\'Ávila, 300, Ipanema, Rio de Janeiro - RJ'
        ],
        'CENTRO_RJ' => [
            'Rua da Candelária, 100, Centro, Rio de Janeiro - RJ',
            'Avenida Rio Branco, 200, Centro, Rio de Janeiro - RJ',
            'Rua da Carioca, 300, Centro, Rio de Janeiro - RJ'
        ],
        'CENTRO_SP' => [
            'Avenida Paulista, 1000, Centro, São Paulo - SP',
            'Praça da Sé, 200, Centro, São Paulo - SP',
            'Rua Augusta, 300, Centro, São Paulo - SP'
        ],
        'VILA' => [
            'Rua Augusta, 1000, Vila Madalena, São Paulo - SP',
            'Rua Harmonia, 200, Vila Madalena, São Paulo - SP',
            'Rua Wisard, 300, Vila Madalena, São Paulo - SP',
            'Rua das Flores, 400, Vila Olímpia, São Paulo - SP'
        ],
        'JARDINS' => [
            'Rua Oscar Freire, 1000, Jardins, São Paulo - SP',
            'Alameda Santos, 200, Jardins, São Paulo - SP',
            'Rua Bela Cintra, 300, Jardins, São Paulo - SP'
        ],
        'BARRA' => [
            'Avenida das Américas, 1000, Barra da Tijuca, Rio de Janeiro - RJ',
            'Rua Desembargador Alfredo Russel, 200, Barra da Tijuca, Rio de Janeiro - RJ',
            'Avenida Ayrton Senna, 300, Barra da Tijuca, Rio de Janeiro - RJ'
        ],
        'LEBLON' => [
            'Rua Dias Ferreira, 100, Leblon, Rio de Janeiro - RJ',
            'Avenida Ataulfo de Paiva, 200, Leblon, Rio de Janeiro - RJ',
            'Rua General Urquiza, 300, Leblon, Rio de Janeiro - RJ'
        ],
        'FLAMENGO' => [
            'Rua do Flamengo, 100, Flamengo, Rio de Janeiro - RJ',
            'Avenida Beira Mar, 200, Flamengo, Rio de Janeiro - RJ',
            'Rua Marquês de Abrantes, 300, Flamengo, Rio de Janeiro - RJ'
        ],
        'BOTAFOGO' => [
            'Rua Voluntários da Pátria, 100, Botafogo, Rio de Janeiro - RJ',
            'Rua General Polidoro, 200, Botafogo, Rio de Janeiro - RJ',
            'Avenida Venceslau Brás, 300, Botafogo, Rio de Janeiro - RJ'
        ],
        'SAVASSI' => [
            'Rua Pernambuco, 1000, Savassi, Belo Horizonte - MG',
            'Rua da Bahia, 200, Savassi, Belo Horizonte - MG',
            'Avenida do Contorno, 300, Savassi, Belo Horizonte - MG'
        ],
        'CENTRO' => [
            'Rua da Bahia, 100, Centro, Belo Horizonte - MG',
            'Avenida Afonso Pena, 200, Centro, Belo Horizonte - MG',
            'Praça da Liberdade, 300, Centro, Belo Horizonte - MG'
        ],
        'RECIFE' => [
            'Rua do Bom Jesus, 100, Recife Antigo, Recife - PE',
            'Avenida Boa Viagem, 200, Boa Viagem, Recife - PE',
            'Rua da Aurora, 300, Santo Amaro, Recife - PE'
        ],
        'SALVADOR' => [
            'Rua do Pelourinho, 100, Pelourinho, Salvador - BA',
            'Avenida Oceânica, 200, Barra, Salvador - BA',
            'Rua Chile, 300, Centro, Salvador - BA'
        ],
        'BRASILIA' => [
            'Esplanada dos Ministérios, 100, Zona Cívico-Administrativa, Brasília - DF',
            'Avenida W3, 200, Asa Sul, Brasília - DF',
            'SCS Quadra 2, 300, Asa Sul, Brasília - DF'
        ],
        'PORTO' => [
            'Rua da Carioca, 100, Centro, Porto Alegre - RS',
            'Avenida Ipiranga, 200, Centro, Porto Alegre - RS',
            'Rua dos Andradas, 300, Centro, Porto Alegre - RS'
        ],
        'CURITIBA' => [
            'Rua XV de Novembro, 100, Centro, Curitiba - PR',
            'Avenida Paulista, 200, Centro, Curitiba - PR',
            'Rua das Flores, 300, Centro, Curitiba - PR'
        ],
        'FORTALEZA' => [
            'Avenida Beira Mar, 100, Meireles, Fortaleza - CE',
            'Rua Dragão do Mar, 200, Centro, Fortaleza - CE',
            'Avenida Dom Luís, 300, Meireles, Fortaleza - CE'
        ]
    ];
    
    $sugestoes = [];
    $termoUpper = strtoupper($termo);
    
    // Buscar correspondências exatas primeiro
    foreach ($locais as $chave => $locaisLista) {
        if (strpos($chave, $termoUpper) !== false) {
            foreach ($locaisLista as $local) {
                $sugestoes[] = [
                    'endereco' => $local,
                    'tipo' => 'exata',
                    'relevancia' => 100,
                    'categoria' => determinarCategoria($local)
                ];
            }
        }
    }
    
    // Buscar também por "CENTRO" genérico
    if (strpos($termoUpper, 'CENTRO') !== false) {
        foreach (['CENTRO_RJ', 'CENTRO_SP'] as $chave) {
            if (isset($locais[$chave])) {
                foreach ($locais[$chave] as $local) {
                    $sugestoes[] = [
                        'endereco' => $local,
                        'tipo' => 'exata',
                        'relevancia' => 100,
                        'categoria' => determinarCategoria($local)
                    ];
                }
            }
        }
    }
    
    // Buscar correspondências parciais
    foreach ($locais as $chave => $locaisLista) {
        if (strpos($chave, $termoUpper) !== false) {
            continue; // Já foi processado acima
        }
        
        foreach ($locaisLista as $local) {
            if (stripos($local, $termo) !== false) {
                $relevancia = 50;
                
                // Aumentar relevância se o termo aparece no início
                if (stripos($local, $termo) === 0) {
                    $relevancia = 80;
                }
                
                $sugestoes[] = [
                    'endereco' => $local,
                    'tipo' => 'parcial',
                    'relevancia' => $relevancia,
                    'categoria' => determinarCategoria($local)
                ];
            }
        }
    }
    
    // Ordenar por relevância
    usort($sugestoes, function($a, $b) {
        return $b['relevancia'] - $a['relevancia'];
    });
    
    // Limitar a 10 sugestões
    return array_slice($sugestoes, 0, 10);
}

function determinarCategoria($local) {
    $localUpper = strtoupper($local);
    
    if (strpos($localUpper, 'UNIVERSIDADE') !== false || strpos($localUpper, 'USP') !== false || 
        strpos($localUpper, 'UNICAMP') !== false || strpos($localUpper, 'PUC') !== false ||
        strpos($localUpper, 'FACULDADE') !== false) {
        return 'universidade';
    }
    
    if (strpos($localUpper, 'ESCOLA') !== false || strpos($localUpper, 'COLÉGIO') !== false) {
        return 'escola';
    }
    
    if (strpos($localUpper, 'HOSPITAL') !== false) {
        return 'hospital';
    }
    
    if (strpos($localUpper, 'SHOPPING') !== false) {
        return 'shopping';
    }
    
    if (strpos($localUpper, 'MCDONALD') !== false || strpos($localUpper, 'BURGER') !== false ||
        strpos($localUpper, 'STARBUCKS') !== false || strpos($localUpper, 'SUBWAY') !== false) {
        return 'restaurante';
    }
    
    if (strpos($localUpper, 'BANCO') !== false || strpos($localUpper, 'ITAU') !== false ||
        strpos($localUpper, 'BRADESCO') !== false) {
        return 'banco';
    }
    
    if (strpos($localUpper, 'AEROPORTO') !== false) {
        return 'aeroporto';
    }
    
    if (strpos($localUpper, 'ESTAÇÃO') !== false || strpos($localUpper, 'TERMINAL') !== false) {
        return 'transporte';
    }
    
    if (strpos($localUpper, 'PARQUE') !== false) {
        return 'parque';
    }
    
    if (strpos($localUpper, 'MUSEU') !== false || strpos($localUpper, 'TEATRO') !== false ||
        strpos($localUpper, 'CINEMA') !== false) {
        return 'cultura';
    }
    
    if (strpos($localUpper, 'FARMÁCIA') !== false || strpos($localUpper, 'DROGA') !== false) {
        return 'farmacia';
    }
    
    if (strpos($localUpper, 'POSTO') !== false) {
        return 'posto';
    }
    
    if (strpos($localUpper, 'MERCADO') !== false || strpos($localUpper, 'WALMART') !== false ||
        strpos($localUpper, 'CARREFOUR') !== false) {
        return 'supermercado';
    }
    
    if (strpos($localUpper, 'PRAÇA') !== false) {
        return 'praca';
    }
    
    return 'endereco';
}
?>
