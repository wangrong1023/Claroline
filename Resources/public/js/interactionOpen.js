function insertStyle() {

    $('#ujm_exobundle_interactionopentype_interaction').find('div').first().find('label').first().remove();
    $('#ujm_exobundle_interactionopentype_typeopenquestion option[value="2"]').prop('selected', true);

    $('#ujm_exobundle_interactionopentype_typeopenquestion option[value="1"]').attr('disabled', 'disabled');
    $('#ujm_exobundle_interactionopentype_typeopenquestion option[value="3"]').attr('disabled', 'disabled');
    $('#ujm_exobundle_interactionopentype_typeopenquestion option[value="4"]').attr('disabled', 'disabled');
}

function CheckForm() {
    /*if ($("*[id$='_penalty']").length > 0) {
        $("*[id$='_penalty']").val($("*[id$='_penalty']").val().replace(/[-]/, ''));
    }*/
}