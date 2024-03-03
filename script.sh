#!/bin/bash

#
# ------------------------------------------------------------------------ #
# Nome do Script:   script.sh
# Descrição:	      Download dos informativos do STF
# Escrito por:      Codador_ (https://github.com/vitoriobsb)
# Data de Criacao:  03/03/2024
# Versão:           1.0.0
# ------------------------------------------------------------------------ #
# Uso:         		  $ ./script.sh
# ------------------------------------------------------------------------ #
# Testado em:    		Bash 5.1.16(1)-release
# ------------------------------------------------------------------------ #
# História:
#                		- faz download de todos os informativos do STF
#                		- converte-os em formato PDF
#                   - armazena-os no diretório "informativos"
# ------------------------------------------------------------------------ #
# Agradecimentos: 	Cézar Smith
# ------------------------------------------------------------------------ #
# FUNÇÕES:
#                  - HTMLConvertPDF
#                     - Esta função é responsável por converter os
#                       informativos de 1 a 283, que estão no formato HTML,
#                       em arquivos PDF.
#                  - ZIPConvertPDF
#                     - Esta função lida com os informativos de 284 a 999,
#                       que estão compactados no formato ZIP. Após a
#                       descompactação, os arquivos são convertidos para
#                       PDF.
#                  - extrairPDF
#                     - Esta função é responsável por lidar com os
#                       informativos a partir do número 1000, que já estão
#                       no formato PDF.
# ------------------------------------------------------------------------ #
# Observações:
#                  - É necessário fornecer o caminho completo para evitar
#                    erros na conversão de arquivos ZIP para PDF.
#                  - O uso do curl em modo anônimo é necessário para
#                    evitar bloqueios de requisições por parte do servidor.
# ------------------------------------------------------------------------ #
# CÓDIGO

HTMLConvertPDF() {
  local start_number=1
  local end_number=283
  local base_url="https://www.stf.jus.br/arquivo/informativo/documento/informativo"
  local output_folder="/var/www/develop/script/stf/informativos"

  # Cria o diretório de saída, se ainda não existir
  mkdir -p "$output_folder"

  for ((number = start_number; number <= end_number; number++)); do
    local url="${base_url}${number}.htm"
    local html_output_file="${output_folder}/Info-${number}.html"
    local pdf_output_file="${output_folder}/Info-${number}.pdf"

    # Verifica se o arquivo PDF já existe
    if [ -e "$pdf_output_file" ]; then
      echo "O arquivo do Informativo STF ${number} já existe. Passando para o próximo."
      continue
    fi

    # Baixa o arquivo HTML
    curl -s -o "$html_output_file" -k -A "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36" "$url"

    # Verifica se o download foi bem-sucedido
    if [ $? -eq 0 ]; then
      echo "Download do Informativo STF ${number} concluído com sucesso."

      # Converte o arquivo HTML em PDF
      wkhtmltopdf "$html_output_file" "$pdf_output_file"

      # Verifica se a conversão foi bem-sucedida
      if [ $? -eq 0 ]; then
        echo "Erro ao converter o arquivo do Informativo STF ${number} para PDF."

      else
        echo "Arquivo do Informativo STF ${number} convertido para PDF com sucesso."

        # Remove o arquivo HTML
        rm -f "$html_output_file"
      fi

    else
      echo "Erro ao baixar o arquivo do Informativo STF ${number}."
    fi
  done

  echo "Download e conversão concluídos."
}

# Chamada da função para baixar e converter os informativos
HTMLConvertPDF

# Função para baixar e converter os informativos do STF em PDF
ZIPConvertPDF() {
  local start_number=284
  local end_number=999
  local base_url="https://www.stf.jus.br/arquivo/informativo/download/zip/informativo"
  local output_folder="/var/www/develop/script/stf/informativos"

  # Cria o diretório de saída, se ainda não existir
  mkdir -p "$output_folder"

  for ((number = start_number; number <= end_number; number++)); do
    local url="${base_url}${number}.zip"
    local output_file="Info-${number}.zip"
    local extracted_folder="Info-${number}"
    local pdf_output_file="${output_folder}/Info-${number}.pdf"

    # Verifica se o arquivo PDF já existe
    if [ -e "$pdf_output_file" ]; then
      echo "O arquivo do Informativo STF ${number} já existe. Passando para o próximo."
      continue
    fi

    # Baixa o arquivo ZIP
    curl -o "$output_file" -k -A "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36" "$url"

    # Verifica se o download foi bem-sucedido
    if [ $? -eq 0 ]; then
      echo "Download do Informativo STF ${number} concluído com sucesso."

      # Descompacta o arquivo ZIP
      unzip -q "$output_file" -d "$extracted_folder"

      # Verifica se a descompactação foi bem-sucedida
      if [ $? -eq 0 ]; then
        echo "Arquivo ZIP do Informativo STF ${number} descompactado com sucesso."

        # Encontra o caminho para o unoconv
        local unoconv_path=$(which unoconv)

        # Converte o arquivo descompactado em PDF
        "$unoconv_path" -f pdf "$extracted_folder"/*.rtf

        # Verifica se a conversão foi bem-sucedida
        if [ $? -eq 0 ]; then
          echo "Arquivo do Informativo STF ${number} convertido para PDF com sucesso."

          # Renomeia o arquivo PDF para o formato desejado
          mv "$extracted_folder"/*.pdf "$pdf_output_file"

          # Remove os arquivos temporários
          rm -rf "$output_file" "$extracted_folder"
        else
          echo "Erro ao converter o arquivo do Informativo STF ${number} para PDF."
        fi

      else
        echo "Erro ao descompactar o arquivo ZIP do Informativo STF ${number}."
      fi

    else
      echo "Erro ao baixar o arquivo do Informativo STF ${number}."
    fi
  done

  echo "Download e conversão concluídos."
}

# Chamada da função para baixar e converter os informativos
ZIPConvertPDF

extrairPDF() {
  local start_number=1000
  local base_url="https://www.stf.jus.br/arquivo/cms/informativoSTF/anexo/Informativo_PDF/Informativo_stf_"
  local output_folder="/var/www/develop/script/stf/informativos"

  # Cria o diretório de saída, se ainda não existir
  mkdir -p "$output_folder"

  for ((number = start_number; ; number++)); do
    local edition_link="${base_url}${number}.pdf"

    # Verifica se o arquivo já existe no diretório de destino
    if [ -e "${output_folder}/Info-${number}.pdf" ]; then
      echo "Informativo STF ${number} já foi baixado. Passando para próxima edição."
      continue
    fi

    # Verifica o link da edição
    local response=$(curl -s -o /dev/null -w "%{http_code}" -k -A "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36" "$edition_link")

    # Imprime o código de resposta HTTP
    echo "Link do Informativo STF ${number} - Resposta HTTP: ${response}"

    if [ $response -ne 200 ]; then
      echo "Link do Informativo STF ${number} não está disponível."
      break
    fi

    echo "Baixando Informativo STF ${number}..."
    mkdir -p "$output_folder"
    curl -s -o "${output_folder}/Info-${number}.pdf" -k -A "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36" "$edition_link"
  done

  echo "Download concluído."
}

# Chamada da função para baixar os informativos
extrairPDF

# ------------------------------------------------------------------------ #
# FIM
# ------------------------------------------------------------------------ #
