<?php

// Verifica se a consulta de pesquisa foi enviada
if (isset($_GET['query'])) {
    $query = $_GET['query'];

    // Lista de sites onde faremos a busca
    $sites = [
        'supertela.vg',
        'mflix.plus',
        'megaflix.sh',
        'obaflix.click',
        'hyper.hypermyapp.site',
        'obaflix.to',
        'topflix.sh',
        'furiaflix.cc',
        'pobreflix.net.br',
        'visioncine.love'
    ];

    $results = [];

    // Faz uma solicitação ao Google para cada site
    foreach ($sites as $site) {
        $url = "https://www.google.com/search?q=" . urlencode("$query site:$site");
        $html = file_get_contents($url);
        if ($html !== false) {
            // Analisa o HTML da resposta do Google
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $links = $dom->getElementsByTagName('a');
            $siteLinks = [];
            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                if (strpos($href, "/url?q=") === 0) {
                    $href = urldecode(substr($href, 7, strpos($href, "&") - 7));
                    if (strpos($href, "http") === 0) {
                        $siteLinks[] = $href;
                    }
                }
            }
            $results[] = ['site' => $site, 'links' => $siteLinks];
        } else {
            // Se houver um erro ao acessar o Google, adiciona uma entrada vazia para o site
            $results[] = ['site' => $site, 'links' => []];
        }
    }

    // Retorna os resultados como JSON
    header('Content-Type: application/json');
    echo json_encode($results);
} else {
    // Se a consulta de pesquisa não foi enviada, retorna um erro
    http_response_code(400);
    echo "Consulta de pesquisa não fornecida.";
}

?>
