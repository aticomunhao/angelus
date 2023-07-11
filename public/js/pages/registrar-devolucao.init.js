$(document).ready(function() {
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
	});

  $(document).on("click", "#btncodigobarra", function () {
    var txt = '<label for="txtCodigoBarra" style="font-size:15px; font-weight: bold;">Código de barras</label>'; txt += ' ';
    txt += '<input type="text" id="txtCodigoBarra" name="txtCodigoBarra" style="height:30px; font-size:15px">'; txt += '  ';
    txt += '<button id="btnPesqItem" type="button" class="btn btn-primary"><i class="fa fa-search"></i></button>';
    showModal("divModal", txt);
    $("#btnPesqItem").off().click(function (e) {
      //hideModal();
      window.setTimeout(function () {
        if ($("#txtCodigoBarra").val().trim()) {
          pesqItem($("#txtCodigoBarra").val());
        } else {
          alert("Digite o código");
        }

      }, 100);

    });
    window.setTimeout(function () {
      $("#txtCodigoBarra").trigger("focus").off().keypress(function (e) {
        if (e.which == 13) {
          $("#btnPesqItem").trigger("click");
        }
      });

    }, 300);
  });



  $("#btnbuscaitem").click(function(){

    $("#dialogBuscaItem").dialog({
      width:800,
      height:500
    });
    $("#divBuscaritem").html('<img src="/images/loading02.gif" width="50px"><span>&nbsp;Carregando...</span>');

  $("#divFormAlterar").html('<img src="/images/loader.gif"  width="50px" />');
    var idPagamento = $(this).val();
    jQuery.ajax({
      url: "/substituicao/buscaritem/",
      method: 'get',
      success: function(result){
      	$("#divBuscaritem").html(result);
      	$("#title-alterar").html("Buscar item");
      	$(".select2").select2();

        $('#datatable').DataTable();

        //Buttons examples
        var table = $('#datatable-buttons').DataTable({
          lengthChange: false,
          buttons: ['copy', 'excel', 'pdf', 'colvis']
        });


      }
    });



 	});






  $(document).on("click", ".btnRemoveItem", function(){
    removeItem($("#id_dev").val(), $(this).val());
  });


  $(document).on("click", "#btnAddItem", function () {
    $("#dialogBuscaItem").dialog("close")
    pesqItem($(this).val());
    /*
    var id_item = $(this).val();
    jQuery.ajax({
      url: "/registrar-devolucao/getItem/"+id_item,
      method: 'get',
      success: function(result){
      	$("#DivConfirmaItem").html(result);
        $("#vlr_unit").val($("#vlrVenda").val());
      	$("#dialogBuscaItem").dialog("close")
        //$("#divVendaItens").show();
        //$("#divVendaBotoes").show();

        fCalculaValor();
      	// $("#title-alterar").html("Buscar item");
      	// $(".select2").select2();
      }
    });
    */
  });




  $("#cpf").change(function(){

    if($("#cpf").val()!=null && $("#cpf").val()!=''){
      $("#divAddItem").show();
      //$("#divVendaItens").show();
    }else{
      $("#divAddItem").hide();
    }

  });


  function pesqItem(id_item) {

    showModal();
    jQuery.ajax({
      url: "/registrar-devolucao/getItem/" +id_item,
      method: 'get',
      success: function (result) {
        hideModal();
        $("#DivConfirmaItem").html(result);
        $("#vlr_unit").val($("#vlrVenda").val());
        //$("#dialogBuscaItem").dialog("close")
        //$("#divVendaItens").show();
        //$("#divVendaBotoes").show();

        fCalculaValor();
        // $("#title-alterar").html("Buscar item");
        // $(".select2").select2();
      }
    });
  }



  $(document).on("click", "#btnAddLista", function(){

      if($("#idItem").val() ==null){
        alert("Selecione um item");
        return;
      }else if ($("#qtd_item").val()<=0) {
        alert("Informe a quantidade");
        $("#qtd_item").focus();
        return;
      }

      showModal("divModal", "", "", "", true, pBackdrop="static", pKeyboard=false);




      if ($("#id_dev").val()==null || $("#id_dev").val()=='') {



        var id_pessoa = $("#cpf").val();
        var data_dev = $("#data_dev").val();
        var id_usuario = $("#id_usuario").val();

        jQuery.ajax({
          url: "/registrar-dev/setDev/"+id_pessoa+"/"+data_dev+"/"+id_usuario+"",
          method: 'get',
          success: function(result){
            $("#id_dev").val(result);
            adicionarItem();
            //$("#cpf").val(result);
            // var html = $("#divVenda").html(result);
            // $("#divVenda").html(result);

            // $("#dialogBuscaItem").dialog("close")
            // $("#title-alterar").html("Buscar item");
            // $(".select2").select2();

          }
        });

        }else {

       adicionarItem();

        }
    // if ($("#id_dev").val()==null) {

      // jQuery.ajax({
      //   url: "/registrar-dev-lista/setItemLista/"+id_item,
      //   method: 'get',
      //   success: function(result){
      //     $("#divListaCompras").html(result);
      //     // $("#dialogBuscaItem").dialog("close")
      //     // $("#title-alterar").html("Buscar item");
      //     // $(".select2").select2();
      //   }
      // });
    // }
    });

  $("#qtd_item").keyup(function(evt){
    fCalculaValor();
    });
});






function adicionarItem(){
  var id_item = $("#idItem").val();
  var id_dev = $("#id_dev").val();


  jQuery.ajax({
    url: "/registrar-dev/setItemLista/"+id_item+"/"+id_dev,
    method: 'get',
    success: function(result){
      $("#divListaCompras").html(result);
      hideModal();
      $("#DivConfirmaItem").html("");
      $(document).on("click", "#btnCancDev", function(){
        if ($("#id_dev").val()){
          cancelarVenda($("#id_dev").val());
        }
      });
      $(document).on("click", "#btnConcDev", function(){

        if ($("#id_dev").val()){
          concluirVenda($("#id_dev").val(), $("#vlrTotalDev").text());
        }
      });


      // $("#dialogBuscaItem").dialog("close")
      // $("#title-alterar").html("Buscar item");
      // $(".select2").select2();
    }
  });
}



function fCalculaValor(){
  if ($("#qtd_item").val()>0 && $("#vlr_unit").val()>0){
    $("#vlr_total").val($("#qtd_item").val()*$("#vlr_unit").val());
  }else{
    $("#vlr_total").val("");
  }
  console.log("Calcula valor....");
}




function removeItem(pIdDev, pIdItem){
  console.log("removeItem",pIdVenda, pIdItem);
  showModal();
  jQuery.ajax({
    url: "/registrar-devolucao/removeItemLista/"+pIdItem+"/"+pIdDev+"",
    method: 'get',
    success: function(result){
      $("#divListaCompras").html(result);
      hideModal();
      /*
      $(document).on("click", "#btnCancVenda", function(){
        if ($("#id_dev").val()){
          cancelarVenda($("#id_dev").val());
        }
      });
      */

    }
  });
}

function cancelarVenda(pIdDev){
  console.log("cancelarDev",pIdDev);
  showModal();
  jQuery.ajax({
    url: "/registrar-devolucao/cancelarDev/"+pIdDev+"",
    method: 'get',
    success: function(result){
      $("#divListaCompras").html(result);
      $("#id_dev").val("");
      $("#cpf").val(null).trigger('change');
      //$("#divVendaItens").hide();
      //$("#divVendaBotoes").hide();
      hideModal();
    }
  });
}



function concluirVenda(pIdDev, pVlrTotal){
  console.log("concluirDev",pIdDev, pVlrTotal);
  showModal();
  jQuery.ajax({
    url: "/registrar-devolucao/concluirDev/"+pIdDev+"/"+pVlrTotal+"",
    method: 'get',
    success: function(result){
      $("#divListaCompras").html(result);
      $("#id_dev").val("");
      $("#cpf").val(null).trigger('change');
      //$("#divVendaItens").hide();
      //$("#divVendaBotoes").hide();
      hideModal();
    }
  });
}






function showModal(pId="divModal", pMsg="", pTitle="", pButtons="", pShowX=true, pBackdrop="static", pKeyboard=false){
  var lOptions={
      backdrop: pBackdrop,
      keyboard: true,
      show: true
  }
  $("#" + pId + " .modal-title").html(pTitle);
 if (pMsg) {
     $("#" + pId + " .modal-body").html(pMsg);
 } else {
   $("#" + pId + " .modal-body").html('<img src="/images/loading02.gif" width="50px"><span>&nbsp;Carregando...</span>');
 }
 if (pButtons) {
    $("#" + pId + " .modal-footer").html(pButtons);
 }else{
    $("#" + pId + " .modal-footer").html("");
 }
 if (pShowX){
    $("#"+pId+" button.close").css("display", "inherit");
 }else{
      $("#"+pId+" button.close").css("display", "none");
  }
  //console.log(lOptions);
  $("#" + pId).modal(lOptions);
  //$("#" + pId).modal();
}

function hideModal(pId="divModal"){
 $("#"+pId).modal('hide');
}
