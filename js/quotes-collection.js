var quotescollectionInstances = [];

function quotescollectionRefresh(args) {
	if(args.ajaxRefresh && !args.autoRefresh)
		jQuery("#"+args.instanceID+" .nav-next").html(quotescollectionAjax.loading);
	jQuery.ajax({
		type: "POST",
		url: quotescollectionAjax.ajaxUrl,
		data: "action=quotescollection&_ajax_nonce="+quotescollectionAjax.nonce+"&current="+args.currQuoteID+"&char_limit="+args.charLimit+"&tags="+args.tags+"&orderby="+args.orderBy,
		success: function(response) {
			if(response == '-1' || !response) {
				if(args.ajaxRefresh && args.autoRefresh)
					quotescollectionTimer(args);
				else if(args.ajaxRefresh && !args.autoRefresh)
					jQuery("#"+args.instanceID+" .nav-next").html('<a class=\"next-quote-link\" style=\"cursor:pointer;\" onclick=\"quotescollectionRefreshInstance(\''+args.instanceID+'\')\">'+quotescollectionAjax.nextQuote+'</a>');
			}
			else {
				if(args.dynamicFetch) {
					args.dynamicFetch = 0;
				}
				args.currQuoteID = response.quote_id;
				quotescollectionInstances[args.instanceID] = args;
				display = quotescollectionDisplayFormat(response, args);
				jQuery("#"+args.instanceID).hide();
				jQuery("#"+args.instanceID).html(display, args);
				jQuery("#"+args.instanceID).fadeIn('slow');
				if(args.ajaxRefresh && args.autoRefresh)
					quotescollectionTimer(args);
			}
		},
		error: function(xhr, textStatus, errorThrown) {
			console.log(textStatus+' '+xhr.status+': '+errorThrown);
			if(args.ajaxRefresh && !args.autoRefresh) {
				jQuery("#"+args.instanceID+" .nav-next").html('<a class=\"next-quote-link\" style=\"cursor:pointer;\" onclick=\"quotescollectionRefreshInstance(\''+args.instanceID+'\')\">'+quotescollectionAjax.nextQuote+';</a>');
			}
		}	
	});

}

function quotescollectionDisplayFormat(quoteData, args) {
	var display = "";
	var attribution = "";

	display += '<p>' + quoteData.quote + '</p>';
	if( args.showAuthor && quoteData.author && quoteData.author != 'null' ) {
		attribution = '<cite class=\"author\">' + quoteData.author + '</cite>';
	}
	if( args.showSource && quoteData.source && quoteData.source != 'null' ) {
		if(attribution) attribution += ', ';
		attribution += '<cite class=\"source title\">' + quoteData.source + '</cite>';
	}
	if(attribution) {
		display += '<div class=\"attribution\">&mdash;&nbsp;' + attribution + '</div>';
	}
	if(args.ajaxRefresh && !args.autoRefresh)
		display += '<div class=\"navigation\"><div class=\"nav-next\"><a class=\"next-quote-link\" style=\"cursor:pointer;\" onclick=\"quotescollectionRefreshInstance(\''+args.instanceID+'\')\">'+quotescollectionAjax.nextQuote+'</a></div></div>';
	return display;
}

function quotescollectionRefreshInstance(instanceID) {
	quotescollectionRefresh(quotescollectionInstances[instanceID]);
}

function quotescollectionTimer(args) {
	var timeInterval = args.autoRefresh * 1000;
	var autoRefreshMax = Number(quotescollectionAjax.autoRefreshMax);
	var autoRefreshCount = Number(quotescollectionAjax.autoRefreshCount);
	if(!quotescollectionInstances[args.instanceID])
		quotescollectionInstances[args.instanceID] = args;
	if( (autoRefreshMax == 0) || (autoRefreshCount < autoRefreshMax) ) {
		setTimeout("quotescollectionRefreshInstance('"+args.instanceID+"')", timeInterval);
		quotescollectionAjax.autoRefreshCount = ++autoRefreshCount;
	}

}
