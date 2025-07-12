<?php
// Configurações do e-mail
$destinatario = "seu_email@example.com"; // <-- MUDE PARA O SEU E-MAIL!
$assunto = "Novo Pedido de Levantamento de Dinheiro - Angomoney";

// Verifica se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta e sanitiza os dados do formulário
    $nomeCompleto = htmlspecialchars($_POST['nomeCompleto']);
    $numeroConta = htmlspecialchars($_POST['numeroConta']);
    $valorLevantar = htmlspecialchars($_POST['valorLevantar']);
    $iban = htmlspecialchars($_POST['iban']);
    $nomeBanco = htmlspecialchars($_POST['nomeBanco']);
    $tipoDocumento = htmlspecialchars($_POST['tipoDocumento']);
    $numeroDocumento = htmlspecialchars($_POST['numeroDocumento']);
    $observacoes = htmlspecialchars($_POST['observacoes']);

    // Monta o corpo do e-mail
    $corpoEmail = "Um novo pedido de levantamento de dinheiro foi submetido:\n\n";
    $corpoEmail .= "Nome Completo: " . $nomeCompleto . "\n";
    $corpoEmail .= "Número da Conta Angomoney: " . $numeroConta . "\n";
    $corpoEmail .= "Valor a Levantar: " . $valorLevantar . " Kz\n";
    $corpoEmail .= "IBAN: " . ($iban ? $iban : "Não informado") . "\n";
    $corpoEmail .= "Banco: " . ($nomeBanco ? $nomeBanco : "Não informado") . "\n";
    $corpoEmail .= "Tipo de Documento: " . $tipoDocumento . "\n";
    $corpoEmail .= "Número do Documento: " . $numeroDocumento . "\n";
    $corpoEmail .= "Observações: " . ($observacoes ? $observacoes : "Nenhuma") . "\n\n";
    $corpoEmail .= "---------------------------------------------------\n";
    $corpoEmail .= "Este e-mail foi enviado automaticamente pelo formulário de levantamento Angomoney.";

    // --- Lidar com upload de arquivos (mais complexo) ---
    // IMPORTANTE: O PHP não envia arquivos diretamente no e-mail com a função mail() simples.
    // Ele salva no servidor e você teria que anexá-los ou enviar links.
    // Para simplificar, neste exemplo, vamos apenas mencionar que um arquivo foi enviado.

    $anexosInfo = [];
    $uploadDir = "uploads/"; // Certifique-se que esta pasta exista e tenha permissão de escrita

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Cria a pasta se não existir
    }

    if (!empty($_FILES['comprovanteIdentidadeFrente']['name'])) {
        $fileName = basename($_FILES['comprovanteIdentidadeFrente']['name']);
        $targetFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['comprovanteIdentidadeFrente']['tmp_name'], $targetFilePath)) {
            $anexosInfo[] = "Comprovante Identidade (Frente): " . $fileName . " (Salvo em: " . $targetFilePath . ")";
        } else {
            $anexosInfo[] = "Erro ao fazer upload do Comprovante Identidade (Frente).";
        }
    }
    if (!empty($_FILES['comprovanteIdentidadeVerso']['name'])) {
        $fileName = basename($_FILES['comprovanteIdentidadeVerso']['name']);
        $targetFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['comprovanteIdentidadeVerso']['tmp_name'], $targetFilePath)) {
            $anexosInfo[] = "Comprovante Identidade (Verso): " . $fileName . " (Salvo em: " . $targetFilePath . ")";
        } else {
            $anexosInfo[] = "Erro ao fazer upload do Comprovante Identidade (Verso).";
        }
    }
    if (!empty($_FILES['comprovanteResidencia']['name'])) {
        $fileName = basename($_FILES['comprovanteResidencia']['name']);
        $targetFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['comprovanteResidencia']['tmp_name'], $targetFilePath)) {
            $anexosInfo[] = "Comprovante Residência: " . $fileName . " (Salvo em: " . $targetFilePath . ")";
        } else {
            $anexosInfo[] = "Erro ao fazer upload do Comprovante Residência.";
        }
    }

    if (!empty($anexosInfo)) {
        $corpoEmail .= "\n--- Informações sobre Anexos ---\n";
        $corpoEmail .= implode("\n", $anexosInfo) . "\n";
    }

    // Cabeçalhos do e-mail
    $cabecalhos = "From: webmaster@seu-dominio.com\r\n"; // <-- MUDE PARA UM E-MAIL DO SEU DOMÍNIO!
    $cabecalhos .= "Reply-To: " . $nomeCompleto . " <" . $_POST['nomeCompleto'] . ">\r\n"; // Idealmente, você teria um campo de e-mail no formulário
    $cabecalhos .= "X-Mailer: PHP/" . phpversion();

    // Envia o e-mail
    if (mail($destinatario, $assunto, $corpoEmail, $cabecalhos)) {
        // Redireciona para uma página de sucesso
        header("Location: sucesso.html");
        exit;
    } else {
        // Redireciona para uma página de erro
        header("Location: erro.html");
        exit;
    }
} else {
    // Se o formulário não foi enviado via POST, redireciona para a página inicial ou formulário
    header("Location: formulario.html");
    exit;
}
?>