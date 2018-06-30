<script>
$(function () {
    $('.id_time_register').change(function(e) {
            var selected = $( ".id_time_register option:selected" ).val();
            $.ajax({
                type:'GET',
                url:'/admin/teacher/list-subject/'+selected,
                data:{_token: "{{ csrf_token() }}"
                },
                success: function( msg ) {
                    $('.gridTimeRegister').html(msg);
                }
            });
        $.ajax({
            type:'GET',
            url:'/admin/teacher/time-table/'+selected,
            data:{_token: "{{ csrf_token() }}"
            },
            success: function( msg ) {
                $('.time-table').html(msg);
            }
        });
    });
});
</script>
<div class="gridTimeRegister">
</div>
<div class="time-table ">
</div>
