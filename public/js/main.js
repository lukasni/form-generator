

$(document).ready(function(){

	populateSelect = function(target, requesturl, errmsg) {
		var rdata = target.closest("form").serialize();
		$.ajax({
			url: requesturl,
			dataType: 'json',
			data: rdata,
			success: function( result ) {
				$('.error').addClass('hidden');
				target.empty();
				target.append(
					$("<option disabled></option>")
						.text("Please select...")
						.val("")
				);
				$.each(result, function(key, value) {
					target.append(
						$("<option></option>")
							.text(value)
							.val(value)
					);
				});
			},
			error: function( result ) {
				target.empty();
				target.append(
					$("<option disabled></option>")
						.text("Not loaded")
						.val("")
				);
				$(".error").text(result.responseText);
				$(".error").removeClass('hidden');
			}
		});
	};

	$("#password").blur(function() {
		populateSelect(
			$("select#database"),
			"Database/getDB",
			"Can't load databases. Please check login information"
		);
	});

	$("#database").change(function() {
		if ( $("select#database").val() !== "" && $("select#database").val() !== null ) {
			populateSelect(
				$("select#table"),
				"Database/getTbl",
				"Can't load tables. Please select a valid database"
			);
		}
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