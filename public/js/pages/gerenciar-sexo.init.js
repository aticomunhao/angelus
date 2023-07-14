$(document).ready(function() {
	
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
	});

   	$(".classBtnAlterar").click(function(){

   		$("#divFormAlterar").html('<img src="/images/loader.gif"  width="50px" />');
   		var idSexo = $(this).val();
   		jQuery.ajax({
	                  url: "/cad-sexo/alterar/"+idSexo,
	                  method: 'get',
	                  success: function(result){
	                  	$("#divFormAlterar").html(result);
	                  	$("#title-alterar").html("Alteração de Sexo");
	                  	$(".select2").select2();	                  	
	                  }
	              	});
   	});

});