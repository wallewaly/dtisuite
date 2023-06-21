function enablefield(field){
    $("#"+field).attr("disabled", false);
}

function disablefield(field){
    $("#"+field).attr("disabled", true);
}

function resetfield(field){

    var tipocampo = document.getElementById(field).type;
    if(tipocampo == "select-one"){
            if(field != "tipoDevol" && field != "tipoItem"){
                $('#'+field).empty();
                $('#'+field).append(
                    '<option value="" selected disabled>-----</option>'
                );
            } else{
                $('#'+field).val("");
            }
    } 
    
    else if(tipocampo == undefined){

        if (field == "content"){
            $('#'+field).empty();
        }

    }

}

function dpd_control(obj){

    //Enable tipo de item

    if(obj.name != "tipoItem" && obj.name != "employee" && obj.name != "equip"){

        if($("#tipoTermo").val() == '1' || $("#tipoTermo").val() != '' && $("#tipoTermo").val() != '2' || $("#tipoDevol").val() == '1' && $("#tipoDevol").val() != ''){
            resetfield("tipoItem");
            enablefield("tipoItem");
        } else {
            resetfield("tipoItem");
            disablefield("tipoItem");
        }

    }

    //Enable tipo de devolução

    if($("#tipoTermo").val() == '2'){
        enablefield("tipoDevol");
    } else {
        resetfield("tipoDevol");
        disablefield("tipoDevol");
    }
    
    //Enable Colaborador

    if(obj.name != "employee" && obj.name != "equip"){

        if(($("#tipoTermo").val() == '2' && $("#tipoDevol").val() == '2') || $("#tipoItem").val() != null){

            if($("#employee").prop('disabled') == true){
                enablefield("employee");
                get_dd_data("employee");
            }
            else{
                get_dd_data("employee");
                resetfield("content");
            }
        } else {
            resetfield("employee");
            disablefield("employee");
        }   
    }

    //Enable Equipamento

    if(obj.name !="equip"){
        if($("#employee").val() != null && $("#tipoDevol").val() != '2'){
            enablefield("equip");
            get_dd_data("equip");
        } else {
            resetfield("equip");
            disablefield("equip");
        }
    }

    //Enable avançar
    if(($("#tipoTermo").val() == '1' && $("#tipoItem").val() != null && $("#employee").val() != null && $("#equip").val() != null) ||
        ($("#tipoTermo").val() == '2' && $("#tipoDevol").val() == '1' && $("#tipoItem").val() != null && $("#employee").val() != null && $("#equip").val() != null) ||
        ($("#tipoTermo").val() == '2' && $("#tipoDevol").val() == '2' && $("#employee").val() != null)){

        enablefield("gerar");

    } 
    
    else{

        disablefield("gerar");
    }

    resetfield("content");
 
}

function get_dd_data(obj){

    resetfield(obj);

    $.ajax(
        {
            type: "POST",
            url: '../ajax/gt_get_dropdown_data.class.php',
            data: {tipoTermo: $('#tipoTermo').val(),tipoDevol: $('#tipoDevol').val(),tipoItem: $('#tipoItem').val(),employee: $('#employee').val(),dropdown: obj},
            error:console.error,
            success: function (response) {
                $.each(response.data, function(key, value) {
                    $('#'+obj).append(
                        "<option value=" + value[1] +">"+value[0]+"</option>"
                    );
                });
            }
        }
    );
}

function term_gen(){
    let ok = confirm("Foi gerado um registro de termo.");
    if(ok){
        $.ajax(
            {
                type: "POST",
                url: '../ajax/gt_generate_terms.class.php',
                dataType: 'html',
                data: {tipoTermo: $('#tipoTermo').val(),tipoDevol: $('#tipoDevol').val(),tipoItem: $('#tipoItem').val(),employee: $('#employee').val(), equip:$('#equip').val()},
                error:console.error,
                success: function (response) {
                    $('#content').html(response);
                }
            }
        );
    }
   
}

//function con() {
//    $(window).bind("beforeunload",function(event) {
//        return "Você tem alterações não salvas.";
//    });
//}

//con();