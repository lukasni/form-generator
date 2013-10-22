$(document).ready(function(){
	$("#password").blur(function() {
		var data = $("form#dblogin").serialize();
		$.getJSON(
			'Database/getDB',
			data,
			function(result) {
				$.each(result, function(key, value) {
					$("select#database").append(
						$("<option></option>")
							.text(value)
							.val(value)
					);
				});
			}
		);
	});

	$("#database").blur(function() {
		var data = $("form#dblogin").serialize();
		$.getJSON(
			'Database/getTbl',
			data,
			function(result) {
				$.each(result, function(key, value) {
					$("select#table").append(
						$("<option></option>")
							.text(value)
							.val(value)
					);
				});
			}
		);
	});
});