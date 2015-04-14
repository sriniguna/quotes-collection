jQuery( document ).ready(function(){
	jQuery( "#quotescollection-dialog" ).dialog({ 
		autoOpen: false,
		modal: true,
		title: quotescollectionConfirm.dialogTitle,
	});
});

jQuery( "span.delete a" ).click(function(event) {
	event.preventDefault();
	var targetUrl = jQuery(this).attr("href");
	jQuery( "#quotescollection-dialog" ).dialog({
		buttons: [
			{
				text: quotescollectionConfirm.buttonYes,
				tabIndex: -1,
				click: function() {
					window.location.href = targetUrl;
				}
			},
			{
				text: quotescollectionConfirm.buttonNo,
				click: function() {
					jQuery(this).dialog("close");
				}
			}
		]
	});
	jQuery("#quotescollection-dialog").html(quotescollectionConfirm.dialogTextSingular);
	jQuery("#quotescollection-dialog").dialog("open");
});

function quotescollectionConfirmBulkDelete() {
	jQuery( "#quotescollection-dialog" ).dialog({
		buttons: [
			{
				text: quotescollectionConfirm.buttonYes,
				tabIndex: -1,
				click: function() {
					jQuery("form#quotescollection").submit();
				}
			},
			{
				text: quotescollectionConfirm.buttonNo,
				focus: true,
				click: function() {
					jQuery(this).dialog("close");
				}
			}
		]
	});
	if( jQuery("form#quotescollection input:checked").length == 0 ) {
		return;
	}
	else if( jQuery("form#quotescollection input:checked").length == 1 ) {
		jQuery("#quotescollection-dialog").html(quotescollectionConfirm.dialogTextSingular);
	}
	else {
		jQuery("#quotescollection-dialog").html(quotescollectionConfirm.dialogTextPlural);
	}
	
	jQuery("#quotescollection-dialog").dialog("open");
}

jQuery( "#doaction" ).click(function(event) {
	if( 'bulk_delete' == jQuery('#bulk-action-selector-top option:selected').val() ) {
		event.preventDefault();
		quotescollectionConfirmBulkDelete();
	}
});
jQuery( "#doaction2" ).click(function(event) {
	if( 'bulk_delete' == jQuery('#bulk-action-selector-bottom option:selected').val() ) {
		event.preventDefault();
		quotescollectionConfirmBulkDelete();
	}
});
