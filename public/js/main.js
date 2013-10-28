$(document).ready(function(){
	$("#password").blur(function() {
		var data = $("form#dblogin").serialize();
		$("select#database").empty();
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
		$("select#table").empty();
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

	$("#code").bind('input propertychange', function() {
		var $this = $(this);
		var delay = 1500;

		clearTimeout($this.data('timer'));
		$this.data('timer', setTimeout(function(){
			$this.removeData('timer');

			var decoded = $("#code").val();
			$("#rendered").html(decoded);
		}, delay));
	});

	$("#download").click(function() {
		$("#codeform").submit();
	});

	$("textarea").keydown(function(e) {
		var $this, end, start;
		if (e.keyCode === 9) {
			start = this.selectionStart;
			end = this.selectionEnd;
			$this = $(this);
			$this.val($this.val().substring(0, start) + "\t" + $this.val().substring(end));
			this.selectionStart = this.selectionEnd = start + 1;
			return false;
		}
	});

	$("form#output").submit(function(e) {
		if (e.preventDefault) {
			e.preventDefault();
		} else {
			e.returnValue = false;
		}
	});
});