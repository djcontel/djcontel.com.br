<?php
    // Variáveis de ambiente
    require_once ".env.php";

    // Biblioteca de funções
    require_once "_lib/functions.php";
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?= DJC_TITULO ?> &mdash; Full Stack Web Developer</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta property="og:title" content="<?= DJC_TITULO ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?= fileVersion("_lib/images/link-preview.jpg", DJC_PATH, DJC_URL) ?>">
    <meta property="og:url" content="<?= DJC_URL ?>">
    <meta property="og:description" content="Full Stack Web Developer">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="<?= DJC_TITULO ?>">
    <link href="<?= fileVersion("_lib/images/favicon.ico") ?>" rel="icon">
    <link href="https://code.cdn.mozilla.net" rel="preconnect">
    <link href="https://code.cdn.mozilla.net/fonts/fira.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css" rel="stylesheet">
    <link href="<?= fileVersion("_lib/css/main.css") ?>" rel="stylesheet">
<?php
    if (DJC_SANDBOX === false) {
        // reCAPTCHA
        if (empty($_SERVER['QUERY_STRING'])) {
            echo '    <script src="https://www.google.com/recaptcha/api.js"></script>' . PHP_EOL;
        }

        // Matomo
?>
    <script>
        var _paq = window._paq = window._paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u="//stats.djcontel.com.br/";
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', '1']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>
<?php
    }
?>
    <script src="<?= fileVersion("_lib/js/main.js", DJC_PATH, DJC_URL) ?>"></script>
  </head>
  <body>
    <main>
      <div class="container">
        <h1><a href="<?= DJC_URL ?>" title="<?= DJC_TITULO ?>"><?= DJC_TITULO ?></a></h1>
        <form name="contato" id="contato" method="post" action="<?= DJC_URL ?>enviar">
          <fieldset>
<?php
    if (empty($_SERVER['QUERY_STRING'])) {
?>
            <input type="text" name="nome" id="nome" placeholder="Nome" autofocus required maxlength="120">
            <input type="text" name="telefone" id="telefone" placeholder="Telefone" required maxlength="15">
            <input type="email" name="email" id="email" placeholder="E-mail" required maxlength="200">
            <textarea name="mensagem" id="mensagem" placeholder="Mensagem" required rows="5"></textarea>
            <div id="recaptcha" class="g-recaptcha" data-sitekey="<?= reCAPTCHA_SITE_KEY ?>" data-callback="verifyCallback"></div>
            <button type="submit" id="enviar">Enviar</button>
<?php
    } else {
        if ($_SERVER['QUERY_STRING'] === 'enviar') {
            $formValido = ['nome', 'telefone', 'email', 'mensagem'];
            $formValido = array_flip($formValido);
            $form = [];

            // Filtra os dados do formulário
            if (count($_POST) > 0) {
                foreach ($_POST as $name => $value) {
                    $recaptcha = stripos($name, 'g-');

                    if ($recaptcha === false) {
                        if ($name !== 'email') {
                            $field = filter_input(INPUT_POST, $name, FILTER_SANITIZE_STRING);

                            if ($field !== false && !is_null($field)) {
                                $form[$name] = $field;
                            }
                        } else {
                            $email = filter_input(INPUT_POST, $name, FILTER_SANITIZE_EMAIL);

                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $form[$name] = $email;
                            }
                        }
                    }
                }
            }

            // Verifica o formulário
            $diff = array_diff_key($formValido, $form);

            // Verifica o reCAPTCHA
            $recaptcha = vrfRecaptcha();

            // Envia o e-mail
            if (count($diff) === 0 && $recaptcha === true) {
                $para     = DJC_EMAIL;
                $assunto  = DJC_TITULO . " - Contato";
                $headers  = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/plain; charset=utf-8\r\n";
                $headers .= "From: " . $_POST['nome'] . " <" . $_POST['email'] . ">\r\n";

                $msg  = DJC_TITULO . " - Contato\n";
                $msg .= date("d/m/Y - H:i:s") . "\n";
                $msg .= "\n";
                $msg .= "- Nome:\n";
                $msg .= $form['nome'] . "\n";
                $msg .= "\n";
                $msg .= "- Telefone:\n";
                $msg .= $form['telefone'] . "\n";
                $msg .= "\n";
                $msg .= "- E-mail:\n";
                $msg .= $form['email'] . "\n";
                $msg .= "\n";
                $msg .= "- Mensagem:\n";
                $msg .= $form['mensagem'];

                if (mail($para, $assunto, $msg, $headers)) {
                    $tipo     = "sucesso";
                    $titulo   = "Mensagem enviada com sucesso!";
                    $mensagem = "Aguarde que entrarei em contato.";
                } else {
                    $tipo     = "erro";
                    $titulo   = "Erro ao enviar mensagem!";
                    $mensagem = "Tente novamente mais tarde.";
                }
            } else {
                $tipo = "alerta";

                if ($recaptcha === false) {
                    $titulo   = "reCAPTCHA inválido!";
                    $mensagem = "Clique no botão voltar e preencha os dados novamente.";
                } else {
                    $titulo   = "Todos os campos são obrigatórios!";
                    $mensagem = "É necessário que você preencha todos os campos.";
                }
            }
?>
        <div class="msg <?= $tipo ?>">
          <p class="titulo"><?= $titulo ?></p>
          <p><?= $mensagem ?></p>
          <button type="button" onclick="javascript:history.back();">Voltar</button>
        </div>
<?php
        } else {
            echo '        <script>window.location.href = "' . DJC_URL . '";</script>' . PHP_EOL;
        }
    }
?>
          </fieldset>
        </form>
        <ul class="stacks">
          <li><i class="devicon-php-plain" title="PHP"></i></li>
          <li><i class="devicon-laravel-plain" title="Laravel"></i></li>
          <li><i class="devicon-html5-plain-wordmark" title="HTML5"></i></li>
          <li><i class="devicon-css3-plain-wordmark" title="CSS3"></i></li>
          <li><i class="devicon-bootstrap-plain" title="Bootstrap"></i></li>
          <li><i class="devicon-javascript-plain" title="JavaScript"></i></li>
          <li><i class="devicon-jquery-plain" title="jQuery"></i></li>
          <li><i class="devicon-mysql-plain" title="MySQL"></i></li>
          <li><i class="devicon-postgresql-plain" title="PostgreSQL"></i></li>
        </ul>
        <h2><strong>Daniel J. Contel</strong><span>Full Stack Web Developer</span></h2>
        <ul class="links">
          <li><a href="https://github.com/djcontel" title="GitHub" target="_blank"><i class="devicon-github-original"></i></a></li>
          <li><a href="https://www.linkedin.com/in/djcontel/" title="LinkedIn" target="_blank"><i class="devicon-linkedin-plain"></i></a></li>
        </ul>
      </div>
    </main>
  </body>
</html>