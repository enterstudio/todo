+function ($) {
	// fade out flash messenger
	$('.alert').delay(500).fadeIn('normal', function() {
		$(this).delay(2000).fadeOut();
	});
	
	$(document).on('click', 'img.toggle', function(e){ 
		var _this = $(this);
		var data = this.id;
		var arr = data.split('-');
		var current = _this.attr("src");
		var swap = _this.attr("data-swap");     
		var t = '#todoDesc-' + arr[1];
		var $todo_count = parseInt($('.todo_count').text(), 10);
			
		request = $.ajax({
			url: "/todo/complete/" + arr[1],
			type: "post",
			data: arr[1]
		});
		
		// Callback handler on success
		request.done(function (response, textStatus, jqXHR){
			_this.attr('src', swap).attr("data-swap",current);
			$(t).toggleClass("completed");
			
			// if(_this.attr("src") == '/images/unchecked') {
			// 	$todo_count += 1;
			// } else {
			// 	$todo_count -= 1;
			// }
			// $('.todo_count').text($todo_count);
		});
		
		// Callback handler on failure
		request.fail(function (jqXHR, textStatus, errorThrown){
			// Log the error to the console
			console.error(
				"The following error occurred: " +
				textStatus, errorThrown
			);
		});
		event.preventDefault();
	});
	
	$(document).on('click', 'img.delete', function(e){ 
		var data = this.id;
		var arr = data.split('-');
		var t = '#todoDelete-' + arr[1];
		var $todo_count = parseInt($('.todo_count').text(), 10);
		
		request = $.ajax({
			url: "/todo/delete/" + arr[1],
			type: "post",
			data: arr[1]
		});
		
		// Callback handler on success
		request.done(function (response, textStatus, jqXHR){
			var t = '#todoRow-' + arr[1];
			
			$(t).fadeOut(1000, function() {
				$(t).remove();
			});
			
			$todo_count -= 1;
			$('.todo_count').text($todo_count);
			e.preventDefault();
		});
		
		// Callback handler on failure
		request.fail(function (jqXHR, textStatus, errorThrown){
			// Log the error to the console
			console.error(
				"The following error occurred: " +
				textStatus, errorThrown
			);
		});
		event.preventDefault();
	});   

	$('#todoForm').on('submit', function(e){
		var request;
		
		if (request) {
			request.abort();
		}
		
		var $todo_count = parseInt($('.todo_count').text(), 10);
		var $form = $(this);
		var $inputs = $form.find("input");
		var serializedData = $form.serialize();
		
		$inputs.prop("disabled", true);
		
		request = $.ajax({
			url: "/todo/add",
			type: "post",
			data: serializedData
		});
		
		// Callback handler on success
		request.done(function (response, textStatus, jqXHR){
			$(":input, #todo").val('');
			$('tbody').fadeIn(1000).append(
				"<tr id='todoRow-" + response.todo_id + "'><td><img class='toggle' id='toggleCheck-" + response.todo_id + "' src='images/unchecked.png' data-swap='images/checked.png'></td>" +
				"<td>" + response.todo +
				"</td><td><img id='todoDelete-" + response.todo_id + "' class='pull-right' src='images/delete.png'></td></tr>"
			);
			
			$todo_count += 1;
			$('.todo_count').text($todo_count);
		});
		
		// Callback handler on failure
		request.fail(function (jqXHR, textStatus, errorThrown){
			// Log the error to the console
			console.error(
				"The following error occurred: " +
				textStatus, errorThrown
			);
		});
		
		// Callback handler that will be called regardless
		request.always(function () {
			$inputs.prop("disabled", false);
		});
		event.preventDefault();
	});
	
	$(document).on('click', 'li.filter>a', function(e){ 
		var request;
		var filter = this.href.substr(this.href.lastIndexOf('/') + 1);
		
		if (filter === 'filter') {
			filter = 'all';
		}
		
		request = $.ajax({
			url: "/todo/filter/" + filter,
			type: "post"
		});

		// Callback handler on success
		request.done(function (response, textStatus, jqXHR){
			$('tbody > tr').remove();
			
			$.each(JSON.parse(response), function(k, v) {
				var elements = [];
				$('.todo_count').text(v.todo_count);
				console.log(v.todo_count);
				if(v.todo_status == 0) {
					src = 'images/unchecked.png';
					ds = 'images/checked.png';
				} else {
					src = 'images/checked.png';
					ds = 'images/unchecked.png';
				}
				
				var currentElement = "<tr id='todoRow-" + v.todo_id + "'><td class='tiny'><img class='toggle' id='toggleCheck-" + v.todo_id + "' src=" + src + " data-swap=" + ds + "></td>" +
				"<td>" + v.todo_desc + "</td><td><img id='todoDelete-" + v.todo_id + "' class='pull-right' src='images/delete.png'></td></tr>";
				
				elements.push(currentElement);
				$('tbody').append(elements);
			});
		});
		
		// Callback handler on failure
		request.fail(function (jqXHR, textStatus, errorThrown){
			// Log the error to the console
			console.error(
				"The following error occurred: " +
				textStatus, errorThrown
			);
		});
		event.preventDefault();
	});
	
	//$("#todoTbl").tableEdit({
    //    columnsTr: "2", //null = all columns editable
     //   enableDblClick: true, //enable edit td with dblclick
     //   callback: function(e){
     //       console.log(e.id);
//
 //       }
  //  });
}(jQuery);
