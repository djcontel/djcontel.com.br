window.onload = function() {
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

        telefone.addEventListener('keypress', (e) => mascaraTelefone(e.target.value));
        telefone.addEventListener('change', (e) => mascaraTelefone(e.target.value));

        const mascaraTelefone = (valor) => {
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