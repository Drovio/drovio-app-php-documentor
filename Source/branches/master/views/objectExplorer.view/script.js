var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle list visibility
	jq(document).on("click", ".objectExplorer .listContainer .header", function() {
		jq(this).closest(".listContainer").toggleClass("open");
	});
	
	// Refresh explorer
	jq(document).on("objectExplorer.refresh", function() {
		jq("#objectExplorerContainer").trigger("reload");
	});
});