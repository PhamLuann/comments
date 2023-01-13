<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"
        integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    function reply(comment_id) {
        document.getElementById('reply-' + comment_id).classList.toggle('hidden')
    }

    function cancelReply(comment_id) {
        document.getElementById('reply-' + comment_id).classList.add('hidden')
    }

    function close_user_like() {
        document.getElementById('view-user-like').remove()
    }

    // view user like
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function viewUserLike(comment_id) {
        $.ajax({
            type: "get",
            cache: false,
            url: "{{route('viewLike')}}",
            data: {
                comment_id: comment_id,
            },
            dataType: "json",
            success: function (data) {
                $('#user-like').html(data);
            },
            error: function (error) {
                alert('error')
            }
        });
    }
    function like_comment(comment_id) {
        $.ajax({
            type: "get",
            url: "{{route('like')}}",
            data: {
                comment_id: comment_id,
            },
            dataType: "json",
            success: function (data){
                if(data.status == "like"){
                    document.getElementById('like-'+comment_id).innerText = 'Unlike'
                    document.getElementById('like-'+comment_id).classList.add('bg-teal-400')
                    document.getElementById('count-like-'+comment_id).innerText = data.count
                }else if(data.status == "unlike"){
                    document.getElementById('like-'+comment_id).innerText = 'Like'
                    document.getElementById('like-'+comment_id).classList.remove('bg-teal-400')
                    document.getElementById('count-like-'+comment_id).innerText = data.count
                }
            },
            error: function (error) {
                alert('error')
            }
        })
    }
</script>