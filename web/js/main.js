(function ($) {
	$('.alert').delay(500).fadeIn('normal', function() {
		$(this).delay(2000).fadeOut();
	});
	
	$('.editable').blur(function(){
		var request;
		
		if (request) {
			request.abort();
		}
		
		var todoTxt = $(this).html();
		var id = this.id.split('-')[1];

		request = $.ajax({
			url:  '/todo/edit/' + id,
			type: 'post',
			data: 'todo=' + todoTxt
		});
		
		request.done(function (response, textStatus, jqXHR){
		});
		
		request.fail(function (jqXHR, textStatus, errorThrown){
			console.error('The following error occurred: ' + textStatus, errorThrown);
		});
		event.preventDefault();
	});

	$('#todoTbl').on('click', 'img.toggle', function(e){ 
		var request;
		
		if (request) {
			request.abort();
		}
		
		var id = this.id.split('-')[1];
		var current = $(this).attr('src');
		var swap = $(this).attr('data-swap');
		console.log(current);
		var status = current === '/images/checked.png' ? 0 : 1;
		var desc = '#todoDesc-' + id;
		var toggle_img = '#toggleCheck-' + id;
		var todo_count = parseInt($('.todo_count').text(), 10);
		
		request = $.ajax({
			url: '/todo/complete/' + id,
			type: 'post',
		 	data: 'status=' + status
		});
		
		request.done(function (response, textStatus, jqXHR){
			console.log('current: ' + current + ' changing to ' + swap);
			$(toggle_img).attr('src', swap).attr('data-swap', current);
		 	$(desc).toggleClass('completed');
		});
		
		request.fail(function (jqXHR, textStatus, errorThrown){
			console.error('The following error occurred: ' + textStatus, errorThrown);
		});
		event.preventDefault();
	});
	
	$('#todoTbl').on('click', 'img.delete', function(e){ 
		var request;
		
		if (request) {
			request.abort();
		}
		
		var id = this.id.split('-')[1];
		var t = '#todoRow-' + id;
		var todo_count = parseInt($('.todo_count').text(), 10);
		
		request = $.ajax({
			url: "/todo/delete/" + id,
			type: "post",
			data: "todoId=" + id
		});
		
		request.done(function (response, textStatus, jqXHR){
			$(t).fadeOut(1000, function() {
				$(t).remove();
			});
			
			todo_count -= 1;
			$('.todo_count').text(todo_count);
			e.preventDefault();
		});
		
		request.fail(function (jqXHR, textStatus, errorThrown){
			console.error("The following error occurred: " + textStatus, errorThrown);
		});
		event.preventDefault();
	});   

	$('#todoForm').on('submit', function(e){
		var request;
		
		if (request) {
			request.abort();
		}
		
		var todo_count = parseInt($('.todo_count').text(), 10);
		var inputs = $(this).find("input");
		var serializedData = $(this).serialize();
		
		inputs.prop("disabled", true);
		
		request = $.ajax({
			url: "/todo/add",
			type: "post",
			data: serializedData
		});
		
		request.done(function (response, textStatus, jqXHR){
			$(":input, #todo").val('');
			$('#todoTbl').fadeIn(1000).append(
				"<tr id='todoRow-" + response.todo_id + "'>" +
				"<td><img class='toggle' id='toggleCheck-" + response.todo_id + "' src='images/unchecked.png' data-swap='images/checked.png'></td>" +
				"<td><div id='todoDesc-" + response.todo_id + "' class='editable pull-left' contenteditable>" + response.todo + "</div></td>" +
				"<td class='tiny'><img id='todoDelete-" + response.todo_id + "' class='delete pull-right' src='images/delete.png'></td>" +
				"</tr>"
			);
			
			todo_count += 1;
			$('.todo_count').text(todo_count);
		});
		
		request.fail(function (jqXHR, textStatus, errorThrown){
			console.error("The following error occurred: " + textStatus, errorThrown);
		});
		
		request.always(function () {
			inputs.prop("disabled", false);
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
				
				if(v.todo_status === 0) {
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
})(jQuery);
