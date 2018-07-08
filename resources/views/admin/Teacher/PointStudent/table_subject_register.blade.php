<script>
$(function () {
    $('#resultPoint').change(function(e) {
            var selected = $( "#resultPoint" ).val();
            if(selected == 0) {
                location.reload();
            }
            $.ajax({
                type:'GET',
                url:'/admin/teacher/point-result/'+selected+'/'+<?php echo $idUser ?>,
                data:{_token: "{{ csrf_token() }}"
                },
                success: function( msg ) {
                    $('.gridPointAll').hide();
                    $('.gridPoint').html(msg);
                    $(".grid-refresh").hide();
                    $("table").addClass('table-striped');
                    $("table").addClass('table-bordered');
                    $("th").css("background-color","#3c8dbc");
                    $("th").css("color","white");
                }
            });
    });
});

</script>
<div class="gridPoint">
</div>

