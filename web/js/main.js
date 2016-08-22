(function ($) {
	$('.alert').delay(500).fadeIn('normal', function() {
		$(this).delay(2000).fadeOut();
	});
	
	$('.editable').blur(function(){
		var request;
		var todoTxt = $(this).html();
		var id = this.id.split('-')[1];

		if (request) {
			request.abort();
		}
	
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
		var id = this.id.split('-')[1];
		var current = $(this).attr('src');
		var swap = $(this).attr('data-swap');
		var status = current === '/images/checked.png' ? 0 : 1;
		var desc = '#todoDesc-' + id;
		var toggle_img = '#toggleCheck-' + id;
		var todo_count = parseInt($('.todo_count').text(), 10);
		
		if (request) {
			request.abort();
		}
		
		request = $.ajax({
			url: '/todo/complete/' + id,
			type: 'post',
		 	data: 'status=' + status
		});
		
		request.done(function (response, textStatus, jqXHR){
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
		var id = this.id.split('-')[1];
		var t = '#todoRow-' + id;
		var todo_count = parseInt($('.todo_count').text(), 10);
		
		if (request) {
			request.abort();
		}
		
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
		var todo_count = parseInt($('.todo_count').text(), 10);
		var inputs = $(this).find("input");
		var serializedData = $(this).serialize();
		
		if (request) {
			request.abort();
		}
		
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
		var filter = $(this).attr('data-filter');
		
		if (request) {
			request.abort();
		}	
		
		request = $.ajax({
		 	url: "/todo",
		 	type: "post",
		 	data: "filter=" + filter
		});
		
		
		request.done(function (response, textStatus, jqXHR){
			var elements = [];
			var currentElement;
			
			$('#todoTbl tr').remove();
			$('span.todo_count').html(response.todos.length);
			
			$.each(response, function(key, value) {
				$.each(value, function(k, v) {
					if(v.todo_status === "0") {
						src = 'images/unchecked.png';
						ds = 'images/checked.png';
						desc_class = 'editable pull-left';
					} else {
						src = 'images/checked.png';
						ds = 'images/unchecked.png';
						desc_class = 'completed editable pull-left';
					}
					
					currentElement = "<tr id='todoRow-" + v.todo_id + "'>" +
					"<td><img class='toggle' id='toggleCheck-" + v.todo_id + "' src='" + src + "' data-swap='" + ds + "'></td>" +
					"<td><div id='todoDesc-" + v.todo_id + "' class='" + desc_class + "' contenteditable>" + v.todo_desc + "</div></td>" +
					"<td class='tiny'><img id='todoDelete-" + v.todo_id + "' class='delete pull-right' src='images/delete.png'></td>" +
					"</tr>";
					
					elements.push(currentElement);
				});
			});
			
			$('tbody').append(elements);
		});
		
		request.fail(function (jqXHR, textStatus, errorThrown){
			console.error("The following error occurred: " + textStatus, errorThrown);
		});
		event.preventDefault();
	});
})(jQuery);
