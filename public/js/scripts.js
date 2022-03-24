$('#send-form').on('click', function(){
    $('#download-form').submit();
    blockResubmit();
})

$('.list-group-item .form-check-input').on('click', function(){
    
    $('.list-group-item .form-check-input:checkbox').each(function() {
        if(this.checked == true){
            $("#select-all").prop( "checked", true );
            $('.text-select-all').text('Borrar selección');
            return false;
        }else{
            $("#select-all").prop( "checked", false );
            $('.text-select-all').text('Seleccionar selección');
        }
        
                        
    });

});
$("#select-all").on('click', function(){
    if(this.checked) {
        // Iterate each checkbox
        $(':checkbox').each(function() {
            $('.text-select-all').text('Borrar selección');
            this.checked = true;                        
        });
    } else {
        $(':checkbox').each(function() {
            $('.text-select-all').text('Seleccionar todos');
            this.checked = false;                       
        });
    }
})

var downloadTimer;
// Prevents double-submits by waiting for a cookie from the server.
function blockResubmit() {
    
    downloadTimer = window.setInterval(function () {
        
        var token = Cookies.get("downloadToken");
        
        if (token == 1) {
            unblockSubmit();
            location.reload();
        } else if(attempts == 0){
            alert('error en la descarga');
        }

        attempts--;
    }, 1000);
}

function unblockSubmit() {
    window.clearInterval(downloadTimer);
    Cookies.set("downloadToken", 0);
    attempts = 500;
}