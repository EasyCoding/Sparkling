<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package sparkling
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

    <h2 class="comments-title">
        <?php
        $commentsCount = get_comments_number();

        if($commentsCount != 0)
        {
            printf(sprintf(esc_html_e( '%d %s to post', 'sparkling' ), $commentsCount, getNumEnding($commentsCount, array(esc_html_e( 'comment', 'sparkling' ), esc_html_e( 'commentary', 'sparkling' ), esc_html_e( 'commentaries', 'sparkling' ))))) ;
        }
        else {
            printf(esc_html_e( 'No commentaries yet.', 'sparkling' ));
        }

        ?>
    </h2>

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) : ?>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'sparkling' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'sparkling' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'sparkling' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-above -->
		<?php endif; // Check for comment navigation. ?>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 60
				) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'sparkling' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'sparkling' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'sparkling' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-below -->
		<?php
		endif; // Check for comment navigation.

	endif; // Check for have_comments().


	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>

		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'sparkling' ); ?></p>
	<?php
	endif;

	//Меняем поля местами
    add_filter('comment_form_fields', 'kama_reorder_comment_fields' );
    function kama_reorder_comment_fields( $fields ){

        $new_fields = array(); // сюда соберем поля в новом порядке

        $myorder = array('author','email','url','comment'); // нужный порядок

        foreach( $myorder as $key ){
            $new_fields[ $key ] = $fields[ $key ];
            unset( $fields[ $key ] );
        }

        // если остались еще какие-то поля добавим их в конец
        if( $fields )
            foreach( $fields as $key => $val )
                $new_fields[ $key ] = $val;

        return $new_fields;
    }

    //Проверим, вошел ли пользователь, что разрешить или запретить размещение ссылок
    $pattern = !is_user_logged_in() ? 'data-pattern=\'([a-zA-Z0-9]+://)?([a-zA-Z0-9_]+:[a-zA-Z0-9_]+@)?([a-zA-Z0-9.-]+\\.[A-Za-z]{2,4})(:[0-9]+)?(/.*)?\'' : "";

    //Меняем содержимое формы
    $comment_args = array(

        'fields' => apply_filters( 'comment_form_after_fields', array(

            'author' => '<div class="comment-inputs">
                            <div class="col-lg-6 col-xs-12" style="padding-left:0px">
                                <div class="form-group">
                                    <label for="inputName" class="control-label">' . esc_html_e( 'Name', 'sparkling' ) . '</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input id="author" name="author" type="text" class="form-control" placeholder="' . esc_html_e( 'Your name', 'sparkling' ).'" value="' . esc_attr( $commenter['comment_author'] ) . '" ' . $aria_req . ' data-error="' . esc_html_e( 'You must enter your name!', 'sparkling' ) . '" required/>
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>',

            'email'  => '<div class="col-lg-6 col-xs-12 email-field"  style="padding-right:0px">
                            <div class="form-group">
                                <label for="inputName" class="control-label">' . esc_html_e( 'Email (hidden)', 'sparkling' ) . '</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                    <input id="email" name="email" type="email" class="form-control" placeholder="Email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" ' . $aria_req . ' data-error="' . esc_html_e( 'Incorrect Email address entered.', 'sparkling' ) .'" required/>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div></div> ' ) ),

        'comment_field' => '<div class="form-group">' .

            '<label for="comment">' . esc_html_e( 'Do not post any hyperlinks, or your comment will be removed as spam.', 'sparkling' ) . '</label>' .

            '<textarea id="comment" name="comment" class="form-control" rows="6" placeholder="' . esc_html_e( 'Enter your comment', 'sparkling' ) . '" aria-required="true" data-error="'.esc_html_e( 'Enter your comment without any hyperlinks.', 'sparkling' ).'" ' . $pattern . ' required></textarea>' .

            '<div class="help-block with-errors"></div>'.

            '</div>',

        'comment_notes_after' => '',
        'class_submit'         => 'submit button_submit_comments btn-lg',
        'submit_button'        => '<div class="row"><input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" /></div>',
    );

    comment_form($comment_args);
	?>

    <script type="text/javascript">

        $('#commentform').validator({
            custom: {
                pattern: function ($el) {
                    var pattern = $el.data('pattern');
                    console.log(!$el.val() || new RegExp(pattern,"g").test($el.val()));
                    return !$el.val() || new RegExp(pattern,"g").test($el.val());
                }
            },
            errors: {
                pattern: "<?php esc_html_e( 'Do not post any hyperlinks!', 'sparkling' ); ?>"
            }
        })
    </script>

</div><!-- #comments -->
