<script>
$(function () {
    $('#resultRegister').change(function(e) {
            var selected = $( "#resultRegister" ).val();
            $.ajax({
                type:'GET',
                url:'/user/register-result/'+selected,
                data:{_token: "{{ csrf_token() }}"
                },
                success: function( msg ) {
                    $('.gridResultAll').hide();
                    $('.gridTimeTable').hide();
                    $('.gridSubjectRegister').html(msg);
                }
            });
            $.ajax({
                type:'GET',
                url:'/user/timetable-result/'+selected,
                data:{_token: "{{ csrf_token() }}"
                },
                success: function( msg ) {
                    $('.time-table').html(msg);
                }
            });
    });
});
</script>
<div class="gridSubjectRegister">
</div>
<div class="time-table ">
</div>

