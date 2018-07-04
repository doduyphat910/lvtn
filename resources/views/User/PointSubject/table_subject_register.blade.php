<script>
$(function () {
    $('#resultPoint').change(function(e) {
            var selected = $( "#resultPoint" ).val();
            if(selected == 0) {
                location.reload();
            }
            $.ajax({
                type:'GET',
                url:'/user/point-result/'+selected,
                data:{_token: "{{ csrf_token() }}"
                },
                success: function( msg ) {
                    $('.gridPointAll').hide();
                    $('.gridPoint').html(msg);
                }
            });
    });
});
</script>
<div class="gridPoint">
</div>

