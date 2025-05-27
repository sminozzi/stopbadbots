jQuery(document).ready(function ($) {
    // Inicializa o primeiro Accordion
    $("#accordion1").accordion({
        collapsible: true,
        active: false, // Começa com todas as seções recolhidas
        heightStyle: "content"
    });

    // Inicializa o segundo Accordion
    $("#accordion2").accordion({
        collapsible: true,
        active: false, // Começa com todas as seções recolhidas
        heightStyle: "content"
    });

    $("#accordion3").accordion({
        collapsible: true,
        active: false, // Começa com todas as seções recolhidas
        heightStyle: "content"
    });

    // Adiciona os estilos personalizados
    $("<style>")
        .prop("type", "text/css")
        .html(`
                /* Fundo e cor padrão para os títulos fechados */
                #accordion1 .ui-accordion-header,
                             #accordion2 .ui-accordion-header,
                #accordion3 .ui-accordion-header {
                    background-color: #ffcccc; /* Vermelho claro */
                    color: black !important;  /* Texto em vermelho */
                    border: 1px solid red;
                }

                /* Fundo e cor do título quando o box estiver aberto */
                #accordion1 .ui-accordion-header-active,
                #accordion2 .ui-accordion-header-active,
                #accordion3 .ui-accordion-header-active {
                    background-color: #cc0000; /* Vermelho mais escuro */
                    color: white !important; /* Texto em branco */
                }
            `)
        .appendTo("head"); // Adiciona o estilo ao <head> do documento
});
