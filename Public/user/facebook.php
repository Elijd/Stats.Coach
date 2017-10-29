
<div class="login-box">
    <div class="login-logo">
        <a href="<?= SITE ?>" style="color: #ffffff; font-size: 150%"><b>Stats</b>.Coach</a>
    </div><!-- /.login-logo -->
    <div class="login-box-body" style="background-color: #ECF0F1; color: #0c0c0c; border: medium">
        <p class="login-box-msg">Sign in to start your session</p>

            <form data-pjax action="<?= SITE ?>login/" method="post">
                <div class="box-body">
                    <div class="callout callout-danger">
                        <h4>I am a danger callout!</h4>

                        <p>There is a problem that we need to fix. A wonderful serenity has taken possession of my entire soul,
                            like these sweet mornings of spring which I enjoy with my whole heart.</p>
                    </div>
                    <div class="callout callout-info">
                        <h4>I am an info callout!</h4>

                        <p>Follow the steps to continue to payment.</p>
                    </div>
                    <div class="callout callout-warning">
                        <h4>I am a warning callout!</h4>

                        <p>This is a yellow callout.</p>
                    </div>
                    <div class="callout callout-success">
                        <h4>I am a success callout!</h4>

                        <p>This is a green callout.</p>
                    </div>
                </div>
            </form>

    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->
<script>  $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });

    $(document).on('submit', 'form[data-pjax]', function (event) {
        $.pjax.submit(event, '#ajax-content')
    });

</script>