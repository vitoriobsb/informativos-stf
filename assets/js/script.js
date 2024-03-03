document
  .getElementById("searchNumberButton")
  .addEventListener("click", function () {
    // Obtém o valor digitado no campo de pesquisa por número
    var searchTerm = document.getElementById("searchNumber").value.trim();

    // Simula uma busca para o termo digitado
    var searchResult = simulateSearch(searchTerm);

    // Atualiza a div de resultados com o resultado da busca por número
    var searchResultDiv = document.getElementById("searchResultNumber");
    searchResultDiv.innerHTML = searchResult;

    // Exibe a div de resultados por número
    searchResultDiv.style.display = "block";
  });

// Função de exemplo para simular a busca
function simulateSearch(searchTerm) {
  // Verifica se o termo de pesquisa é um número válido
  if (!isNaN(searchTerm) && searchTerm >= 1 && searchTerm <= 10000) {
    // Se for um número válido, cria o HTML para o resultado da busca
    var resultHtml = "<span>Informativo: " + searchTerm + "</span>";
    resultHtml +=
      '<a href="./../../informativos/Info-' +
      searchTerm +
      '.pdf" target="_blank">';
    resultHtml += '<i class="fa-solid fa-file-pdf"></i></a>';
    return resultHtml;
  } else {
    // Se o termo de pesquisa não for um número válido, exibe uma mensagem de erro
    return "<span>Nenhum informativo encontrado para o número digitado.</span>";
  }
}

//!!!!!!!!!!!!!!!!!!!!!!!!!!!
