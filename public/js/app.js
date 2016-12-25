$(document).ready(function () {

    //Capturar el evento submit en el formualrio
    $("[name='formCube']").submit(function () {
        //Realizo una petición al servidor para procesar la data
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            beforeSend: function () {
                $('#p2').removeClass('hidden');
            },
            success: function (xhr) {
                showResults(xhr, 1);
            },
            complete: function () {
                $('#p2').addClass('hidden');
            },
            error: function (xhr, status, error) {
                //El codigo 422 es devuelvto cunado se encuentra errores en la data de la petición.
                if (xhr.status == 422) {
                    showResults(xhr.responseJSON, 0);
                }
            }

        });
        //Cancelo el envio tradicional del formulario.
        return false;

    });

    /*
    * @xhr response
    * @type intenger 0 -> errors 1 results
    * */
    function showResults(xhr, type) {
        var color = '#88da99';//Color verde
        var content = '';
        //Si encuentra errores
        if (type == 0) {
            content = 'Errores encontrados!';
            response = xhr.errors;
            color = '#e8a1a1';//Color rojo
        } else {
            content = 'Resultados';
            response = xhr.results;
        }

        //Se crea una lista desordenada
        content += '<ul>';
        $.each(response, function (index, value) {
            content += '<li>' + value + '</li>';
        });
        content += '</ul>'

        //Asigna el resultado en el div de información
        $("#info").css({background: color});
        $("#info").html(content);
        //Si hay errores
        if (type == 0) {
            //Aplicar un efecto sobre el div info
            $("#info").effect("shake");
        }
    }


});