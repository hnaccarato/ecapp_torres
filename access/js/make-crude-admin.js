function isset(obj) {
    return typeof obj !== typeof undefined ? true : false;
};

function MCAdmin(settings){
	this.config = settings;
};

MCAdmin.prototype.update = function() {
	
	$.ajax({

		url: this.config.url,
		data: this.config,
		method: 'POST',
		beforeSend: function() {
			$('#mc-table').html('<div class="loading"><img src="'+base_url+'/access/images/straight-loader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
		},
		context: this,
		success: this.table
	});

};

MCAdmin.prototype.table = function(data) {
	$('#mc-table').html(data);
    $('td[data-column="' + this.config.order_by +'"]').attr('data-order-type', this.config.order_type);
};

MCAdmin.prototype.limit = function(count) {
	this.config.limit  = count;
};

MCAdmin.prototype.url = function(url) {
	this.config.url  = url;
};

MCAdmin.prototype.order = function(order_by, order_type) {
	this.config.order_by  = order_by;
	this.config.order_type  = order_type;
};

MCAdmin.prototype.search = function(term) {
	this.config.search  = term;
};

MCAdmin.prototype.filters_url = function() {
	return encodeURI('?filter=1&limit='+this.config.limit+'&order_by='+this.config.order_by+'&order_type='+this.config.order_type+'&search='+this.config.search);
};

