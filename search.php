<?php
function listFilesRecursively($directory)
{
  $files = array();

  // Verifica se o diretório existe
  if (is_dir($directory)) {
    // Abre o diretório
    if ($handle = opendir($directory)) {
      // Percorre os arquivos e subdiretórios
      while (false !== ($entry = readdir($handle))) {
        // Ignora as entradas . e ..
        if ($entry != "." && $entry != "..") {
          $path = $directory . DIRECTORY_SEPARATOR . $entry;
          // Verifica se é um diretório
          if (is_dir($path)) {
            // Se for um diretório, chama a função recursivamente
            $files = array_merge($files, listFilesRecursively($path));
          } else {
            // Se for um arquivo, verifica se é um arquivo PDF
            if (pathinfo($path, PATHINFO_EXTENSION) == 'pdf') {
              // Adiciona o caminho do arquivo à lista
              $files[] = $path;
            }
          }
        }
      }
      // Fecha o diretório
      closedir($handle);
    }
  }

  return $files;
}

function extractPDFContent($pdfFile)
{
  // Use a ferramenta pdftotext para extrair o conteúdo do PDF
  $command = "pdftotext -q '$pdfFile' -";
  $content = shell_exec($command);
  return $content;
}

// Diretório raiz onde estão as pastas de cada ano
$rootDirectory = '/var/www/git/informativos-stf/informativos/';

// Lista todos os arquivos PDF encontrados de forma recursiva
$allPdfFiles = array();
foreach (glob($rootDirectory . '/*.pdf') as $pdfFile) {
  $allPdfFiles[] = $pdfFile;
}

// Extrai o conteúdo de todos os arquivos PDF
$informatives = array();
foreach ($allPdfFiles as $pdfFile) {
  $content = extractPDFContent($pdfFile);
  $filename = basename($pdfFile);
  $informatives[$filename] = $content;
}

function searchInformatives($searchTerm, $informatives)
{
  $results = array();

  // Percorre os informativos
  foreach ($informatives as $filename => $content) {
    // Verifica se o termo de pesquisa está presente no conteúdo do informativo
    $searchPosition = stripos($content, $searchTerm);
    if ($searchPosition !== false) {
      // Obtém o trecho do conteúdo onde o termo foi encontrado
      $startPosition = max(0, $searchPosition - 100); // 100 caracteres antes do termo
      $endPosition = min(strlen($content), $searchPosition + 100); // 100 caracteres após o termo
      $snippet = substr($content, $startPosition, $endPosition - $startPosition);

      // Adiciona o informativo e o trecho ao resultado
      $results[] = array(
        'filename' => $filename,
        'snippet' => $snippet
      );
    }
  }

  return $results;
}

// Verifica se o termo de pesquisa foi submetido
if (isset($_GET['searchWord'])) {
  $searchTerm = $_GET['searchWord'];

  // Realiza a pesquisa nos informativos
  $searchResults = searchInformatives($searchTerm, $informatives);
} else {
  $searchResults = array();
}