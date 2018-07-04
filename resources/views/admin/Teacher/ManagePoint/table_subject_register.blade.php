<script>
$(function () {
    $('#timeRegister').change(function(e) {
            var selected = $( ".id_time_register option:selected" ).val();
            $.ajax({
                type:'GET',
                url:'/admin/teacher/list-subject/'+selected,
                data:{_token: "{{ csrf_token() }}"
                },
                success: function( msg ) {
                    $('.grid-subject-register').hide();
                    $('.timetable-teacher').hide();
                    $('.gridTimeRegister').html(msg);
                }
            });
    });
});
</script>
<div class="gridTimeRegister">
</div>
<div class="time-table ">
</div>
