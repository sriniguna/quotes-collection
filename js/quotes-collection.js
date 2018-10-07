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
	var author = "";
	var source = "";

	display += '<p>' + quoteData.quote + '</p>';
	if( args.showAuthor && quoteData.author && quoteData.author != 'null' ) {
		if( quoteData.author_url ) {
			author = '<a href=\"' + quoteData.author_url + '\">' + quoteData.author + '</a>';
		} else {
			author = quoteData.author;
		}
		attribution = '<cite class=\"author\">' + author + '</cite>';
	}
	if( args.showSource && quoteData.source && quoteData.source != 'null' ) {
		if(attribution) attribution += ', ';
		if( quoteData.source_url ) {
			source = '<a href=\"' + quoteData.source_url + '\">' + quoteData.source + '</a>';
		} else {
			source = quoteData.source;
		}
		attribution += '<cite class=\"source title\">' + source + '</cite>';
	}
	if(attribution) {
		display += args.beforeAttribution + attribution + args.afterAttribution;
	}
	display = args.before + display + args.after;
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
