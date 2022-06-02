var loader = function() {
    return {
        init: function () {
            $('body').addClass('page-loading');
            $('.page-loader').css('background', 'rgba(255,255,255,0.2').show();
        },
        destroy: function() {
            $('body').removeClass('page-loading');
            $('.page-loader').css('background', 'rgba(255,255,255,1').hide();
        }
    }
}();

$(function(){
    $(".selectpicker").selectpicker();

    $('body').delegate('.btn-action-delete','click',function(e){
        e.preventDefault();
        var $this=$(this);
        confirmModal.find('.modal-title > span').text('Warning : Delete Data');
        confirmModal.find('.modal-body').text('This action will delete this data permanently!');
        confirmModal.modal('show');

        confirmModal.find('.btn-modal-action').click(function(){
            document.location.href = $this.attr('href');
        });
    });

    $('.btn-loading').map(function(){
        var $this=$(this);
        $this.attr('data-initial-text', $this.html());
        $this.attr('data-loading-text', "<i class='fas fa-spinner spinner mr-8'></i> Loading...");
    });

    confirmModal.on('hidden.bs.modal', function (e) {
        var btn = confirmModal.find('.btn-modal-action'),
        initialText = btn.attr("data-initial-text");

        confirmModal.find(".modal-title > span").text("Warning");
        confirmModal.find(".modal-body").text("");
        confirmModal.find(".modal-footer > .btn-modal-action").removeAttr("data-action").removeAttr("data-params");

        btn.html(initialText).removeClass('disabled').prop("disabled",false);
    });

    alertModal.on('hidden.bs.modal', function (e) {
        var btn = alertModal.find('.btn-modal-action'),
        initialText = btn.attr("data-initial-text");

        alertModal.find(".modal-title > span").text("Alert");
        alertModal.find(".modal-body").text("");

        btn.html(initialText).removeClass('disabled').prop("disabled",false);
    });

    $('.required').each(function(){
        $('<span/>',{
            class: 'required-text text-danger',
            text: '*'
        }).appendTo($(this));
    });

    $('body').delegate('.checkbox > input:checkbox','click',function(e){
        var $this=$(this),$checkbox=$this.parents('.checkbox'),$color=$this.attr('data-color'),$parent=$this.attr('data-parent'),$alias=$this.attr('data-alias'),$roles=$this.parents('#role-'+$alias);

        $this.parents('.option').removeClass('bg-'+$color+'-o-30');
        if($this.is(':checked')){
            $this.parents('.option').addClass('bg-'+$color+'-o-30');
            if($('#view-'+$parent).find('input:checkbox').is(':checked')==false){
                $('#view-'+$parent).find('input:checkbox').trigger('click');
            }
            if($this.attr('data-role')!='view'){
                if($('#view-'+$alias).find('input:checkbox').is(':checked')==false){
                    $('#view-'+$alias).find('input:checkbox').prop('checked',true);
                    $('#view-'+$alias).parents('.option').addClass('bg-'+$('#view-'+$alias).find('input').attr('data-color')+'-o-30');
                }
            }
        }
        else{
            if($this.attr('data-role')=='view'){
                $roles.find('.checkbox > input[data-role!=view]').map(function(){
                    if($(this).is(':checked')==true){
                        $(this).prop('checked',false);
                        $(this).parents('.option').removeClass('bg-'+$(this).attr('data-color')+'-o-30');
                    }
                });

                $('.checkbox > input[data-parent='+$alias+']').map(function(){
                    if($(this).is(':checked')==true){
                        $(this).trigger('click');
                    }
                });
            }
        }
    });
});
