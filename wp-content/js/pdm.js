/*!
 * pdm v1.0
 * Copyright 2014, pony_chiang <jxcent@gmail.com>
 * Date:2014-09-25 15:52:28
 */

// reload the verify code
$('#captcha').click(function() {
	var that=$(this);
	that.attr('src',that.attr('rel')+'&r=' + Math.random())
});
