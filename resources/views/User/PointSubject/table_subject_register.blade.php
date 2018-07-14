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
                    $(".grid-refresh").hide();
                    $("table").addClass('table-striped');
                    $("table").addClass('table-bordered');
                    $("th").css("background-color","#3c8dbc");
                    $("th").css("color","white");
                    $("td").addClass('text-center');
                    $("td:nth-last-child(11)").css('text-align','left');
                    $("th").addClass('text-center');
                }
            });
            
    });
});

</script>
<div class="gridPoint" >
</div>

