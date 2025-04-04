<?php
// Verificar se os arrays de contato e redes sociais não foram definidos no arquivo que incluiu este
if (!isset($contatos) || !isset($redes_sociais)) {
    // Incluir conexão com banco de dados
    $conn = require 'ConfigBD.php';
    
    // Buscar informações de contato
    $query_contato = "SELECT tipo, valor, chave FROM informacoes_contato";
    $result_contato = mysqli_query($conn, $query_contato);
    $contatos = array();
    
    if ($result_contato && mysqli_num_rows($result_contato) > 0) {
        while ($row = mysqli_fetch_assoc($result_contato)) {
            $contatos[$row['chave']] = $row['valor'];
        }
    }
    
    // Buscar redes sociais
    $query_social = "SELECT nome, url, icone FROM redes_sociais WHERE ativo = 1 ORDER BY ordem ASC";
    $result_social = mysqli_query($conn, $query_social);
    $redes_sociais = array();
    
    if ($result_social && mysqli_num_rows($result_social) > 0) {
        while ($row = mysqli_fetch_assoc($result_social)) {
            $redes_sociais[] = $row;
        }
    }
}
?>
<footer id="footer" class="footer position-relative light-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.php" class="logo d-flex align-items-center">
            <span class="sitename">Bento de Jesus Caraça</span>
          </a>
          <div class="footer-contact pt-3">
            <p><a href="https://www.google.com/maps/place/Escola+Profissional+Bento+de+Jesus+Cara%C3%A7a+-+Lisboa/@38.7079433,-9.1447203,17z/data=!3m1!4b1!4m14!1m7!3m6!1s0xd193587275608c1:0x28cf86cb9a4d47ee!2sEscola+Profissional+Bento+de+Jesus+Cara%C3%A7a+-+Lisboa!8m2!3d38.7079392!4d-9.1401069!16s%2Fg%2F11ldk7jpn0!3m5!1s0xd193587275608c1:0x28cf86cb9a4d47ee!8m2!3d38.7079392!4d-9.1401069!16s%2Fg%2F11ldk7jpn0?entry=ttu&g_ep=EgoyMDI1MDMwNC4wIKXMDSoASAFQAw%3D%3D" target="_blank"><?php echo isset($contatos['endereco_linha1']) ? htmlspecialchars($contatos['endereco_linha1']) : 'Rua Vitor Cordon, N1, R/C'; ?></a></p>
            <p><?php echo isset($contatos['endereco_linha2']) ? htmlspecialchars($contatos['endereco_linha2']) : '1200-482 Lisboa'; ?></p>
            <p class="mt-3"><strong>Tel:</strong> <span><?php echo isset($contatos['telefone_principal']) ? htmlspecialchars($contatos['telefone_principal']) : '213 255 326'; ?></span></p>
            <p><strong>Email:</strong> <span><?php echo isset($contatos['email_principal']) ? htmlspecialchars($contatos['email_principal']) : 'geral@epbjc.pt'; ?></span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <?php 
            // Se existirem redes sociais no banco de dados, exibe-as
            if (!empty($redes_sociais)) {
                foreach ($redes_sociais as $rede) {
                    echo '<a href="' . htmlspecialchars($rede['url']) . '" target="_blank"><i class="bi bi-' . htmlspecialchars(str_replace('bi bi-', '', $rede['icone'])) . '"></i></a>';
                }
            } else {
                // Exibe as redes sociais padrão caso não existam no banco
            ?>
                <a href="https://www.facebook.com/EPBJC" target="_blank"><i class="bi bi-facebook"></i></a>
                <a href="https://www.instagram.com/epbjc_escolaprofissional/" target="_blank"><i class="bi bi-instagram"></i></a>
                <a href="https://pt.linkedin.com/school/escola-profissional-bento-de-jesus-cara%C3%A7a/" target="_blank"><i class="bi bi-linkedin"></i></a>
            <?php } ?>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Páginas do site</h4>
          <ul>
            <li><a href="index.php">Início</a></li>
            <li><a href="vida.php">Vida</a></li>
            <li><a href="obras.php">Obras</a></li>
            <li><a href="legado.php">Legado</a></li>
          </ul>
        </div>
        <div class="col-lg-4 col-md-12 footer-newsletter">
          <h4>A nossa Newsletter</h4>
          <p>Subscreve a nossa newsletter e fica atualizado em relação às noticias de Bento de Jesus Caraça !</p>
          <form action="forms/newsletter.php" method="post" class="php-email-form">
            <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscreve"></div>
            <div class="loading">Loading</div>
            <div class="error-message"></div>
            <div class="sent-message">A sua subscrição foi feita com sucesso, Obrigado!!</div>
          </form>
        </div>
      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>©<span> EPBJC - Todos os direitos reservados</span></p>
      <div class="credits">
        Feito por David Farinha e Julismo
      </div>
    </div>

  </footer>