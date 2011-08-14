(function($) {
    $(document).ready(function(){
        $('.fsColorpicker').ColorPicker({
            onSubmit: function(hsb, hex, rgb, el) {
                $(el).val('#'+hex);
                $(el).ColorPickerHide();
            },
            onBeforeShow: function () {
                $(this).ColorPickerSetColor(this.value);
            }
        });
        
        $('a.selectAll').click(function(){
            $(this).siblings('select').children('option').attr('selected', 'selected');
            return false;
        });
        
        $('a.selectNone').click(function(){
            $(this).siblings('select').children('option').removeAttr('selected');
            return false;
        });
        
        $('a.selectInverse').click(function(){
            $(this).siblings('select').children('option').each(function(){
                if($(this).is(":selected"))
                    $(this).removeAttr('selected');
                else
                    $(this).attr('selected', 'selected');
            });
            return false;
        });
        
        $('.displayMode.closed .formRow').hide();
        $('.displayMode input[name="type"]').change(function(){
            if(this.checked == true){
                $('.displayMode .formRow').hide();
                $(this).parents('.displayMode').removeClass('closed').children('.formRow').fadeIn();
            }
        });
        
        autoSizing();
        $('#autoSize').change(autoSizing);
    });
    
    function autoSizing(){
        if($('#autoSize').is(':checked'))
            $('.blockSize input.width').attr('disabled','disabled');
        else
            $('.blockSize input.width').removeAttr('disabled');
    }
})(jQuery);