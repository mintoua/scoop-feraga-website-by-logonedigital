var commentForm=$(.comment-form) ;
commentForm.on('submit',function (e) {
    e.preventDefault();
    var $form=$(e.currentTarget);
    var postId=$form.data.('post-id');
    $.ajax({
        url:$form.attr('action'),
        method:'POST',
        data:$form.serialize(),
        success : function (data) {
            var comments=$('comments_'+postId);
            comments.append(data);
            $form.trigger("reset");
        }
    })

})