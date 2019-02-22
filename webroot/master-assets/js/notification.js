$(document).ready(function() {
    notifToast = function(heading, content, icon, hide = false, timeout = 5000) {
        if(icon == 'info') {
            var bgColor = '#047edf';
        }
        else if(icon == 'success') {
            bgColor = '#07cdae';
        }
        else if(icon == 'warning') {
            bgColor = '#ffd500';
        }
        else if(icon == 'error') {
            bgColor = '#fe7096';
        }
        
        if(hide == false) {
            var hideAfter = false;
        }
        else {
            hideAfter = timeout;
        }
        
        $.toast({
            heading: heading,
            text: content,
            showHideTransition: 'slide',
            icon: icon,
            bgColor: bgColor,
            hideAfter: hideAfter,
            position: 'bottom-right',
        })
    }
})