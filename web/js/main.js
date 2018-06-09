// Validating functions
function requiredValidate(value) {
    if (typeof(value) == 'string') {
        value = value.trim();
    }
    
    if (!value) {
        return 'Обязательное поле!';
    }
}

function emailValidate(value) {
    var valid;
    
    if (typeof(value) != 'string') {
        valid = false;
    } else {
        valid = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/gi.test(value);
    }
    
    if (!valid) {
        return 'Некорректный email!';
    }
}

$(document).ready(function() {
    // Chosen plugin for selects
    $('.chosen-select').chosen();
    
    var $form = $('#registration-form');
    
    // Load geo data
    $form.on('change', 'select', function() {
        var $select = $(this),
            $selectedOption = $select.find('option:selected'),
            $wrapper = $select.parent(),
            action = $selectedOption.data('action') || $select.data('action'),
            target = $select.find('option:selected').data('target') || $select.data('target'),
            $target = $(target),
            $targetSelect = $target.find('select'),
            paramName = $selectedOption.data('param') || $select.attr('name')
            data = {};
        
        // Cleaning errors
        $wrapper.find('.errors').html('');
        
        data[paramName] = $(this).val();
        
        $.get(action, data)
            .done(function(json) {
                if (json.status == 'error') {
                    alert(json.error);
                    return;
                }
                
                if (json.status == 'ok') {
                    var $childrenWrappers = $wrapper.nextAll('.select-wrapper').find('select');
                    $childrenWrappers.chosen('destroy');
                    $childrenWrappers.remove();
                    
                    // Remove second level select if exists
                    var secondTarget = $targetSelect.data('target');
                    
                    if (secondTarget) {
                        var $secondTargetSelect = $(secondTarget).find('select');
                        
                        $secondTargetSelect.chosen('destroy');
                        $secondTargetSelect.remove();
                    }
                    
                    $targetSelect.remove();
                    
                    if (json.html) {
                        $target.html(json.html);
                        
                        $('.chosen-select').chosen();
                    }
                }
            });
    });
    
    // Form validation
    $form.on('submit', function(e) {
        e.preventDefault();
        
        var message,
            errors;
        
        $('.field-wrapper .errors').html('');
        
        // Name validation
        var name = $('#name').val();
        
        message = requiredValidate(name);
        
        if (message) {
            errors = true;
            $('#name-wrapper .errors').append('<span>' + message + '</span>');
        }
        
        // Email validation
        var email = $('#email').val();
        message = requiredValidate(email);
        
        if (message) {
            errors = true;
            $('#email-wrapper .errors').append('<span>' + message + '</span>');
        }
        
        message = emailValidate(email);
        
        if (message) {
            errors = true;
            $('#email-wrapper .errors').append('<span>' + message + '</span>');
        }
        
        // Geo validation
        $('select').each(function() {
            message = requiredValidate($(this).val());
            
            if (message) {
                errors = true;
                $(this).parent().find('.errors').append('<span>' + message + '</span>')
            }
        });
        
        if (errors) {
            return;
        }
        
        $form[0].submit();
    });
    
    
});
