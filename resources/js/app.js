import './bootstrap';

// Wait for the DOM to be fully loaded
$(document).ready(function () {
    // Sidebar Toggle
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        $('#content').toggleClass('active');
        
        // Toggle icon
        $(this).find('i').toggleClass('fa-bars fa-times');
        
        // Add overlay when sidebar is active on mobile
        if ($('#sidebar').hasClass('active')) {
            $('<div class="overlay"></div>').appendTo('body').on('click', function() {
                $('#sidebar').removeClass('active');
                $('#content').removeClass('active');
                $('.overlay').remove();
                $('#sidebarCollapse i').toggleClass('fa-bars fa-times');
            });
        } else {
            $('.overlay').remove();
        }
    });
    
    // Auto-hide alerts after 5 seconds
    window.setTimeout(function() {
        $('.alert').fadeTo(500, 0).slideUp(500, function(){
            $(this).remove();
        });
    }, 5000);
    
    // Enable tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Close dropdown when clicking outside
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
    
    // Add active class to current nav item
    var current = location.pathname;
    $('.sidebar .nav-link').each(function() {
        var $this = $(this);
        if ($this.attr('href') === current) {
            $this.addClass('active');
        }
    });
});

// Initialize popovers
$(function () {
    $('[data-bs-toggle="popover"]').popover();
});
