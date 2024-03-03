<?php require_once "./search.php" ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Informativos - STF</title>

  <link rel="icon" type="image/png" sizes="16x16" href="./assets/favicons/favicon-16x16.png">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="theme-color" content="#ffffff">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>

  <main class="main">
    <section class="section">
      <div class="logo">
        <img src="./assets/logo/logo.png" alt="">
      </div>
      <div class="content">
        <form action="" method="get">
          <div class="title">
            <h1>Informativos - STF</h1>
          </div>
          <div class="info">
            <?php
            // Pasta onde os arquivos estão localizados
            $pasta = "./informativos/";

            // Lista todos os arquivos na pasta
            $arquivos = scandir($pasta, SCANDIR_SORT_DESCENDING);

            // Remove os diretórios . e ..
            $arquivos = array_diff($arquivos, array('.', '..'));

            // Função para comparar numericamente os números extraídos dos nomes dos arquivos
            function comparar_numeros($a, $b)
            {
              $numero_a = preg_replace('/[^0-9]/', '', $a);
              $numero_b = preg_replace('/[^0-9]/', '', $b);
              return $numero_a - $numero_b;
            }

            // Ordena os arquivos numericamente
            usort($arquivos, 'comparar_numeros');

            // Inverte a ordem para listar os 5 últimos arquivos
            $arquivos = array_reverse($arquivos);

            // Inicializa a variável de contagem
            $contagem = 0;

            // Loop para listar os 5 últimos arquivos
            foreach ($arquivos as $arquivo) {
              // Verifica se é um arquivo PDF
              if (pathinfo($arquivo, PATHINFO_EXTENSION) == "pdf") {
                $contagem++;
                // Se já foram encontrados 10 arquivos, para o loop
                if ($contagem > 10) {
                  break;
                }
                // Extrai o número do informativo do nome do arquivo
                $numero_informativo = preg_replace('/[^0-9]/', '', $arquivo);
            ?>

            <div class="paragraph">
              <span>Informativo: <?= $numero_informativo; ?></span>
              <a href="<?= $pasta . "Info-" . $numero_informativo . ".pdf"; ?>" target="_blank">
                <i class="fa-solid fa-file-pdf"></i>
              </a>
            </div>

            <?php
              }
            }
            ?>
          </div>
          <div class="search">
            <label for="searchNumber">Pesquisa por número:</label>
            <input type="search" name="searchNumber" placeholder="n.º do informativo" id="searchNumber">
            <button type="button" id="searchNumberButton">
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>
          </div>
          <div class="search-result" id="searchResultNumber"></div>

          <div class="word">
            <label for="searchWord">Pesquisa por termo:</label>
            <input type="text" name="searchWord" id="searchWord" placeholder="Digite o termo de busca"
              value="<?php echo isset($_GET['searchWord']) ? $_GET['searchWord'] : '' ?>">
            <button type="submit" id="searchWordButton">
              <i class="fas fa-search"></i>
            </button>
          </div>
          <div class="word-result" id="searchResultWord">
            <?php if (!empty($searchResults)) : ?>
            <?php foreach ($searchResults as $result) : ?>
            <div class="section-result">
              <span><strong>Informativo: </strong><?php echo $result['filename']; ?></span>
              <span><strong>Trecho: </strong><?php echo $result['snippet']; ?></span>
            </div>
            <?php endforeach; ?>
            <?php else : ?>
            <?php endif; ?>
          </div>
      </div>
      </form>

    </section>
  </main>

  <footer>
    <div class="copyright">
      <span>Copyright © 2024. Direitos não reservados, desde que cite a fonte</span>
      <span>Fonte: <strong>Codador_</strong></span>
    </div>
    <div class="icons">
      <a href="https://contate.me/cbvitorio" target="_blank">
        <i class="fa-brands fa-whatsapp"></i>
      </a>
      <a href="https://github.com/vitoriobsb" target="_blank">
        <i class="fa-brands fa-github"></i>
      </a>
      <a href="https://www.linkedin.com/in/carlos-vitorio-67632954/" target="_blank">
        <i class="fa-brands fa-linkedin"></i>
      </a>
      <a href="https://www.instagram.com/codador_/" target="_blank">
        <i class="fa-brands fa-instagram"></i>
      </a>
      <a href="https://codepen.io/cbvitorio" target="_blank">
        <i class="fa-brands fa-codepen"></i>
      </a>
    </div>
  </footer>


  <script src="./assets/js/script.js"></script>

</body>

</html>