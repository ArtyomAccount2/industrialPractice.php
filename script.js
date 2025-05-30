"use strict";

document.addEventListener('DOMContentLoaded', function() 
{
    if (window.location.hash) 
    {
        let modalElement = document.querySelector(window.location.hash);
        
        if (modalElement && modalElement.classList.contains('modal')) 
        {
            let modal = new bootstrap.Modal(modalElement);
            modal.show();
            
            history.replaceState(null, null, ' ');
        }
    }
});