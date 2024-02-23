window.onload = function() {
    const djc_url = window.location.origin + window.location.pathname;

    const favicon = document.querySelector('link[rel="icon"]');

    const mediaQueryDark = window.matchMedia('(prefers-color-scheme: dark)');

    if (mediaQueryDark.matches) {
        favicon.setAttribute('href', djc_url + '_lib/images/favicon.dark.ico');
    }

    mediaQueryDark.addEventListener('change', themeChange);

    function themeChange(event) {
        if (event.matches) {
            favicon.setAttribute('href', djc_url + '_lib/images/favicon.dark.ico');
        } else {
            favicon.setAttribute('href', djc_url + '_lib/images/favicon.ico');
        }
    }

    const input = document.getElementById('g-recaptcha-response');

    input.required = true;

    input.addEventListener('invalid', function(e) {
        e.target.setCustomValidity("reCAPTCHA é obrigatório.");
    });

    const form = document.getElementById('contato');

    if (form) {
        verifyCallback = function() {
            input.setCustomValidity("");
        };

        const telefone = document.getElementById('telefone');

        telefone.addEventListener('keypress', (e) => phoneMask(e.target.value));
        telefone.addEventListener('change', (e) => phoneMask(e.target.value));

        const phoneMask = (valor) => {
            valor = valor.replace(/\D/g, "");
            valor = valor.replace(/^(\d{2})(\d)/g, "($1) $2");
            valor = valor.replace(/(\d)(\d{4})$/, "$1-$2");

            telefone.value = valor;
        }

        // Desabilita o botão
        form.onsubmit = function() {
            document.getElementById('enviar').disabled = true;
        };
    }
};