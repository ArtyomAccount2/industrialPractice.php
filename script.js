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

    let accordion = document.getElementById('aboutAccordion');
    let buttons = accordion.querySelectorAll('.accordion-button');
    let currentOpen = accordion.querySelector('.accordion-collapse.show') || document.querySelector(buttons[0].getAttribute('data-bs-target'));

    if (!accordion.querySelector('.accordion-collapse.show')) 
    {
        buttons[0].classList.remove('collapsed');
        currentOpen.classList.add('show');
    }

    buttons.forEach(button => {
        button.addEventListener('click', function(e) 
        {
            let target = document.querySelector(button.getAttribute('data-bs-target'));
            
            if (target === currentOpen) 
            {
                if (accordion.querySelectorAll('.accordion-collapse.show').length === 1) 
                {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
            } 
            else 
            {
                if (currentOpen) 
                {
                    let currentButton = accordion.querySelector(`[data-bs-target="#${currentOpen.id}"]`);
                    currentButton.classList.add('collapsed');
                    currentOpen.classList.remove('show');
                }
                currentOpen = target;
            }
        });
    });

    accordion.addEventListener('hide.bs.collapse', function(e) 
    {
        if (accordion.querySelectorAll('.accordion-collapse.show').length <= 1) 
        {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});