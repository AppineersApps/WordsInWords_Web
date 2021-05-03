/** game_level module script */
Project.modules.game_level = {
    init: function() {
        
        valid_more_elements = [];
        
        
    },
    validate: function (){
        $.validator.addMethod('le', function(value, element, param) {
            return this.optional(element) || value < $(param).val();
        }, 'Invalid value');
        $.validator.addMethod('ge', function(value, element, param) {
                return this.optional(element) || value > $(param).val();
        }, 'Invalid value');

        $("#frmaddupdate").validate({
            onfocusout: false,
            ignore:".ignore-valid, .ignore-show-hide",
            rules : {
                "glm_level_name": {
                    "required": true
                },
                "glm_min_word_length": {
                    "required": true,
                    "le": '#glm_max_word_length'
                },
                "glm_max_word_length": {
                    "required": true,
                    "ge": '#glm_min_word_length'
                },
                "glm_max_round": {
                    "required": true
                },
                "glm_round_to_unlock": {
                    "required": true
                },
		    },
            messages : {
		    "glm_level_name": {
		        "required": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE__C35FIELD_C35_FIELD_C46 ,"#FIELD#",js_lang_label.GAME_LEVEL_LEVEL_NAME)
		    },
            "glm_min_word_length": {
                "required": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE__C35FIELD_C35_FIELD_C46 ,"#FIELD#",js_lang_label.GAME_LEVEL_MAX_WORD_LENGTH),
                "le": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE_GAME_LEVEL_MIN_WORD_LENGTH ,"#FIELD#",js_lang_label.GAME_LEVEL_MAX_WORD_LENGTH)
            },
            "glm_max_word_length": {
                "required": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE__C35FIELD_C35_FIELD_C46 ,"#FIELD#",js_lang_label.GAME_LEVEL_MAX_WORD_LENGTH),
                "ge": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE_GAME_LEVEL_MAX_WORD_LENGTH ,"#FIELD#",js_lang_label.GAME_LEVEL_MIN_WORD_LENGTH)
            },
            "glm_level_name": {
		        "required": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE__C35FIELD_C35_FIELD_C46 ,"#FIELD#",js_lang_label.GAME_LEVEL_MAX_ROUND)
		    },
            "glm_level_name": {
		        "required": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE__C35FIELD_C35_FIELD_C46 ,"#FIELD#",js_lang_label.GAME_LEVEL_ROUND_TO_UNLOCK)
		    },
		},
            errorPlacement : function(error, element) {
                switch(element.attr("name")){
                    
                        case 'glm_level_name':
                            $('#'+element.attr('id')+'Err').html(error);
                            break;
                        case 'glm_min_word_length':
                            $('#'+element.attr('id')+'Err').html(error);
                            break;
                        case 'glm_max_word_length':
                            $('#'+element.attr('id')+'Err').html(error);
                            break;
                    default:
                        printErrorMessage(element, valid_more_elements, error);
                        break;
                }
                
            },
            invalidHandler: function(form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {                    
                    validator.errorList[0].element.focus();
                }
            },
            submitHandler: function (form) {
                getAdminFormValidate();
                return false;
            }
        });
        
    },
    callEvents: function() {
        this.validate();
        this.initEvents();
        this.toggleEvents();
        callGoogleMapEvents();
        
    },
    callChilds: function(){
        
        callGoogleMapEvents();
    },
    initEvents: function(elem){
        
    },
    childEvents: function(elem, eleObj){
        
    },
    toggleEvents: function(){
        
    },
    dropdownLayouts:function(elem){
        
    }
}
Project.modules.game_level.init();
